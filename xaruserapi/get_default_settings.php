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
    Gets the default settings a module / itemtype pair
    If there are no module/ itemtype pairs matching it will return
    defaults to use instead.

    @param $args['modid']
    @param $args['itemtype'] (optional)

    @return array The default settings
*/
function security_userapi_get_default_settings($args)
{
    extract($args);

    $vars = array("settings");
    if( !empty($modid) )
    {
        array_push($vars, "settings.$modid");
        if( !empty($itemtype) )
        {
            array_push($vars, "settings.$modid.$itemtype");
        }
    }

    $settings = array();
    while( empty($settings) && ($var = array_pop($vars)) != null )
    {
        $settings =@ unserialize(xarModGetVar('security', $var));
    }

    if( !isset($settings['owner']) )
    {
        $settings['owner'] = null;
    }
    if( !isset($settings['exclude_groups']) )
    {
        $settings['exclude_groups'] = array();
    }
    if( !isset($settings['default_group_level']) )
    {
        $settings['default_group_level'] = SECURITY_OVERVIEW+SECURITY_READ;
    }
    if( !isset($settings['levels']) )
    {
        $settings['levels'] = array(
            'user' => SECURITY_OVERVIEW+SECURITY_READ+SECURITY_COMMENT+SECURITY_WRITE+SECURITY_ADMIN,
            'groups' => array(),
            'world' => SECURITY_OVERVIEW+SECURITY_READ
        );
    }

    return $settings;
}
?>