<?php
/*
 *
 * Keywords Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @author mikespub
*/

/**
 * show the links for module items
 */
function keywords_admin_view($args)
{ 
    extract($args);

    if (!xarVarFetch('modid',    'isset', $modid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype', 'isset', $itemtype, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemid',   'isset', $itemid,   NULL, XARVAR_NOT_REQUIRED)) {return;}

    if (!xarSecurityCheck('AdminKeywords')) return;

    $data = array();

    return $data;
}

?>
