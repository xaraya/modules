<?php
/**
 * File: $Id: s.xaradmin.php 1.22 03/01/18 11:39:30-05:00 John.Cox@mcnabb. $
 *
 * Xaraya converter
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage converter Module
 * @author Jim McDonald
*/

/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with converter Menu information
 */
function converter_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('Adminconverter')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('converter', 'admin', 'pntheme'));
    }
    // success
    return true;
}

function converter_admin_pntheme()
{

    // Get parameters from whatever input we need
    list($theme,
         $author,
         $confirmation,
         $id) = xarVarCleanFromInput('theme',
                                     'author',
                                     'confirmation',
                                     'id');

    if(!xarSecurityCheck('Adminconverter', 0)) return;

    // Check for confirmation.
    if (empty($confirmation)) {

    $data['authid'] = xarSecGenAuthKey();

    return $data;

    }

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // Check arguments
    if (empty($theme)) {
        $msg = xarML('No Theme Provided, Please Go Back and Provide Theme');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if (empty($author)) {
        $msg = xarML('No Author Provided, Please Go Back and Provide Author');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if (empty($id)) {
        $msg = xarML('No ID Provided, Please Go Back and Provide ID');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $theme_dir = xarConfigGetVar('Site.BL.ThemesDirectory')."/$theme/";

    // The API function is called
    $output = xarModAPIFunc('converter',
                            'admin',
                            'pntheme',
                            array('theme'       => $theme,
                                  'theme_dir'   => $theme_dir));

    // The API function is called
    $output = xarModAPIFunc('converter',
                            'admin',
                            'createxarthemefile',
                            array('theme'       => $theme,
                                  'author'      => $author,
                                  'theme_dir'   => $theme_dir,
                                  'id'          => $id));

    // load themes into *_themes table
    if (!xarModAPIFunc('themes', 'admin', 'regenerate')) {
        return NULL;
    }

    $regid=xarThemeGetIDFromName($theme);
    if (isset($regid)) {
        if (!xarModAPIFunc('themes','admin','setstate', array('regid'=> $regid,
                                                              'state'=> XARTHEME_STATE_INACTIVE)))
        {
            return;
        }
        // Activate the module
        if (!xarModAPIFunc('themes','admin','activate', array('regid'=> $regid)))
        {
            return;
        }
    }

    xarResponseRedirect(xarModURL('converter', 'admin', 'main'));

    // Return
    return true;
}

function converter_admin_phpnuketheme()
{

    // Get parameters from whatever input we need
    list($theme,
         $author,
         $confirmation,
         $id) = xarVarCleanFromInput('theme',
                                     'author',
                                     'confirmation',
                                     'id');

    if(!xarSecurityCheck('Adminconverter', 0)) return;

    // Check for confirmation.
    if (empty($confirmation)) {

    $data['authid'] = xarSecGenAuthKey();

    return $data;

    }

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // Check arguments
    if (empty($theme)) {
        $msg = xarML('No Theme Provided, Please Go Back and Provide Theme');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if (empty($author)) {
        $msg = xarML('No Author Provided, Please Go Back and Provide Author');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    if (empty($id)) {
        $msg = xarML('No ID Provided, Please Go Back and Provide ID');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $theme_dir = xarConfigGetVar('Site.BL.ThemesDirectory')."/$theme/";

    // The API function is called
    $output = xarModAPIFunc('converter',
                            'admin',
                            'pnuketheme',
                            array('theme'       => $theme,
                                  'theme_dir'   => $theme_dir));

    // The API function is called
    $output = xarModAPIFunc('converter',
                            'admin',
                            'createxarthemefile',
                            array('theme'       => $theme,
                                  'author'      => $author,
                                  'theme_dir'   => $theme_dir,
                                  'id'          => $id));

    // load themes into *_themes table
    if (!xarModAPIFunc('themes', 'admin', 'regenerate')) {
        return NULL;
    }

    $regid=xarThemeGetIDFromName($theme);
    if (isset($regid)) {
        if (!xarModAPIFunc('themes','admin','setstate', array('regid'=> $regid,
                                                              'state'=> XARTHEME_STATE_INACTIVE)))
        {
            return;
        }
        // Activate the module
        if (!xarModAPIFunc('themes','admin','activate', array('regid'=> $regid)))
        {
            return;
        }
    }

    xarResponseRedirect(xarModURL('converter', 'admin', 'main'));

    // Return
    return true;
}

?>