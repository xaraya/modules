<?php
/**
 * File: $Id$
 *
 * init file for installing/upgrading Images module
 *
 * @package modules
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage images
 * @author Carl P. Corliss <carl.corliss@xaraya.com>
*/

/**
 * Images API
 * @package Xaraya
 * @subpackage Images_API
 */


/**
 * initialise the images module
 */
function images_init()
{
    if (!xarModIsAvailable('uploads')) {
        $msg = xarML('The module [#(1)] should be activated first.', 'uploads');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'MODULE_DEPENDENCY', new SystemException($msg));
        return;
    }
    
    if(xarServerGetVar('PATH_TRANSLATED')) {
        $base_directory = dirname(realpath(xarServerGetVar('PATH_TRANSLATED')));
    } elseif(xarServerGetVar('SCRIPT_FILENAME')) {
        $base_directory = dirname(realpath(xarServerGetVar('SCRIPT_FILENAME')));
    } else {
        $base_directory = './';
    }
    
    // Load any predefined constants
    xarModAPILoad('images', 'user');
    
    // Set up module variables
    xarModSetVar('images', 'type.graphics-library', _IMAGES_LIBRARY_GD);
    xarModSetVar('images', 'path.derivative-store', $base_directory . '/images/.thumbs');
    
    $imageAttributes = array(new xarTemplateAttribute('src',         XAR_TPL_REQUIRED | XAR_TPL_STRING),
                             new xarTemplateAttribute('height',      XAR_TPL_OPTIONAL | XAR_TPL_STRING),
                             new xarTemplateAttribute('width',       XAR_TPL_OPTIONAL | XAR_TPL_STRING),
                             new xarTemplateAttribute('constrain',   XAR_TPL_OPTIONAL | XAR_TPL_STRING),
                             new xarTemplateAttribute('label',       XAR_TPL_REQUIRED | XAR_TPL_STRING));
    xarTplRegisterTag('images', 'image', $imageAttributes, 'images_userapi_handle_image_tag');
     
    // Initialisation successful
    return true;
}

/**
 * upgrade the images module from an old version
 */
function images_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case 1.0:
            // Code to upgrade from version 1.0 goes here
            break;
        case 2.0:
            // Code to upgrade from version 2.0 goes here
            break;
        case 2.5:
            // Code to upgrade from version 2.5 goes here
            break;
    }
}

/**
 * delete the images module
 */
function images_delete()
{
    // Delete module variables
    xarModDelVar('images', 'type.graphics-library');
    xarModDelVar('images', 'path.derivative-store');

    xarTplUnregisterTag('image');
    // Deletion successful
    return true;
}

?>
