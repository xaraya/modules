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
    Allows for creating default security settings for xaraya modules

    @author Brian McGilligan <brian@envisionnet.net>

    @return String  Contains module output
*/
function security_admin_hook_settings($args)
{
    extract($args);

    if( !xarSecurityCheck('AdminSecurity') ) return false;

    if( !xarVarFetch('reload', 'str', $reload, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('submit_button', 'str', $submit, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('mod_itemtype', 'str', $mod_itemtype, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('owner_table', 'str', $owner_table, null, XARVAR_NOT_REQUIRED) ){ return false; }

    // Get default settings to start with
    @list( $module, $itemtype ) = split('-', $mod_itemtype);
    $settings = xarModAPIFunc('security', 'user', 'get_default_settings',
        array(
            'modid' => !empty($module)?xarModGetIdFromName($module):null,
            'itemtype' => $itemtype
        )
    );

    // Load form data if Update button is pressed. and when module module/itemtype
    // pair has not changed and not on init page load
    if( !empty($submit) or (!is_null($reload) and $reload != "reload") )
    {
        //var_dump("Loading from form submission");
        if( !xarVarFetch('exclude_gids', 'array', $e_gids,   array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('default_group_level', 'int', $default_group_level, 48, XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('primary_key', 'str', $primary_key, null, XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('owner_column', 'str', $owner_column, null, XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('user',   'array', $user,   array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('groups', 'array', $groups, array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('world',  'array', $world,  array(), XARVAR_NOT_REQUIRED) ){ return false; }

        $exclude_gids = array();
        foreach( $e_gids as $e_gids ){ $exclude_gids[$e_gids] = $e_gids; }

        // Generate Owner Field
        $owner = null;
        if( !empty($owner_table) ){ $owner['table'] = $owner_table; }
        if( !empty($owner_column) ){ $owner['column'] = $owner_column; }
        if( !empty($primary_key) ){ $owner['primary_key'] = $primary_key; }

        // Calc all new levels
        $userLevel = 0;
        foreach( $user as $part ){ $userLevel += $part; }

        $groupsLevel = array();
        foreach( $groups as $key => $group )
        {
            $groupsLevel[$key] = 0;
            foreach( $group as $part ){ $groupsLevel[$key] += $part; }
            if( $groupsLevel[$key] == 0 ){ unset($groupsLevel[$key]); }
        }

        $worldLevel = 0;
        foreach( $world as $part ){ $worldLevel += $part; }

        $settings = array(
            'exclude_groups' => $exclude_gids,
            'default_group_level' => $default_group_level,
            'owner' => $owner,
            'levels' => array(
                'user'   => $userLevel,
                'groups' => $groupsLevel,
                'world'  => $worldLevel
            )
        );
    }

    if( !empty($submit) )
    {
        /*
            Set a security levels set
        */
        if( $submit == 'Update' )
        {
        }
        /*
            Adds another group to the set of levels
        */
        else if( $submit == 'Add Group' )
        {
            if( !xarVarFetch('group', 'int', $group, null) ){ return false; }
            /*$settings = xarModAPIFunc('security', 'user', 'get_default_settings',
                array(
                    'modid'    => $modid,
                    'itemtype' => $itemtype
                )
            );*/
            $settings['levels']['groups'][$group] = 0;
        }
        // Save the updated settings
        $result = xarModAPIFunc('security', 'admin', 'set_hook_settings',
            array(
                'modid' => !empty($module)?xarModGetIdFromName($module):null,
                'itemtype' => $itemtype,
                'settings' => $settings
            )
        );
    }

    // Prepare template vars
    $data = array();
    $levels = $data['levels'] = $settings['levels'];
    $data['settings'] = $settings;
    $data['mod_itemtype'] = $mod_itemtype;

    /*
        Calc Security Levels and make a Map
    */
    $secMap = array();
    $data['sec_levels'] = xarModAPIFunc('security', 'user', 'getlevels');
    foreach( $data['sec_levels'] as $secLevel )
    {
        $currentLevel = $secLevel['level'];
        $data['sec_map']['user'][$currentLevel] = $levels['user'] & $currentLevel;
        $data['sec_map']['world'][$currentLevel] = $levels['world'] & $currentLevel;
        foreach( $levels['groups'] as $gid => $group )
        {
            $data['sec_map'][$gid][$currentLevel] = $group & $currentLevel;
        }
    }

    $data['hook_list'] = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
        array(
            'hookModName' => 'security'
        )
    );

    $data['show_remove'] = true;
    $all_groups = array();
    $groups = xarModAPIFunc('roles', 'user', 'getallgroups');
    foreach( $groups as $group )
    {
        $data['all_groups'][$group['uid']] = $group;
    }

    // owner data
    $dbconn =& xarDBGetConn();
    $dict   =& xarDBNewDataDict($dbconn);
    $data['owner_table'] = @$settings['owner']['table'];
    $data['owner_column'] = @$settings['owner']['column'];
    $data['tables']  = $dict->getTables();
    if( !empty($data['owner_table']) ){ $data['columns'] = $dict->getColumns($data['owner_table']); }
    else{ $data['columns']  = array(); }

    $data['owner_primary_key'] = @$settings['owner']['primary_key'];
    if( empty($data['owner_primary_key']) )
    {
        foreach( $data['columns'] as $column )
        {
            if( $column->primary_key == true )
            {
                $data['owner_primary_key'] = $column->name;
                break;
            }
        }
    }

    return xarTplModule('security', 'admin', 'hook_settings', $data);
}
?>