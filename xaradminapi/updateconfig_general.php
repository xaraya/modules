<?php

/**
 *
 * Update configuration settings for the commerce module
 *
 * @package Commerce
 * @copyright (C) 2005 by The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Commerce
 * @link  link to information for the subpackage
 * @author author name Marc Lutolf <mfl@netspan.ch>
 */

function commerce_adminapi_updateconfig_general($args)
{
    if(!xarVarFetch('group_value', 'id:', $args['group_value'], 1)) return;
    return $args;
}

?>