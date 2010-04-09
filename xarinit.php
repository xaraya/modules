<?php
/**
 * Initialization
 *
 * @package modules
 * @copyright (C) copyright-placeholder
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
    xarModAPILoad('images', 'user');

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

    xarRegisterMask('AdminImages', 'All','images','Image','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up privileges
    #
        xarRegisterPrivilege('ViewImages','All','images','All','All','ACCESS_OVERVIEW');
        xarRegisterPrivilege('ReadImages','All','images','All','All','ACCESS_READ');
        xarRegisterPrivilege('CommentImages','All','images','All','All','ACCESS_COMMENT');
        xarRegisterPrivilege('ModerateImages','All','images','All','All','ACCESS_MODERATE');
        xarRegisterPrivilege('EditImages','All','images','All','All','ACCESS_EDIT');
        xarRegisterPrivilege('AddImages','All','images','All','All','ACCESS_ADD');
        xarRegisterPrivilege('ManageImages','All','images','All','All','ACCESS_DELETE');
        xarRegisterPrivilege('AdminImages','All','images','All','All','ACCESS_ADMIN');

    if (!xarModRegisterHook('item', 'transform', 'API', 'images', 'user', 'transformhook')) {
        $msg = xarML('Could not register hook.');
        throw new Exception($msg);
    }

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
    switch($oldversion) {
        case '1.0.0':
            // Code to upgrade from version 1.0.0 goes here
            $thumbsdir = xarModVars::get('images', 'path.derivative-store');
            if (!empty($thumbsdir) && is_dir($thumbsdir)) {
                xarModVars::set('images','upgrade-1.0.0',1);
                // remove all old-style derivatives
            /* skip this - too risky depending on site config
                $images = xarModAPIFunc('images','admin','getderivatives');
                if (!empty($images)) {
                    foreach ($images as $image) {
                        @unlink($image['fileLocation']);
                    }
                }
            */
            }
            // Fall through to next upgrade

        case '1.1.0':
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
    // Remove mask
    xarUnregisterMask('AdminImages');
    // Unregister the hook
    xarModUnregisterHook('item', 'transform', 'API', 'images', 'user', 'transformhook');
    // Delete module variables
    xarModVars::delete_all('images');
    // Deletion successful
    return true;
}
?>
