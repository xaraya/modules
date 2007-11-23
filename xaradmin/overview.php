<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarbb Module
 */
/**
 * Overview displays standard Overview page
 *
 * Only used if you actually supply an overview link in your adminapi menulink function
 * and used to call the template that provides display of the overview
 *
 * @author John Cox <niceguyeddie@xaraya.com>
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @return array xarTplModule with $data containing template data
 * @since 3 Sept 2005
 */
function xarbb_admin_overview()
{
    // Security Check
    if (!xarSecurityCheck('AdminxarBB', 0)) return;

    $data=array();

    // if there is a separate overview function return data to it
    // else just call the main function that usually displays the overview

    return xarTplModule('xarbb', 'admin', 'main', $data, 'main');
}

?>