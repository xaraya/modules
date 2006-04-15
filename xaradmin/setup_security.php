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
    if( empty($tech_group_id) )
    {
        $tech_group_name = xarML("Helpdesk Representatives");

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
        $tech_group_name = $tech_role['name'];
    }
    if( !empty($tech_group_name) and xarFindRole($tech_group_name) ){ $data['tech_exists'] = true; }
    else{ $data['tech_exists'] = false; }

    /*
        Check for hooks
    */
    if( xarModIsHooked('security', 'helpdesk') ){ $data['security_hooked'] = true; }
    else{ $data['security_hooked'] = true; }

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
    if( $settings['levels']['user'] >= 60 ){ $data['security_user_levels_ok'] = true; }
    else{ $data['security_user_levels_ok'] = false; }

    if( isset($settings['levels']['groups'][$tech_group_id])
        and $settings['levels']['groups'][$tech_group_id] >= 60 )
    { $data['security_tech_levels_ok'] = true; }
    else{ $data['security_tech_levels_ok'] = false; }

    if( $settings['levels']['world'] == 0 ){ $data['security_world_levels_ok'] = true; }
    else{ $data['security_world_levels_ok'] = false; }

    /*
        Check owner settings. We want do have them so we don't use the owner module
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
            if( ($group = xarMakeGroup($tech_group_name)) != false )
            {
                $tech_group_id = $group['uid'];
                if( xarMakeRoleMemberByName($tech_group_name, "Users") )
                {
                    $data['tech_exists'] = true;
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
            $settings['levels']['user'] = 60;
            $update_security_levels = true;
        }

        if( $data['security_tech_levels_ok'] == false )
        {
            $settings['levels']['groups'][$tech_group_id] = 60;
            $update_security_levels = true;
        }

        if( $data['security_world_levels_ok'] == false )
        {
            $settings['levels']['world'] = 0;
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
?>