<?php
/**
    Allows for creating default security settings for xaraya modules
    
    @author Brian McGilligan <brian@envisionnet.net>
    
    @return String  Contains module output
*/
function security_admin_hook_settings($args)
{
    extract($args);
    
    if( !xarSecurityCheck('AdminSecurity') ) return false;

    if( !xarVarFetch('submit', 'str', $submit, null, XARVAR_NOT_REQUIRED) ){ return false; }

    xarModAPILoad('security', 'user');
    
    $data = array();    

    if( !empty($submit) )
    {
        if( !xarVarFetch('mod_itemtype', 'str', $mod_itemtype, null) ){ return false; }
        list( $module, $itemtype ) = split('-', $mod_itemtype);

        $modid = null;       
        $default_var_name = "settings";
        if( !empty($module) )
        {
            $modid = XarModGetIdFromName($module);
            $default_var_name .= ".$modid";
            if( !empty($itemtype) )
            {
                $default_var_name .= ".$itemtype";
            }
        }
        /*
            Set a security levels set
        */
        if( $submit == 'Update' )
        {
            if( !xarVarFetch('exclude_gids', 'array', $e_gids,   array(), XARVAR_NOT_REQUIRED) ){ return false; }
            if( !xarVarFetch('default_group_level', 'int', $default_group_level, 48, XARVAR_NOT_REQUIRED) ){ return false; }
            if( !xarVarFetch('user',   'array', $user,   array(), XARVAR_NOT_REQUIRED) ){ return false; }
            if( !xarVarFetch('groups', 'array', $groups, array(), XARVAR_NOT_REQUIRED) ){ return false; }
            if( !xarVarFetch('world',  'array', $world,  array(), XARVAR_NOT_REQUIRED) ){ return false; }

            $exclude_gids = array();
            foreach( $e_gids as $e_gids )
            {
               $exclude_gids[$e_gids] = $e_gids;
            }
            
            // Calc all new levels
            $userLevel = 0;
            foreach( $user as $part )
                $userLevel += $part;
               
            $groupsLevel = array();
            foreach( $groups as $key => $group )
            {
                $groupsLevel[$key] = 0;
                foreach( $group as $part )
                    $groupsLevel[$key] += $part;
                if( $groupsLevel[$key] == 0 )
                    unset($groupsLevel[$key]);  
            }
            
            $worldLevel = 0;
            foreach( $world as $part )
                $worldLevel += $part;

            $settings = array(
                'exclude_groups' => $exclude_gids,
                'default_group_level' => $default_group_level,
                'levels' => array(
                    'user'   => $userLevel,
                    'groups' => $groupsLevel,
                    'world'  => $worldLevel
                )               
            );

            xarModSetVar('security', $default_var_name, serialize($settings)); 
        }
        /*
            Get a security levels set
        */
        else if( $submit == 'Reload' ) 
        {
            /*
                Default security settings
            */
            $settings = xarModAPIFunc('security', 'user', 'get_default_settings',
                array(
                    'modid'    => $modid,
                    'itemtype' => $itemtype
                )
            );
        }
        /*
            Adds another group to the set of levels
        */
        else if( $submit == 'Add Group' ) 
        {
            if( !xarVarFetch('group', 'int', $group, null) ){ return false; }
            $settings = xarModAPIFunc('security', 'user', 'get_default_settings',
                array(
                    'modid'    => $modid,
                    'itemtype' => $itemtype
                )
            );
            $settings['levels']['groups'][$group] = 0;
            xarModSetVar('security', $default_var_name, serialize($settings));
        }

        $levels = $settings['levels'];
        $data['levels'] = $settings['levels'];
        $data['settings'] = $settings;

        $secLevels = xarModAPIFunc('security', 'user', 'getlevels');
        
        /*
            Calc Security Levels and make a Map
        */
        $secMap = array();
        foreach( $secLevels as $secLevel )
        {
            $currentLevel = $secLevel['level'];
            $secMap['user'][$currentLevel] = $levels['user'] & $currentLevel;
            $secMap['world'][$currentLevel] = $levels['world'] & $currentLevel;
            foreach( $levels['groups'] as $gid => $group )
            {
                $secMap[$gid][$currentLevel] = $group & $currentLevel;
            }
        }
        $data['sec_levels']  = $secLevels;
        $data['sec_map']     = $secMap;
        
        $data['mod_itemtype'] = $mod_itemtype;
    }
    
    $hook_list = xarModAPIFunc('modules', 'admin', 'gethookedmodules', 
        array(
            'hookModName' => 'security'
        )
    );
        
    $all_groups = array();
    $groups = xarModAPIFunc('roles', 'user', 'getallgroups');
    foreach( $groups as $group )
    {
        $all_groups[$group['uid']] = $group;
    }    
    
    $data['hook_list']   = $hook_list;
    $data['show_remove'] = true;
    $data['all_groups']  = $all_groups;

    return xarTplModule('security', 'admin', 'hook_settings', $data);
}
?>