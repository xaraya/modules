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

    if( !xarVarFetch('reload',        'str', $reload, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('submit_button', 'str', $submit, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('mod_itemtype',  'str', $mod_itemtype, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('owner_table',   'str', $owner_table, null, XARVAR_NOT_REQUIRED) ){ return false; }

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

        if( !xarVarFetch('overview', 'array', $overview,array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('read',     'array', $read,    array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('comment',  'array', $comment, array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('write',    'array', $write,   array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('manage',   'array', $manage,  array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('admin',    'array', $admin,   array(), XARVAR_NOT_REQUIRED) ){ return false; }

        $exclude_gids = array();
        foreach( $e_gids as $e_gids ){ $exclude_gids[$e_gids] = $e_gids; }

        // Generate Owner Field
        $owner = null;
        if( !empty($owner_table) ){ $owner['table'] = $owner_table; }
        if( !empty($owner_column) ){ $owner['column'] = $owner_column; }
        if( !empty($primary_key) ){ $owner['primary_key'] = $primary_key; }

        $secLevels = xarModAPIFunc('security', 'user', 'getlevels');
        $levels = array();
        // Calc all new levels
        foreach( $secLevels as $secLevel )
        {
            foreach( $$secLevel['name'] as $role_id => $value )
            {
                $levels[$role_id][$secLevel['name']] = $value;
            }
        }

        foreach( $levels as $role_id => $level_type )
        {
            foreach( $secLevels as $secLevel )
            {
                if( !isset($levels[$role_id][$secLevel['name']]) )
                    $levels[$role_id][$secLevel['name']] = 0;
            }
        }

        $settings = array(
            'exclude_groups' => $exclude_gids,
            'default_group_level' => $default_group_level,
            'owner' => $owner,
            'levels' => $levels
        );
        $result = xarModAPIFunc('security', 'admin', 'set_hook_settings',
            array(
                'modid' => !empty($module)?xarModGetIdFromName($module):null,
                'itemtype' => $itemtype,
                'settings' => $settings
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
            $settings['levels'][$group] = array(
                'overview'  => 0
                , 'read'    => 0
                , 'comment' => 0
                , 'write'   => 0
                , 'manage'  => 0
                , 'admin'   => 0
            );
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
    $data['sec_map'] = $settings['levels'];

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