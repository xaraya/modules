<?php
/**
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
 * @return empty array
 */
function figlet_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('AdminFiglet')) return;
    // Return the output
    return array();
    //    xarController::redirect(xarModURL('figlet', 'admin', 'modifyconfig'));

    // success
//    return true;
}

/**
 * modify configuration
 */
function figlet_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminFiglet')) return;

    // Get Fonts
    $handle = opendir(sys::code().'modules/figlet/xarfonts');

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

    $data['fontnow'] = xarModVars::get('figlet', 'defaultfont');
    $data['submit'] = xarML('submit');
    $data['authid'] = xarSecGenAuthKey();

    return $data;
}

/**
 * update configuration
 */
function figlet_admin_updateconfig()
{
    if (!xarVarFetch('font', 'str:1:100', $font, '', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

    if (!xarSecConfirmAuthKey()) return;

    // Security Check
    if(!xarSecurityCheck('AdminFiglet')) return;

    xarModVars::set('figlet', 'defaultfont', $font);

    xarController::redirect(xarModURL('figlet', 'admin', 'modifyconfig'));

    return true;
}

?>