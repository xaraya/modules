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

    @author Brian McGilligan <brian@mcgilligan.us>

    @return String  Contains module output
*/
function security_admin_hook_settings($args)
{
    extract($args);

    if( !Security::check(SECURITY_ADMIN, 'security') ){ return false; }

    if( !xarVarFetch('reload',        'str', $reload, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('submit_button', 'str', $submit, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('mod_itemtype',  'str', $mod_itemtype, null, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('owner_table',   'str', $owner_table, null, XARVAR_NOT_REQUIRED) ){ return false; }

    // Get default settings to start with
    if( !xarModAPILoad('security', 'user') ){ return false; }
    @list( $module, $itemtype ) = split('-', $mod_itemtype);
    $settings = SecuritySettings::factory(!empty($module) ? xarModGetIdFromName($module) : 0, $itemtype);

    // Load form data if Update button is pressed. and when module module/itemtype
    // pair has not changed and not on init page load
    if( !empty($submit) or (!is_null($reload) and $reload != "reload") )
    {
        //var_dump("Loading from form submission");
        if( !xarVarFetch('exclude_gids', 'array', $e_gids,   array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('default_group_level', 'array', $default_group_level, array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('primary_key', 'str', $primary_key, null, XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('owner_column', 'str', $owner_column, null, XARVAR_NOT_REQUIRED) ){ return false; }

        //
        if( !xarVarFetch('default_item_levels', 'array', $default_item_levels, array(), XARVAR_NOT_REQUIRED) ){ return false; }
        if( !xarVarFetch('default_module_levels', 'array', $default_module_levels, array(), XARVAR_NOT_REQUIRED) ){ return false; }
        $none = array();

        $settings->exclude_groups = null;
        foreach( $e_gids as $e_gids ){ $settings->exclude_groups[$e_gids] = $e_gids; }

        // Generate Owner Field
        $settings->owner_table       = !empty($owner_table)  ? $owner_table  : null;
        $settings->owner_column      = !empty($owner_column) ? $owner_column : null;
        $settings->owner_primary_key = !empty($primary_key)  ? $primary_key  : null;

        //Set Default group levels
        $settings->default_group_level = new SecurityLevel($default_group_level);

        // Calc all new levels
        $settings->default_item_levels = null;
        foreach( $default_item_levels as $key => $role_level )
        {
            $settings->default_item_levels[$key] = new SecurityLevel($role_level);
        }
        if( !isset($settings->default_item_levels['user']) ){ $settings->default_item_levels['user'] = new SecurityLevel(); }

        $settings->default_module_levels = null;
        foreach( $default_module_levels as $key => $role_level )
        {
            $settings->default_module_levels[$key] = new SecurityLevel($role_level);
        }

        $settings->save();
    }

    // Prepare template vars
    $data = array();
    $levels = $data['levels'] = $settings->default_item_levels;
    $data['mod_itemtype'] = $mod_itemtype;

    /*
        Calc Security Levels and make a Map
    */
    $secMap = array();
    $data['sec_levels'] = xarModAPIFunc('security', 'user', 'getlevels');
    $data['sec_map'] = $settings->default_item_levels;

    $data['hook_list'] = xarModAPIFunc('modules', 'admin', 'gethookedmodules',
        array(
            'hookModName' => 'security'
        )
    );

    $data['show_remove'] = true;
    $groups = xarModAPIFunc('roles', 'user', 'getallgroups');
    $data['all_groups'][0] = array('name' => xarML('All'));
    foreach( $groups as $group )
    {
        $data['all_groups'][$group['uid']] = $group;
    }

    // owner data
    $dbconn =& xarDBGetConn();
    $dict   =& xarDBNewDataDict($dbconn);
    $data['tables']  = $dict->getTables();
    if( !empty($settings->owner_table) ){ $data['columns'] = $dict->getColumns($settings->owner_table); }
    else{ $data['columns']  = array(); }

    if( empty($settings->owner_primary_key) )
    {
        foreach( $data['columns'] as $column )
        {
            if( $column->primary_key == true )
            {
                $settings->owner_primary_key = $column->name;
                break;
            }
        }
    }
    $data['settings'] = $settings;

    return xarTplModule('security', 'admin', 'hook_settings', $data);
}
?>