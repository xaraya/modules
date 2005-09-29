<?php
/**
 * Overview menu link
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitecontact
 */

/**
 * The displays the overview in the menu
 * @author Jo Dalle Nogare
 */
function sitecontact_admin_overview()
{
    if (!xarSecurityCheck('AdminSiteContact')) return;

    $data=array();
    
    /* let's just display the main module overview */
    return xarTplModule('sitecontact', 'admin', 'main',$data,'main');
}

?>