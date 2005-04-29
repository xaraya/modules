<?php
/**
 * File: $Id:
 * 
 * SiteContact main function
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * The main administration function
 */
function sitecontact_admin_overview()
{
      if (!xarSecurityCheck('AdminSiteContact')) return;
    // success
    $data=array();
   return xarTplModule('sitecontact', 'admin', 'main',$data,'main');
}

?>
