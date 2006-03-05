<?php
/**
 * Owner - Tracks who creates xaraya based items.
 *
 * @package Xaraya Modules
 * @copyright (C) 2003-2005 by Envision Net, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.envisionnet.net/
 *
 * @subpackage Owner module
 * @link http://www.envisionnet.net/home/products/security/
 * @author Brian McGilligan <brian@envisionnet.net>
 */
/**
 * @author MrB
 * @Since 9 Feb 2006
 * @return array
 */
function owner_admin_main($args)
{
    // Security Check (this the right one?)
    if(!xarSecurityCheck('ChangeOwner')) return;
    $data = array();
  //  if (!xarModGetVar('adminpanels', 'overview')) {
  //      // Normal overview page
  //      return $data;
  //  } else {
  //      // If args specified allow functionless addressing the changeowner function
  //      xarResponseRedirect(xarModURL('owner', 'admin', 'changeowner',$args));
  //      return true;
  //  }
    return $data;
}
?>
