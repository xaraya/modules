<?php
/**
 * Weather Module - initialization functions
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Weather Module
 * @link http://xaraya.com/index.php/release/662.html
 * @author Weather Module Development Team
 */

/**
 * Initialise the module
 *
 * @author Roger Raymond
 * @param none
 * @return bool true on success of installation
 */

function weather_init()
{
    # --------------------------------------------------------
    #
    # Set up masks
    #
        xarRegisterMask('ViewWeather','All','weather','All','All','ACCESS_OVERVIEW');
        xarRegisterMask('ReadWeather','All','weather','All','All','ACCESS_READ');
        xarRegisterMask('ManageWeather','All','weather','All','All','ACCESS_DELETE');
        xarRegisterMask('AdminWeather','All','weather','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up privileges
    #
        xarRegisterPrivilege('ReadWeather','All','weather','All','All','ACCESS_READ');
        xarRegisterPrivilege('ManageWeather','All','weather','All','All','ACCESS_DELETE');
        xarRegisterPrivilege('AdminWeather','All','weather','All','All','ACCESS_ADMIN');
        xarRegisterMask('ReadWeatherBlock', 'All', 'weather', 'Block', 'All', 'ACCESS_OVERVIEW');

    # --------------------------------------------------------
    #
    # Set up privilege instances
    #
        $xartable =& xarDBGetTables();
        $query = "SELECT DISTINCT i.xar_title 
                  FROM $xartable[block_instances] i, $xartable[block_types] t 
                  WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'weather'";
        $instances = array(
            array(
                'header' => 'Weather Block Title:',
                'query' => $query,
                'limit' => 20
                )
            );
        xarDefineInstance('weather', 'Block', $instances);

    # --------------------------------------------------------
    #
    # Set up modvars
    #
        xarModVars::set('weather', 'itemsperpage', 20);
        xarModVars::set('weather', 'useModuleAlias',0);
        xarModVars::set('weather', 'aliasname','Weather');
        
        xarModVars::set('weather', 'partner_id','xxx');
        xarModVars::set('weather', 'license_key','xxx');
        xarModVars::set('weather', 'default_location','a:3:{s:7:"country";s:2:"us";s:6:"region";s:10:"California";s:4:"city";a:2:{s:4:"name";s:8:"La Jolla";s:4:"code";s:8:"USCA0565";}}');
        xarModVars::set('weather', 'cc_cache_time',60*30); // 30 minutes - these should not be changed
        xarModVars::set('weather', 'ext_cache_time',60*60*2); // 2 hours - these should not be changed
        xarModVars::set('weather', 'units','m');
        xarModVars::set('weather', 'extdays',10);


    # --------------------------------------------------------
    #
    # Register blocks
    #
        if (!xarModAPIFunc('blocks','admin','register_block_type',
                array('modName' => 'weather',
                    'blockType' => 'weather'))) return;

    return true;
}


/**
 * Upgrade the module from an old version
 *
 * @author Rodulfo Araujo
 * @param string oldversion. This function takes the old version that is currently stored in the module db
 * @return bool true on succes of upgrade
 * @throws mixed This function can throw all sorts of errors, depending on the functions present
 */
function weather_upgrade($oldversion)
{
    /* Upgrade dependent on old version number */

    /* Update successful */
    return true;
}


/**
 * Delete the module
 *
 * @author Roger Raymond
 * @param none
 * @return bool true on succes of deletion
 */
function weather_delete()
{
        // Only change the next line. No need for anything else
        $this_module = 'weather';

    # --------------------------------------------------------
    #
    # Remove database tables
    #
        // Load table maintenance API
        sys::import('xaraya.tableddl');

        // Generate the SQL to drop the table using the API
        $prefix = xarDB::getPrefix();
        $table = $prefix . "_" . $this_module;
        $query = xarDBDropTable($table);
        if (empty($query)) return; // throw back

    # --------------------------------------------------------
    #
    # Remove block types
    #
        if (!xarModAPIFunc('blocks', 'admin', 'unregister_block_type', array('modName'  => 'weather', 'blockType'=> 'weather'))) return;

    # --------------------------------------------------------
    #
    # Remove block groups
    #

    # --------------------------------------------------------
    #
    # Delete all DD objects created by this module
    #
        try {
            $dd_objects = unserialize(xarModVars::get($this_module,$this_module . '_objects'));
            foreach ($dd_objects as $key => $value)
                $result = xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $value));
        } catch (Exception $e) {}

    # --------------------------------------------------------
    #
    # Remove the categories
    #
        try {
            xarModAPIFunc('categories', 'admin', 'deletecat',
                                 array('cid' => xarModVars::get($this_module, 'basecategory'))
                                );
        } catch (Exception $e) {}

    # --------------------------------------------------------
    #
    # Remove modvars, masks and privilege instances
    #
        xarRemoveMasks($this_module);
        xarRemoveInstances($this_module);
        xarModVars::delete_all($this_module);

        return true;
}
?>