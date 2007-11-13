<?php

/**
 * Fetch the global parameters and settings for the module.
 * @param name string The name of an individual parameter to return
 * @param names string or array List of parameters to return as a numeric keyed array
 * @param knames string or array List of paremeters to return as a name keyed array
 *
 * @todo Some of these parameters may have user overrides, and may
 * ultimately be stored as module variables.
 * - Use 'names' with list() to assign values to variables.
 * - Use 'knames' with extract() to create variables.
 */

function mag_userapi_params($args)
{
    static $params = array();

    extract($args);

    if (empty($params)) {
        // Initialise the parameter list.
        $params = array();

        // The maximum number of tags that can be added to an article
        // This may be handled a module at a later date
        $params['maxtags'] = 10;

        // Standard itemtypes.
        $params['itemtype_mags'] = 1;
        $params['itemtype_issues'] = 2;
        $params['itemtype_series'] = 3;
        $params['itemtype_articles'] = 4;
        $params['itemtype_authors'] = 5;
        $params['itemtype_articles_authors'] = 6;

        // Set the module
        $module = 'mag';
        $params['module'] = $module;
        $params['modid'] = xarModGetIDFromName($module);


        
        // Various image paths.
        //
        // Substitution variables used in paths are:
        //  {base_image_vpath} - as above
        //  {mag_ref} - magazine reference
        //  {mag_logo} - magazine logo (full file-path as stored)
        //  {issue_ref} - issue reference
        //  {issue_cover} - issue cover image (full file-path as stored)
        // etc.

        // Note that these paths will completely override the paths stored in the images.
        // You can take these paths out, and just use the image path and name as stored.
  
        // The base path for most, if not all, images.
        // This is relative to the site entry point (index.php)
        $params['base_image_vpath'] = 'modules/' . $module . '/xarimages';

        // Path of a magazine logo image.
        // Available substitution vars: {base_image_vpath} {mag_ref} {mag_logo}
        $params['image_mag_logo_vpath'] = '{base_image_vpath}/{mag_ref}/logo/{mag_logo}';

        // Path of the issue front cover.
        // Available substitution vars: {base_image_vpath} {mag_ref} {issue_ref} {issue_cover}
        $params['image_issue_cover_vpath'] = '{base_image_vpath}/{mag_ref}/issues/{issue_ref}/cover/{issue_cover_filename}';
        $params['image_issue_cover_icon_vpath'] = '{base_image_vpath}/{mag_ref}/issues/{issue_ref}/cover/{issue_cover_filename}';

        // Path for the article main image.
        $params['image_article_main_vpath'] = '{base_image_vpath}/{mag_ref}/issues/{issue_ref}/articles/{image1_filename}';
        // Or just to use the full image and path as stored.
        //$params['image_article_main_vpath'] = '{image1}';

        // Path for an article embedded images, and its associated display thumbnail.
        // The thumbnails are assumed to be in the same directory as the full-size images,
        // but they need not be; adapt the thumbnail path as required.
        $params['image_article_embedded_vpath'] = '{base_image_vpath}/{mag_ref}/issues/{issue_ref}/articles/{image_filename}';
        $params['image_article_embedded_thumb_vpath'] = '{image_filedir}/{image_filebody}.thumb{image_fileext}';

        // Image classes that trigger the thumbnail code.
        // Add any of these classes to an article image, and they will automatically get
        // replaced by a thumb-nail, with a pop-up full sized image (using jQuery thickbox if available).
        $params['image_article_thumb_classes'] = array('thumbnailed', 'thumbnailed-left', 'thumbnailed-right', 'thumbnailed-centre');
        
        // Photos for the authors.
        // We put an '-icon80' suffixed thumbnail in the same directory as the author main photo.
        $params['image_author_photo_vpath'] = '{base_image_vpath}/authors/{photo_filename}';
        $params['image_author_icon_vpath'] = '{base_image_vpath}/authors/{photo_filebody}.icon80{photo_fileext}';


        
        // Determine whether fulltext search is supported.
        // The mode can be 'TRUE', 'FALSE' or 'AUTO'.
        $fulltext_mode = xarModGetVar($module, 'fulltext_search');
        $params['fulltext_mode'] = (empty($fulltext_mode) ? false : true);
        $params['fulltext_columns_articles'] = array('', '');

        // Default sort orders.
        $params['sort_default_mags'] = 'title ASC';
        $params['sort_default_issues'] = 'number DESC, pubdate DESC, ref DESC';
        $params['sort_default_articles'] = 'page ASC';
        $params['sort_default_articles_toc'] = 'page ASC';
        $params['sort_default_series'] = 'display_order ASC';
        
        // Numbers of items when displaying magazines and issues.
        $params['default_numitems_mags'] = 20;
        $params['max_numitems_mags'] = $params['default_numitems_mags'] * 5;
        
        $params['default_numitems_issues'] = 24;
        $params['max_numitems_issues'] = $params['default_numitems_issues'] * 5;

        $params['default_numitems_authors'] = 20;
        $params['max_numitems_authors'] = $params['default_numitems_authors'];

        // Maximum number of articles to show on the author profile page.
        $params['max_author_articles_profile_page'] = 40;

        // Default number of author articles to select.
        $params['default_author_articles'] = 100;

        // List of month names (MLS-sensitive)
        $params['month_names'] = array();
        $locale = xarMLSLoadLocaleData();
        for($i=1; $i<=12; $i+=1) {
            $params['month_names'][$i] = $locale['/dateSymbols/months/' .$i. '/full'];
        }

        // Optional pager template name, stored in base/xartemplates/includes/pager-{name}.xt (or the theme)
        $params['pager_template_name'] = 'default';

        // Lists of restricted fields when selecting articles, to help performance.
        $params['article_fieldset_toc'] = array('aid','issue_id','series_id','title','subtitle','status','ref','page','premium','image1','pubdate','hitcount');

        // Article display template search path.
        // Change the order as required.
        // IDEA: Would be great to handle core Xaraya search paths like this too, making it easier to customise.
        // **NOT USED**
        $params['article_template_path'] = '{theme_templates}/user-article-{style}-{mag_ref}.xt;{theme_templates}/user-article-{style}.xt;'
            . '{module_templates}/user-article-{style}.xd;{theme_templates}/user-article.xt;{module_templates}/user-article.xd';
        $params['article_template_extra'] = '{style}-{mag_ref};{style};;';

        // List of IP addresses that bypass any premium restrictions.
        // Used mainly for administration and for allowing search spiders
        // to scan the full details of the articles.
        $params['premium_policy_bypass_ip'] = 'localhost,192.168.1.1';

        // List of premium flags and their values.
        // These are shared across three DD properties and the MagArt privilege configuration.
        $params['premium_flags'] = array(
            'OPEN' => xarML('Open (unrestricted)'),
            'SAMPLE' => xarML('Sample (semi-restricted)'),
            'PREMIUM' => xarML('Premium (restricted)'),
        );
    }


    if (!empty($name)) {
        // Return a single parameter
        if (isset($params[$name])) {
            $return = $params[$name];
        } else {
            $return = NULL;
        }
    } elseif (!empty($names) || !empty($knames)) {
        // Multiple names as a comma-separated list
        $return = array();

        if (!empty($knames)) $names = $knames;

        if (is_string($names)) $names = explode(',', $names);

        // Loop for each name and look up its value.
        foreach($names as $name) {
            // Trim in case there are spaces in the list.
            $name = trim($name);

            if (!empty($knames)) {
                $return[$name] = mag_userapi_params(array('name' => $name));
            } else {
                $return[] = mag_userapi_params(array('name' => $name));
            }
        }
    } else {
        // Return all parameters
        $return = $params;
    }

    return $return;
}

?>