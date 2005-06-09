<?php

/**
 *
 * Modify configuration settings for the commerce module
 *
 * @package modules
 * @copyright (C) 2005 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * 
 * @subpackage commerce
 * @link  link to information for the subpackage
 * @author author name <marcel@hsdev.com>
 */

function commerce_admin_modifyconfig($args)
{
    if(!xarVarFetch('group_value','id:',$group_value,1)) return;
    extract($args);
    
    return xarModFunc('commerce','admin','commoninfo_object', array(
                            'objectname'   => 'ice_configuration', 
                            'use_grouping' => true,
                            'group_field'  => 'group_id',
                            'group_value'  => $group_value));
}

?>