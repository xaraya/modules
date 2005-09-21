<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitecontact
 */

/**
 * The main administration function
 * @author Jo Dalle Nogare
 */
function sitecontact_admin_overview()
{
      if (!xarSecurityCheck('AdminSiteContact')) return;
    // success
    $data=array();
   return xarTplModule('sitecontact', 'admin', 'main',$data,'main');
}

?>
