<?php
/**
 *
 * Initialise or remove the xarayatesting module
 *
 */

    function xarayatesting_init()
    {
    # --------------------------------------------------------
    #
    # Set up tables
    #
        sys::import('xaraya.structures.query');
        $q = new Query();
        $prefix = xarDB::getPrefix();

        $query = "DROP TABLE IF EXISTS " . $prefix . "_xarayatesting_test";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_xarayatesting_test (
          id                   integer unsigned NOT NULL auto_increment,
          code                 varchar(64) NULL,
          name                 varchar(64) NULL,
          testsuite            varchar(64) NULL,
          testgroup            varchar(64) NULL,
          description          text,
          expected             text,
          step01               text,
          step02               text,
          step03               text,
          step04               text,
          step05               text,
          step06               text,
          step07               text,
          step08               text,
          step09               text,
          step10               text,
          state                tinyint unsigned default 0 NOT NULL,
          timestamp            integer default 0 NOT NULL,
        PRIMARY KEY  (id)
        ) TYPE=MyISAM";
        if (!$q->run($query)) return;

    # --------------------------------------------------------
    #
    # Set up masks
    #
        xarRegisterMask('ViewXarayatesting','All','xarayatesting','All','All','ACCESS_OVERVIEW');
        xarRegisterMask('ReadXarayatesting','All','xarayatesting','All','All','ACCESS_READ');
        xarRegisterMask('EditXarayatesting','All','xarayatesting','All','All','ACCESS_EDIT');
        xarRegisterMask('ManageXarayatesting','All','xarayatesting','All','All','ACCESS_DELETE');
        xarRegisterMask('AdminXarayatesting','All','xarayatesting','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up privileges
    #
        xarRegisterPrivilege('ManageXarayatesting','All','xarayatesting','All','All','ACCESS_DELETE');
        xarRegisterPrivilege('AdminXarayatesting','All','xarayatesting','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up configuration modvars (general)
    #
            $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'xarayatesting'));
            $module_settings->initialize();

    # --------------------------------------------------------
    #
    # Set up modvars
    #
        xarModVars::set('xarayatesting', 'defaultmastertable', 'xarayatesting_tests');

    # --------------------------------------------------------
    #
    # Set up hooks
    #
        sys::import('xaraya.structures.hooks.observer');
        $observer = new BasicObserver('xarayatesting');
        $subject = new HookSubject('comments');
        $subject->attach($observer);

    # --------------------------------------------------------
    #
    # Create DD objects
    #
        $module = 'xarayatesting';
        $objects = array(
//                       'xarayatesting_tests',
                         );

        if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

        return true;
    }

    function xarayatesting_upgrade()
    {
        return true;
    }

    function xarayatesting_delete()
    {
        // Only change the next line. No need for anything else
        $this_module = 'xarayatesting';

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
    # Delete all DD objects created by this module
    #
        try {
            $dd_objects = unserialize(xarModVars::get($this_module,$this_module . '_objects'));
            foreach ($dd_objects as $key => $value)
                $result = xarMod::apiFunc('dynamicdata','admin','deleteobject',array('objectid' => $value));
        } catch (Exception $e) {}

    # --------------------------------------------------------
    #
    # Remove the categories
    #
        try {
            xarMod::apiFunc('categories', 'admin', 'deletecat',
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