<?php
/**
 * Wurfl Module
 *
 * @package modules
 * @subpackage wurfl module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 *
 * Initialise or remove the wurfl module
 *
 */

    sys::import('xaraya.structures.query');

    function wurfl_init()
    {

        xarMasks::register('ViewWurfl','All','wurfl','All','All','ACCESS_OVERVIEW');
        xarMasks::register('ReadWurfl','All','wurfl','All','All','ACCESS_READ');
        xarMasks::register('CommentWurfl','All','wurfl','All','All','ACCESS_COMMENT');
        xarMasks::register('ModerateWurfl','All','wurfl','All','All','ACCESS_MODERATE');
        xarMasks::register('EditWurfl','All','wurfl','All','All','ACCESS_EDIT');
        xarMasks::register('AddWurfl','All','wurfl','All','All','ACCESS_ADD');
        xarMasks::register('ManageWurfl','All','wurfl','All','All','ACCESS_DELETE');
        xarMasks::register('AdminWurfl','All','wurfl','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up privileges
    #
        xarPrivileges::register('ViewWurfl','All','wurfl','All','All','ACCESS_OVERVIEW');
        xarPrivileges::register('ReadWurfl','All','wurfl','All','All','ACCESS_READ');
        xarPrivileges::register('CommentWurfl','All','wurfl','All','All','ACCESS_COMMENT');
        xarPrivileges::register('ModerateWurfl','All','wurfl','All','All','ACCESS_MODERATE');
        xarPrivileges::register('EditWurfl','All','wurfl','All','All','ACCESS_EDIT');
        xarPrivileges::register('AddWurfl','All','wurfl','All','All','ACCESS_ADD');
        xarPrivileges::register('ManageWurfl','All','wurfl','All','All','ACCESS_DELETE');
        xarPrivileges::register('AdminWurfl','All','wurfl','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up modvars
    #
        $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'wurfl'));
        $module_settings->initialize();

        // Add variables like this next one when creating utility modules
        // This variable is referenced in the xaradmin/modifyconfig-utility.php file
        // This variable is referenced in the xartemplates/includes/defaults.xd file
        xarModVars::set('wurfl', 'defaultmastertable','wurfl_wurfl');

    # --------------------------------------------------------
    #
    # Set up events
    #
        // Unregister all mapper event subjects 
        xarMapperEvents::unregisterSubject('PreDispatch');
        xarMapperEvents::unregisterSubject('PostDispatch');
        // Unregister all mapper event observers
        xarMapperEvents::unregisterObserver('PreDispatch');
        xarMapperEvents::unregisterObserver('PostDispatch');  

        // Register wurfl mapper event subjects 
        xarMapperEvents::registerSubject('PreDispatch', 'mapper', 'wurfl');
        xarMapperEvents::registerSubject('PostDispatch', 'mapper', 'wurfl');
        // Register wurfl mapper event observers
        xarMapperEvents::registerObserver('PreDispatch', 'wurfl');
        xarMapperEvents::registerObserver('PostDispatch', 'wurfl');             

        return true;
    }

    function wurfl_upgrade()
    {
        return true;
    }

    function wurfl_delete()
    {
        $this_module = 'wurfl';
        xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $this_module));
        
        // Unregister all mapper event subjects 
        xarMapperEvents::unregisterSubject('PreDispatch');
        xarMapperEvents::unregisterSubject('PostDispatch');
        // Unregister all mapper event observers
        xarMapperEvents::unregisterObserver('PreDispatch');
        xarMapperEvents::unregisterObserver('PostDispatch');  

        // Register default mapper event subjects 
        xarMapperEvents::registerSubject('PreDispatch', 'mapper', 'themes');
        xarMapperEvents::registerSubject('PostDispatch', 'mapper', 'themes');
        // Register default mapper event observers
        xarMapperEvents::registerObserver('PreDispatch', 'themes');
        xarMapperEvents::registerObserver('PostDispatch', 'themes');  
        
        return true;
    }

?>
