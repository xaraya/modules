<?php
/**
 * File: $Id: s.xaradmin.php 1.22 03/01/18 11:39:30-05:00 John.Cox@mcnabb. $
 *
 * Figlet Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage figlet Module
 * @author Lucas Baltes, John Cox
*/

/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with Figlet Menu information
 */
function figlet_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('AdminFiglet')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('figlet', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}

/**
 * modify configuration
 */
function figlet_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminFiglet')) return;

    // Get Fonts
    $handle = opendir('modules/figlet/xarfonts');

    while($f = readdir($handle)){
        if ($f != '.' && $f != '..' && $f != 'SCCS'){
            $fontdir[] = $f;
        }
    }

    closedir($handle);
    sort ($fontdir);
    $data['fontselect'] = array();

    foreach ($fontdir as $v){
        $data['fontselect'][] = array('fontname' => $v);
    }

    $data['fontnow'] = xarModGetVar('figlet', 'defaultfont');
    $data['submit'] = xarML('submit');
    $data['authid'] = xarSecGenAuthKey();

    return $data;
}

/**
 * update configuration
 */
function figlet_admin_updateconfig()
{
    $font = xarVarCleanFromInput('font');

    if (!xarSecConfirmAuthKey()) return;

    // Security Check
    if(!xarSecurityCheck('AdminFiglet')) return;

    xarModSetVar('figlet', 'defaultfont', $font);

    xarResponseRedirect(xarModURL('figlet', 'admin', 'modifyconfig'));

    return true;
}

?>