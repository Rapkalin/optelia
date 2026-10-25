#!/usr/bin/env bash
# =============================================================================
# Initialisation WordPress (exécutée par l'entrypoint du conteneur PHP).
#
# IDEMPOTENT : si WordPress est déjà installé, l'installation initiale est
# sautée. C'est ce qui permet d'importer un dump existant sans que le script
# n'écrase quoi que ce soit au redémarrage suivant.
#
# wp cible website/wordpress-core grâce à wp-cli.yml (path:) à la racine.
# Le script tourne en root dans le conteneur -> toutes les commandes wp ont
# besoin de --allow-root.
# =============================================================================
set -e

APP_DIR="/var/www/html"
cd "$APP_DIR"

# Le .env vit dans le docroot sur le serveur (website/.env, lien vers
# shared/.env) et à la racine du dépôt en local.
if [ -f "$APP_DIR/website/.env" ]; then
    ENV_FILE="$APP_DIR/website/.env"
else
    ENV_FILE="$APP_DIR/.env"
fi

echo "==> [init] Initialisation WordPress"

if [ -f "$ENV_FILE" ]; then
    # set -a : toute variable affectée est exportée automatiquement.
    set -a
    # shellcheck disable=SC1090
    . "$ENV_FILE"
    set +a
fi

# -----------------------------------------------------------------------------
# 1) Générer les sels encore à 'generateme' dans le .env.
#    Chaînes alphanumériques de 64 caractères : sans | ni /, donc sûres pour sed.
# -----------------------------------------------------------------------------
SALT_KEYS="AUTH_KEY SECURE_AUTH_KEY LOGGED_IN_KEY NONCE_KEY AUTH_SALT SECURE_AUTH_SALT LOGGED_IN_SALT NONCE_SALT"

if [ -f "$ENV_FILE" ]; then
    for KEY in $SALT_KEYS; do
        if grep -q "^${KEY}='generateme'" "$ENV_FILE"; then
            SALT="$(tr -dc 'A-Za-z0-9' </dev/urandom | head -c 64)"
            sed -i "s|^${KEY}='generateme'|${KEY}='${SALT}'|" "$ENV_FILE"
            echo "==> [init] Sel généré pour ${KEY}"
        fi
    done
else
    echo "!!! [init] .env introuvable : génération des sels ignorée."
fi

# -----------------------------------------------------------------------------
# 2) Droits d'écriture. uploads/ est un volume nommé (donc réellement chownable) ;
#    les autres sont sur le bind mount, où le chown peut échouer selon l'hôte —
#    on tolère l'échec plutôt que de bloquer le démarrage.
# -----------------------------------------------------------------------------
echo "==> [init] Droits d'écriture sur les dossiers de contenu"
for DIR in website/app/uploads website/app/cache website/app/languages; do
    mkdir -p "$APP_DIR/$DIR"
    chown -R www-data:www-data "$APP_DIR/$DIR" 2>/dev/null || true
    chmod -R u+rwX,g+rwX "$APP_DIR/$DIR" 2>/dev/null || true
done

# -----------------------------------------------------------------------------
# 3) Installation initiale : UNIQUEMENT si WordPress n'est pas encore installé.
# -----------------------------------------------------------------------------
if wp core is-installed --allow-root 2>/dev/null; then
    echo "==> [init] WordPress déjà installé : installation initiale sautée."
