<?php

/**
 * Fields of the site settings page (Settings → Configuration). Read by the
 * header, the footer, the SEO tags and the seed: one enum keeps their
 * names in step with acf-json/group_optelya_settings.json.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

enum OptelyaSetting: string
{
    case Logo = 'logo';
    case HeaderBackground = 'header_background';
    case Tagline = 'tagline';
    case MetaDescription = 'meta_description';
    case SeoTitle = 'seo_title';
    case ShareImage = 'share_image';
    case HeaderCta = 'header_cta';
    case ContactRecipient = 'contact_recipient';
    case ContactTitle = 'contact_title';
    case Address = 'address';
}
