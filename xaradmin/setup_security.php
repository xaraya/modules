<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
 * Setup the security
 * @since April 2006
 */
function helpdesk_admin_setup_security($args)
{
    extract($args);

    if( !xarVarFetch('setup', 'str', $setup, null, XARVAR_NOT_REQUIRED) ){ return false; }

    xarModAPILoad('helpdesk');

    $data = array();

    /*
        Check for Techs group
    */
    $tech_group_id = xarModGetVar('helpdesk', 'tech_group');
    $tech_group_name = xarML("Helpdesk Representatives");
    if( empty($tech_group_id) )
    {
        // Do a quick check incase group exists but helpdesk as not be informed yet.
        $tech_role = xarModAPIFunc('roles', 'user', 'get',
            array(
                'type' => 1, // groups
                'name' => $tech_group_name
            )
        );
        if( $tech_role != false )
        {
            $tech_group_id = $tech_role['uid'];
            // Set tech group as if has not been set yet
            xarModSetVar('helpdesk', 'tech_group', $tech_group_id);
            $tech_group_name = $tech_role['name'];
        }
    }
    else
    {
        $tech_role = xarModAPIFunc('roles', 'user', 'get',
            array(
                'type' => 1, // groups
                'uid' => (int)$tech_group_id
            )
        );
        if( $tech_role['state'] == 0 )
        {
            // Role is deleted so we can not use it.
            $tech_group_id = null;
        }
        else
        {
            // Otherwise we are ok.
            $tech_group_name = $tech_role['name'];
        }
    }
    if( $tech_group_id > 0 ){ $data['tech_exists'] = true; }
    else{ $data['tech_exists'] = false; }

    /*
        Check for hooks.
    */
    if( xarModIsAvailable('security') && xarModIsHooked('security', 'helpdesk', TICKET_ITEMTYPE) ){ $data['security_hooked'] = true; }
    else{ $data['security_hooked'] = false; }

    // Check for security levels set for tickets.
    $settings = xarModAPIFunc('security', 'user', 'get_default_settings',
        array(
            'modid'    => xarModGetIDFromName('helpdesk'),
            'itemtype' => TICKET_ITEMTYPE //Ticket
        )
    );
    /*
        Check security levels
    */
    if( helpdesk_level_to_numeric($settings['levels']['user']) >= 60 ){ $data['security_user_levels_ok'] = true; }
    else{ $data['security_user_levels_ok'] = false; }

    if( isset($settings['levels'][$tech_group_id])
        and helpdesk_level_to_numeric($settings['levels'][$tech_group_id]) >= 60 )
    { $data['security_tech_levels_ok'] = true; }
    else{ $data['security_tech_levels_ok'] = false; }

    if( helpdesk_level_to_numeric($settings['levels'][0]) == 0 ){ $data['security_world_levels_ok'] = true; }
    else{ $data['security_world_levels_ok'] = false; }

    /*
        Check owner settings. We want to have them so we don't use the owner module
    */
    if( $settings['owner'] == null ){ $data['owner_ok'] = false; }
    else{ $data['owner_ok'] = true; }

    /*
        Now we know what needs to be fixed we can try and fix things
    */
    if( !is_null($setup) )
    {
        // Try to update security
        if( $data['tech_exists']  == false )
        {
            // Create tech group
            if( xarMakeGroup($tech_group_name) === true )
            {
                $role = xarFindRole($tech_group_name);
                if( is_object($role) )
                {
                    $tech_group_id = $role->uid;
                    xarModSetVar('helpdesk', 'tech_group', $tech_group_id);
                    if( xarMakeRoleMemberByName($tech_group_name, "Users") )
                    {
                        $data['tech_exists'] = true;
                    }
                }
            }
        }

        if( $data['security_hooked'] == false )
        {
            $result = xarModAPIFunc('modules','admin','enablehooks',
                array(
                    'callerModName' => 'helpdesk',
                    'callerItemType' => TICKET_ITEMTYPE, // Ticket Item Type
                    'hookModName' => 'security'
                )
            );
            if( $result == true ){ $data['security_hooked'] = true; }
        }

        $update_security_levels = false;
        if( $data['security_user_levels_ok'] == false )
        {
            $settings['levels']['user'] = array(
                'overwrite' => 1
                , 'read'    => 1
                , 'comment' => 1
                , 'write'   => 1
                , 'manage'  => 0
                , 'admin'   => 0
            );
            $update_security_levels = true;
        }

        if( $data['security_tech_levels_ok'] == false && $tech_group_id > 0 )
        {
            $settings['levels'][$tech_group_id] = array(
                'overwrite' => 1
                , 'read'    => 1
                , 'comment' => 1
                , 'write'   => 1
                , 'manage'  => 0
                , 'admin'   => 0
            );
            $update_security_levels = true;
        }

        if( $data['security_world_levels_ok'] == false )
        {
            $settings['levels'][0] = array(
                'overwrite' => 0
                , 'read'    => 0
                , 'comment' => 0
                , 'write'   => 0
                , 'manage'  => 0
                , 'admin'   => 0
            );
            // Also forcing default group level
            // if user is runnning this they want
            // a low default group level
            $settings['default_group_level'] = 0;
            $update_security_levels = true;
        }

        if( $data['owner_ok'] == false )
        {
            xarModAPILoad('helpdesk');
            $xartable =& xarDBGetTables();

            $settings['owner']['table'] = $xartable['helpdesk_tickets'];
            $settings['owner']['column'] = 'xar_openedby';
            $settings['owner']['primary_key'] = 'xar_id';

            $update_security_levels = true;
        }

        if( $update_security_levels == true )
        {
            $result = xarModAPIFunc('security', 'admin', 'set_hook_settings',
                array(
                    'modid' => xarModGetIdFromName('helpdesk'),
                    'itemtype' => 1,
                    'settings' => $settings
                )
            );
            if( $result == true )
            {
                $data['security_user_levels_ok'] = true;
                $data['security_tech_levels_ok'] = true;
                $data['security_world_levels_ok'] = true;
                $data['owner_ok'] = true;
            }
        }
    }

    return xarTplModule('helpdesk', 'admin', 'setup_security', $data);
}

function helpdesk_level_to_numeric($level)
{
    xarModAPILoad('security');
    $map = array(
        'overview'  => SECURITY_OVERVIEW
        , 'read'    => SECURITY_READ
        , 'comment' => SECURITY_COMMENT
        , 'write'   => SECURITY_WRITE
        , 'manage'  => SECURITY_MANAGE
        , 'admin'   => SECURITY_ADMIN
    );

    $numeric_level = 0;
    foreach( $level as $key => $value )
    {
        if( $value == 1 )
        {
            $numeric_level += $map[$key];
        }
    }

    return $numeric_level;
}

?>