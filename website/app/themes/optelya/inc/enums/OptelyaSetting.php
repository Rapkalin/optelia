<?php

/**
 * Fields of the site settings page (Settings → Configuration). Read by the
 * header, the footer, the contact section and the seed: one enum keeps their
 * names in step with acf-json/group_optelya_settings.json.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

enum OptelyaSetting: string
{
    case Tagline = 'tagline';
    case HeaderCta = 'header_cta';
    case PhoneLabel = 'phone_label';
    case PhoneNumber = 'phone_number';
    case ContactTitle = 'contact_title';
    case Address = 'address';
}
