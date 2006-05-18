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
            'user' => array(
                'overview'  => 1
                , 'read'    => 1
                , 'comment' => 1
                , 'write'   => 1
                , 'manage'  => 1
                , 'admin'   => 1
            ),
            0 => array(
                'overview'  => 1
                , 'read'    => 1
                , 'comment' => 0
                , 'write'   => 0
                , 'manage'  => 0
                , 'admin'   => 0
            )
        );
    }

    // Code to convert old levels to new style
    if( !is_array($settings['levels']['user']) )
    {
        // Move groups down a level
        if( isset($settings['levels']['groups']) )
        {
            foreach( $settings['levels']['groups'] as $uid => $level )
            {
                $settings['levels'][$uid] = $level;
            }
            unset($settings['levels']['groups']);
        }
        if( isset($settings['levels']['world']) )
        {
            $settings['levels'][0] = $settings['levels']['world'];
            unset($settings['levels']['world']);
        }

        // Now convert from int to an array
        foreach( $settings['levels'] as $key => $value )
        {
            if( !is_array($value) )
            {
                $settings['levels'][$key] = array(
                    'overview' => $value & SECURITY_OVERVIEW ? 1 : 0
                    ,'read' => $value & SECURITY_READ ? 1 : 0
                    ,'comment' => $value & SECURITY_COMMENT ? 1 : 0
                    ,'write' => $value & SECURITY_WRITE ? 1 : 0
                    ,'manage' => $value & SECURITY_MANAGE ? 1 : 0
                    ,'admin' => $value & SECURITY_ADMIN ? 1 : 0
                );
            }
        }
    }

    return $settings;
}
?>