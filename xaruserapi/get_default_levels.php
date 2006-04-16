<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
    Gets the default security levesl for a modules / itemtype pair
    If there are no module/ itemtype pairs matching it will return
    defaults to use instead.

    @param $args['modid']
    @param $args['itemtype'] (optional)

    @return array The default security levels
*/
function security_userapi_get_default_levels($args)
{
    extract($args);

    $settings = xarModAPIFunc('security', 'user', 'get_default_settings', $args);
    if( !$settings ){ return false; }

    return $settings['levels'];
}
?>