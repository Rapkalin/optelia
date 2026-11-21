<?php

/**
 * Fields of the catalogue settings (Solutions → Réglages, Industries →
 * Réglages): one enum keeps their names in step with
 * acf-json/group_optelya_{solution,industry}_settings.json
 * (tests/Unit/CatalogSettingTest.php). A heading, a list head or a band is a
 * cloned part (acf-json/group_optelya_part_*.json), read as one value.
 *
 * @package optelya
 */

if (! defined('ABSPATH')) {
    exit;
}

enum OptelyaCatalogSetting: string
{
    // Solution and vendor pages.
    case ReadersTitle = 'readers_title';
    case Readers = 'readers';
    case ProofsHeading = 'proofs_heading';
    case DemoHeading = 'demo_heading';

    // Solution page.
    case SolutionProofsLink = 'solution_proofs_link';
    case SolutionBenefits = 'solution_benefits';
    case SolutionSectorsLabel = 'solution_sectors_label';
    case SolutionOthers = 'solution_others';
    case SolutionDetails = 'solution_details';
    case SolutionDatasheetLink = 'solution_datasheet_link';
    case SolutionContactTitle = 'solution_contact_title';

    // Solutions list.
    case SolutionsHead = 'solutions_head';
    case SolutionsBand = 'solutions_band';
    case SolutionsFilterLabel = 'solutions_filter_label';
    case SolutionsAll = 'solutions_all';
    case SolutionsVendors = 'solutions_vendors';
    case SolutionsContactTitle = 'solutions_contact_title';

    // Vendor page.
    case VendorToolsLink = 'vendor_tools_link';
    case VendorTools = 'vendor_tools';
    case VendorContactTitle = 'vendor_contact_title';

    // Industries list.
    case IndustriesHead = 'industries_head';
    case IndustriesChoose = 'industries_choose';
    case IndustriesBand = 'industries_band';
    case IndustriesContactTitle = 'industries_contact_title';

    // Industry page.
    case IndustryStudiesLink = 'industry_studies_link';
    case IndustryNearLink = 'industry_near_link';
    case IndustryIssues = 'industry_issues';
    case IndustryFigures = 'industry_figures';
    case IndustryStudies = 'industry_studies';
    case IndustryContactTitle = 'industry_contact_title';

    /**
     * Head of a content type's list, if it has one.
     */
    public static function listHead(OptelyaPostType $type): ?self
    {
        return match ($type) {
            OptelyaPostType::Solution => self::SolutionsHead,
            OptelyaPostType::Industry => self::IndustriesHead,
            default => null,
        };
    }
}
