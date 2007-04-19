<?php
/**
 * Deletes an event.
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * Delete an event
 *
 * DEPRECATED: moved to 'delete' for consistency
 *
 * Delete an item from the events table
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @author  Julian Development Team, MichelV. <michelv@xarayahosting.nl>
 * @access  private
 * @param   $event_id ID of the event
 * @return  array
 * @todo    MichelV. <#>
 */
function julian_adminapi_deleteevent($args)
{
    return xarModAPIfunc('julian', 'admin', 'delete', $args);
}

?>