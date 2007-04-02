<?php

/**
 *
 * Modify configuration settings for the products module
 *
 * @package modules
 * @copyright (C) 2005 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage commerce
 * @link  link to information for the subpackage
 * @author author name Marc Lutolf <mfl@netspan.ch>
 */

function products_admin_modifyconfig_general($args)
{
    if (!xarVarFetch('group_value', 'id:', $group_value, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('subtab', 'str', $data['subtab'], 'manage', XARVAR_NOT_REQUIRED)) return;

    switch ($data['subtab']) {
        case 'configuration':
        default:
        $data['config_args'] = array(
                        'objectname'   => 'ice_configuration',
                        'use_grouping' => true,
                        'group_field'  => 'group_id',
                        'group_value'  => $group_value);
/*          return xarModFunc('commerce','admin','commoninfo_object', array(
                                    'objectname'   => 'ice_configuration',
                                    'use_grouping' => true,
                                    'group_field'  => 'group_id',
                                    'group_value'  => $group_value));
                                    */
            break;
        case 'manage':
    }
    return $data;
}

?>