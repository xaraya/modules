<?php
/**
 *
 * AuthLDAP 
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @author Chris Dudley <miko@xaraya.com> | Richard Cave <rcave@xaraya.com> | Sylvain Beucler <beuc@beuc.net>
 */

/**
 * Calls the groups synchronization On Demand (tm), rather than
 * waiting for the cron job.
 */
function authldap_admin_manuallysyncgroups()
{
  // Security check
  if(!xarSecurityCheck('AdminAuthLDAP')) return false;

  $success = xarModAPIFunc('authldap', 'admin', 'syncgroups', array());
  return array('success' => $success);
}
?>
