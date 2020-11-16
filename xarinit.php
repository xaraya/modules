<?php
/**
 * Initialization
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Images Module
 * @link http://xaraya.com/index.php/release/152.html
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
*/

/**
 * initialise the images module
 * @return bool true on success
 */
function images_init()
{
    // Load any predefined constants
    xarMod::apiLoad('images', 'user');

    // Check for the required extensions
    // GD is only needed if the user wants to use resize.
    // True or False
    /*$data['gdextension']              = extension_loaded ('gd');*/


    // Set up module variables
    xarModVars::set('images', 'type.graphics-library', _IMAGES_LIBRARY_GD);
    xarModVars::set('images', 'path.derivative-store', 'Put a real directory in here...!');
    xarModVars::set('images', 'view.itemsperpage', 200);
    xarModVars::set('images', 'file.cache-expire', 60);
    xarModVars::set('images', 'file.imagemagick', '');

    /*
        xarMasks::register('ViewUploads',  'All','images','Image','All','ACCESS_READ');
        xarMasks::register('AddUploads',   'All','images','Image','All','ACCESS_ADD');
        xarMasks::register('EditUploads',  'All','images','Image','All','ACCESS_EDIT');
        xarMasks::register('DeleteUploads','All','images','Image','All','ACCESS_DELETE');
    */
    xarMasks::register('AdminImages', 'All', 'images', 'Image', 'All', 'ACCESS_ADMIN');

    if (!xarModHooks::register('item', 'transform', 'API', 'images', 'user', 'transformhook')) {
        $msg = xarML('Could not register hook.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    // Register the tag
    $imageAttributes = array(new xarTemplateAttribute('src', XAR_TPL_REQUIRED | XAR_TPL_STRING),
                             new xarTemplateAttribute('height', XAR_TPL_OPTIONAL | XAR_TPL_STRING),
                             new xarTemplateAttribute('width', XAR_TPL_OPTIONAL | XAR_TPL_STRING),
                             new xarTemplateAttribute('constrain', XAR_TPL_OPTIONAL | XAR_TPL_STRING),
                             new xarTemplateAttribute('label', XAR_TPL_REQUIRED | XAR_TPL_STRING));
    xarTplRegisterTag('images', 'image-resize', $imageAttributes, 'images_userapi_handle_image_tag');

    // Initialisation successful
    return true;
}

/**
 * upgrade the images module from an old version
 * @param string oldversion
 * @return bool true on success
 */
function images_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '1.0.0':
            // Code to upgrade from version 1.0.0 goes here
            $thumbsdir = xarModVars::get('images', 'path.derivative-store');
            if (!empty($thumbsdir) && is_dir($thumbsdir)) {
                xarModVars::set('images', 'upgrade-1.0.0', 1);
                // remove all old-style derivatives
            /* skip this - too risky depending on site config
                $images = xarMod::apiFunc('images','admin','getderivatives');
                if (!empty($images)) {
                    foreach ($images as $image) {
                        @unlink($image['fileLocation']);
                    }
                }
            */
            }
            // Fall through to next upgrade

            // no break
        case '1.1.0':
        
        case '1.1.1': //current version
            break;
    }

    return true;
}

/**
 * delete the images module
 * @param none
 * @return bool
 */
function images_delete()
{
    // Unregister template tag
    xarTplUnregisterTag('image-resize');
    // Remove mask
    xarMasks::unregister('AdminImages');
    // Unregister the hook
    xarModHooks::unregister('item', 'transform', 'API', 'images', 'user', 'transformhook');
    // Delete module variables
    xarModVars::delete_all('images');
    // Deletion successful
    return true;
}
