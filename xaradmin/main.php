<?php
/**
 * Owner - Tracks who creates xaraya based items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Owner Module
 * @author MrB
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
