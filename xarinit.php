<?php
/**
 * Weather Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
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
    /* Set up initial values for module variables. */
    xarModSetVar('weather','partner_id','');
    xarModSetVar('weather','license_key','');
    xarModSetVar('weather','default_location','');
    xarModSetVar('weather','cc_cache_time',60*30); // 30 minutes - these should not be changed
    xarModSetVar('weather','ext_cache_time',60*60*2); // 2 hours - these should not be changed
    xarModSetVar('weather','units','m');
    xarModSetVar('weather','extdays',10);
    
    /* Register blocks. */
    if (!xarModAPIFunc('blocks','admin','register_block_type',
            array('modName' => 'weather',
                'blockType' => 'current'))) return;
    
    /* Define instances for this module. */
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
    
    /* First for the blocks */
    xarRegisterMask('ReadWeatherBlock', 'All', 'weather', 'Block', 'All', 'ACCESS_OVERVIEW');
    /* Then for all operations */
    xarRegisterMask('ViewWeather', 'All', 'weather', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadWeather', 'All', 'weather', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('AdminWeather', 'All', 'wethear', 'Item', 'All:All:All', 'ACCESS_ADMIN');
    return true;

    /* This init function brings our module to version 1.0.1, run the upgrades for the rest of the initialisation */
    return weather_upgrade('1.1.1');
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
    /* Delete any module variables */
    xarModDelAllVars('weather');
    /* UnRegister all blocks that the module uses*/
    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'weather',
                'blockType' => 'current'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'weather',
                'blockType' => 'forecast'))) return;


    /* Remove Masks and Instances. */
    xarRemoveMasks('weather');
    xarRemoveInstances('weather');

    /* Deletion successful*/
    return true;
}
?>