<?php

/**
 *
 * Manage configuration groups
 *
 * @package modules
 * @copyright (C) 2005 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * 
 * @subpackage commerce
 * @author author name <marcel@hsdev.com>
 */
function commerce_admin_configgroups()
{
    return xarModFunc('commerce','admin','commoninfo_object',array('objectname' => 'ice_config_groups'));
}

?>