else
    echo "==> [init] Installation de WordPress sur ${WP_HOME}"
    wp core install \
        --url="$WP_HOME" \
        --title="$WP_TITLE" \
        --admin_user="$WP_ADMIN_USER" \
        --admin_password="$WP_ADMIN_PASSWORD" \
        --admin_email="$WP_ADMIN_EMAIL" \
        --skip-email \
        --allow-root

    # `wp core install` écrit la MÊME valeur dans home et siteurl, ce qui laisse
    # un `home` faux (suffixé /wordpress-core). Sans effet au runtime — les
    # constantes WP_HOME/WP_SITEURL priment — mais trompeur dès qu'on inspecte la
    # base, et faux si le dump est réimporté ailleurs sans ces constantes.
    # On écrit via $wpdb : `wp option update` serait un no-op, update_option()
    # comparant la nouvelle valeur à celle renvoyée par la constante.
    wp eval '
        global $wpdb;
        $wpdb->update($wpdb->options, ["option_value" => WP_HOME], ["option_name" => "home"]);
        $wpdb->update($wpdb->options, ["option_value" => WP_SITEURL], ["option_name" => "siteurl"]);
    ' --allow-root

    echo "==> [init] Permaliens"
    wp rewrite structure '/%postname%/' --hard --allow-root
    wp rewrite flush --hard --allow-root
fi

# -----------------------------------------------------------------------------
# 4) Thème : activé à chaque démarrage (idempotent). Son nom est lu dans la
#    configuration (WP_DEFAULT_THEME) pour ne pas être écrit une seconde fois.
# -----------------------------------------------------------------------------
THEME="$(wp eval 'echo WP_DEFAULT_THEME;' --allow-root)"
if wp theme is-installed "$THEME" --allow-root 2>/dev/null; then
    wp theme activate "$THEME" --allow-root >/dev/null 2>&1 || true
    echo "==> [init] Thème '${THEME}' actif."
else
    echo "!!! [init] Thème '${THEME}' introuvable (ignoré)."
fi

# -----------------------------------------------------------------------------
# 5) Plugins sous licence (ACF Pro) : déposés à la main dans shared/plugins/,
#    jamais dans le dépôt. Reliés puis activés à chaque démarrage, comme le fait
#    le déploiement sur le serveur. Lien RELATIF : il reste valide sur l'hôte
#    (IDE) comme dans le conteneur.
# -----------------------------------------------------------------------------
mkdir -p "$APP_DIR/website/app/plugins"
for PLUGIN_DIR in "$APP_DIR"/shared/plugins/*/; do
    [ -d "$PLUGIN_DIR" ] || continue
    PLUGIN="$(basename "$PLUGIN_DIR")"
    ln -nsf "../../../shared/plugins/$PLUGIN" "$APP_DIR/website/app/plugins/$PLUGIN"
    wp plugin activate "$PLUGIN" --allow-root >/dev/null 2>&1 \
        && echo "==> [init] Plugin '${PLUGIN}' relié et actif." \
        || echo "!!! [init] Activation de '${PLUGIN}' échouée (ignoré)."
done

# -----------------------------------------------------------------------------
# 6) Contenu initial (accueil, réglages, menus) : APRÈS l'activation d'ACF, dont
#    il a besoin. Idempotent — voir inc/seed.php du thème.
# -----------------------------------------------------------------------------
wp eval 'foreach (optelya_seed() as $line) { echo "==> [seed] {$line}", PHP_EOL; }' --allow-root \
    || echo "!!! [init] Amorçage du contenu échoué (ignoré)."

# -----------------------------------------------------------------------------
# 7) Langue du site : fr_FR à chaque démarrage. Les fichiers de langue du cœur
#    sont VERSIONNÉS (website/app/languages) et partent avec la release : le
#    site est en français même sur un serveur où WP-CLI ne tourne pas.
# -----------------------------------------------------------------------------
echo "==> [init] Langue du site : fr_FR"
if ! wp language core is-installed fr_FR --allow-root 2>/dev/null; then
    wp language core install fr_FR --allow-root >/dev/null 2>&1 \
        || echo "!!! [init] Installation de fr_FR échouée (ignoré)."
fi
wp site switch-language fr_FR --allow-root >/dev/null 2>&1 \
    || echo "!!! [init] Réglage de la langue échoué (ignoré)."

echo "==> [init] Initialisation terminée."
echo "    Front : ${WP_HOME}"
echo "    Admin : ${WP_HOME}/wordpress-core/wp-admin"
