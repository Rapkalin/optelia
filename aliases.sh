# Raccourcis Docker pour Optelya.
# Charger dans le shell courant :  source aliases.sh
#
# Les identifiants sont ceux du .env.example (dev). À ajuster si le .env local
# utilise d'autres valeurs pour DB_NAME / DB_USER / DB_PASSWORD.

# ─────────────────────────────────────────────────────────────
# Application
# ─────────────────────────────────────────────────────────────
alias dphp='docker compose exec php'
alias dcomposer='docker compose exec php composer'
alias dwp='docker compose exec php wp --allow-root'
alias dlogs='docker compose logs -f php'

# Qualité (dans le conteneur : même version de PHP que la CI)
alias dcheck='docker compose exec php composer check'
alias dtest='docker compose exec php composer test'

# Front (profil "tools", conteneur jetable)
alias dnpm='docker compose run --rm node npm'

# ─────────────────────────────────────────────────────────────
# Base de données (MySQL 8.4)
#
# Ces fonctions REMPLACENT `wp db query` / `wp db export` / `wp db import`,
# inopérantes dans le conteneur php — voir readme/docker.md.
#
# Les identifiants ne sont écrits nulle part ici : ce sont les variables
# MYSQL_* du conteneur `db`, que docker-compose.yml alimente depuis le .env.
# Les guillemets simples les font développer DANS le conteneur.
#
# db-drop / db-create passent par `root` : l'utilisateur applicatif n'a de
# privilèges que SUR sa base, pas celui d'en créer une.
# ─────────────────────────────────────────────────────────────
DB_AS_USER='MYSQL_PWD="$MYSQL_PASSWORD" mysql -u "$MYSQL_USER" -h db "$MYSQL_DATABASE"'
DB_AS_ROOT='MYSQL_PWD="$MYSQL_ROOT_PASSWORD"'

db-cli() { docker compose exec db sh -c "$DB_AS_USER"; }
db-query() { docker compose exec -T db sh -c "$DB_AS_USER"' -e "$1"' sh "$1"; }
db-import() { docker compose exec -T db sh -c "$DB_AS_USER" < "$1"; }
db-export() { docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" mysqldump --no-tablespaces -u "$MYSQL_USER" "$MYSQL_DATABASE"' > "$1"; }
db-drop() { docker compose exec -T db sh -c "$DB_AS_ROOT"' mysqladmin -h db -u root -f drop "$MYSQL_DATABASE"'; }
db-create() { docker compose exec -T db sh -c "$DB_AS_ROOT"' mysql -u root -h db -e "CREATE DATABASE IF NOT EXISTS \`$MYSQL_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"'; }

# Exemples :
#   db-export dump.sql
#   db-drop && db-create && db-import dump.sql
#   db-query "SELECT option_value FROM wp_options WHERE option_name='home'"
#   db-cli                       # shell MySQL interactif
#   dwp search-replace 'http://optelya.local' 'http://localhost:8040' --all-tables
