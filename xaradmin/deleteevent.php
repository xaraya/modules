<?php
/**
 * Delete an event
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * Delete an event
 * @deprecated, replaced since April 2007 by julian-admin-delete() 
 * This function takes the delete command, checks the privilege of the current user and,
   if passed, passes the delete command to the API.
 *
 * @param  id 'event_id' the id of the event to be deleted, or
 * @param  int objectid
 * @param  string cal_date
 * @return bool and URL redirect
 */
function julian_admin_deleteevent($args)
{
    return xarModFunc('julian', 'admin', 'delete', $args);
}
?>