<?php
/**
 * Example Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Initialise the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance. It holds all the installation routines and sets the variables used
 * by this module. This function is the place to create you database structure and define
 * the privileges your module uses.
 *
 * @author Example Module Development Team
 * @param none
 * @return bool true on success of installation
 */
function example_init()
{
    /* Get database setup - note that both xarDBGetConn() and xarDBGetTables()
     * return arrays but we handle them differently. For xarDBGetConn()
     * we currently just want the first item, which is the official
     * database handle. For xarDBGetTables() we want to keep the entire
     * tables array together for easy reference later on
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table definitions you
     * are using - $table doesn't cut it in more complex modules
     */
    $exampletable = $xartable['example'];

    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    /* Define the table structure in a string, each field and it's description
     * separated by a comma. The key for the element is the physical field name.
     * Each field descripton contains other data specifying the
     * data type and associated parameters
     */

     /* Old method of specifying fields and table creation  - deprecated
       xarDBLoadTableMaintenanceAPI();

       $fields = array('xar_exid'   => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
                    'xar_name'   => array('type' => 'varchar', 'size' => 32,      'null' => false),
                    'xar_number' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0')
        );
    */
    $fields = "xar_exid      I         AUTO       PRIMARY,
               xar_name      C(100)    Null,
               xar_number    I4        NotNull    DEFAULT 0
              ";

    /* C:  Varchar, capped to 255 characters. To prevent SQL errors, set a size when using C(sizeint)
       X:  Larger varchar, capped to 4000 characters
       XL: For Oracle, returns CLOB, otherwise the largest varchar size.
       C2: Multibyte varchar
       X2: Multibyte varchar (largest size)
       B:  BLOB (binary large object)
       D:  Date
       T:  Datetime or Timestamp
       L:  Integer field for storing booleans (fails on PostgreSQL, use I1 instead)
       I:  Integer (mapped to I4)
       I1: 1-byte integer
       I2: 2-byte integer
       I4: 4-byte integer
       I8: 8-byte integer
       F:  Floating point number
       N:  Numeric or decimal number
    */
    /*
       AUTO          For autoincrement numbers and sets NOTNULL also.
       KEY           Primary key field. Sets NOTNULL also.
       PRIMARY       Same as KEY.
       DEFAULT       The default value. Character strings are auto-quoted unless
                        the string begins and ends with spaces, eg ' SYSDATE '.
       NULL          Let the field be null.
       NOTNULL       If field is not null. (NOTNULL DEFAULT '' fails on PostgreSQL)
       DEFDATE       Set default value to call function to get today's date.
       DEFTIMESTAMP  Set default to call function to get today's datetime.
       NOQUOTE       Prevents autoquoting of default string values.
       CONSTRAINTS   Additional constraints defined at end of field definition.
    */

    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($exampletable, $fields);
    if (!$result) {return;}

    /* If and as necessary create indexes for your tables */
    /*
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_example_number',
        $exampletable,
        'xar_number'
    );
    if (!$result) {return;}
    */

    /* If Categories API loaded and available, generate proprietary
     * module master category if (cid) and child category ids (subcids)
     */
    if (xarModIsAvailable('categories')) {
        $examplecid = xarModAPIFunc('categories',
            'admin',
            'create',
            Array('name' => 'examples',
                'description' => 'Example Categories',
                'parent_id' => 0));
        /* Store the generated master category id and the number of possible categories
         * Note: you can have more than 1 mastercid (cfr. articles module)
         */
        xarModSetVar('example', 'number_of_categories', 1);
        xarModSetVar('example', 'mastercids', $examplecid);
        $examplecategories = array();
        $examplecategories[] = array('name' => "one",
            'description' => "description one");
        $examplecategories[] = array('name' => "two",
            'description' => "description two");
        $examplecategories[] = array('name' => "three",
            'description' => "description three");
        foreach($examplecategories as $subcat) {
            $examplesubcid = xarModAPIFunc('categories',
                'admin',
                'create',
                Array('name' => $subcat['name'],
                    'description' =>
                    $subcat['description'],
                    'parent_id' => $examplecid));
        }
    }
    /* Set up an initial value for a module variable. Note that all module
     * variables should be initialised with some value in this way rather
     * than just left blank, this helps the user-side code and means that
     * there doesn't need to be a check to see if the variable is set in
     * the rest of the code as it always will be
     */
    xarModSetVar('example', 'bold', 0);
    xarModSetVar('example', 'itemsperpage', 10);
    /* If your module supports short URLs, the website administrator should
     * be able to turn it on or off in your module administration.
     * Use the standard module var name for short url support.
     */
    xarModSetVar('example', 'SupportShortURLs', 0);

    /* Register Block types (this *should* happen at activation/deactivation) */
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'example',
                'blockType' => 'others'))) return;
    /* Register blocks */
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'example',
                'blockType' => 'first'))) return;

    /* Register our hooks that we are providing to other modules. The example
     * module shows an example hook in the form of the user menu.
     */
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
                            'example', 'user', 'usermenu')) {
        return false;
    }
    /* Hooking this module to Roles. Roles calls this hook when displaying the
     * User Account page.
     */
    if (xarModIsAvailable('roles')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'roles', 'hookModName' => 'example'));
    }
    /* Example provides a search hook too. It is registered and hooked during
     * initialization too in example_upgrade(). The upgrade function is called
     * from this function to avoid duplicate code.
     */

    /*
     * Define instances for this module
     * Format is
     * xarDefineInstance($module,        module name, 'example' in this case
     *                   $type,          component name, mostly 'Item'. If the
     *                                   module has blocks mostly 'Block' is used
     *                                   Do not use 'All' or 'ALL' as component!
     *                   $query,         And SQL query or a function to get the
     *                                   items to check against.
     *                   $propagate=0,
     *                   $table2='',
     *                   $childId='',
     *                   $parentId='',
     *                   $description='' A valuable description
     *                  )
     *
     * Instance definitions serve two purposes:
     * 1. The define "filters" that are added to masks at runtime, allowing us to set
     *    security checks over single objects or groups of objects
     * 2. They generate dropdowns the UI uses to present the user with choices when
     *    definng or modifying privileges.
     * For each component we need to tell the system how to generate
     * a list (dropdown) of all the component's instances.
     * The first field of the selected item is stored as instance parameter.
     * The optional second field delivers a description for the list item.
     * In addition, we add a header which will be displayed for greater clarity, and a number
     * (limit) which defines the maximum number of rows a dropdown can have. If the number of
     * instances is greater than the limit (e.g. registered users), the UI instead presents an
     * input field for manual input, which is then checked for validity.
     */
    $query1 = "SELECT DISTINCT xar_name FROM " . $exampletable;
    $query2 = "SELECT DISTINCT xar_number FROM " . $exampletable;
    $query3 = "SELECT DISTINCT xar_exid, xar_name FROM " . $exampletable;
    $instances = array(
        array('header' => 'Example Name:',
            'query' => $query1,
            'limit' => 20
            ),
        array('header' => 'Example Number:',
            'query' => $query2,
            'limit' => 20
            ),
        array('header' => 'Example ID:',
            'query' => $query3,
            'limit' => 20
            )
        );
    xarDefineInstance('example', 'Item', $instances);
    /* You can also use some external "wizard" function to specify instances
     * You will need to provide the wizard function in admin_privileges :

      $instances = array(
          array('header' => 'external', // this keyword indicates an external "wizard"
                'query'  => xarModURL('example','admin','privileges',array('foo' =>'bar')),
                'limit'  => 0
          )
      );
      xarDefineInstance('example', 'Item', $instances);

     */
    $instancestable = $xartable['block_instances'];
    $typestable = $xartable['block_types'];
    $query = "SELECT DISTINCT i.xar_id, i.xar_name FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'example'";
    $instances = array(
        array('header' => 'Example Block ID:',
            'query' => $query,
            'limit' => 20
            )
        );
    xarDefineInstance('example', 'Block', $instances);

    /*
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     * These masks are used in the module for the security checks
     */
    /* First for the blocks */
    xarRegisterMask('ReadExampleBlock', 'All', 'example', 'Block', 'All', 'ACCESS_OVERVIEW');
    /* Then for all operations */
    xarRegisterMask('ViewExample',   'All', 'example', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadExample',   'All', 'example', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditExample',   'All', 'example', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddExample',    'All', 'example', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteExample', 'All', 'example', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminExample',  'All', 'example', 'Item', 'All:All:All', 'ACCESS_ADMIN');

    /* This example_init function brings our module to version 1.0 and then calls
     * the upgrades for the rest of the initialisation. This avoids duplicate
     * code in init and upgrade parts of this file.
     */
    return example_upgrade('1.0');
}

/**
 * Upgrade the module from an old version
 *
 * This function can be called multiple times. It holds all the routines for each version
 * of the module that are necessary to upgrade to a new version. It is very important to keep the
 * initialisation and the upgrade compatible with eachother.
 *
 * @author Example Module Development Team
 * @param string oldversion. This function takes the old version that is currently stored in the module db
 * @return bool true on succes of upgrade
 * @throws mixed This function can throw all sorts of errors, depending on the functions present
                 Currently it can raise database errors.
 */
function example_upgrade($oldversion)
{
    /* Upgrade dependent on old version number */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    switch ($oldversion) {
        case '0.5':
            /* Version 0.5 didn't have a 'number' field, it was added
             * in version 1.0
             * Get database setup - note that both xarDBGetConn() and xarDBGetTables()
             * return arrays but we handle them differently. For xarDBGetConn()
             * we currently just want the first item, which is the official
             * database handle. For xarDBGetTables() we want to keep the entire
             * tables array together for easy reference later on
             * This code could be moved outside of the switch statement if
             * multiple upgrades need it (as it is in this case)
             */
            //$dbconn =& xarDBGetConn();
            //$xartable =& xarDBGetTables();

            /* It's good practice to name the table and column definitions you
             * are getting - $table and $column don't cut it out from more complex
             * modules
             * This code could be moved outside of the switch statement if
             * multiple upgrades need it
             */
            $exampletable = $xartable['example'];
            /* Add a column to the table */
            $result = $datadict->ChangeTable(
                $exampletable, 'xar_number I NotNull Default 0'
            );
            if (!$result) return;
            /* At the end of the successful completion of this function you can
             * recurse the upgrade to handle any other upgrades that need
             * to be done. In normal cases this is not necessary, as the switch
             * will continue with the next step until it hits a break.
             * return example_upgrade('1.0.0');
             */
        case '1.0':
             /* Previously one was allowed to use two digit version numbers
              * You are adviced to use three digits in all next versions of your module
              * We still need to catch all possible versions, as 1.0 is not the same as 1.0.0
              */
             /* The init function brings the module only to version 1.0 and then
              * calls the upgrade from here. So all following code is done on 
              * initialization too.
              */
        case '1.0.0':
            /* Code to upgrade from version 1.0.0 goes here */
            /* Register search hook and hooking this module to Search.*/
            if (!xarModRegisterHook('item', 'search', 'GUI', 'example', 'user', 'search')) {
               return false;
            }
            if (xarModIsAvailable('search')) {
                xarModAPIFunc('modules','admin','enablehooks',
                              array('callerModName' => 'search', 'hookModName' => 'example'));
            }
            /* If you provide short URL encoding functions you might want to also
             * provide module aliases and have them set in the module's administration.
             * Use the standard module var names for useModuleAlias and aliasname.
             */
            xarModSetVar('example', 'useModuleAlias',false);
            xarModSetVar('example','aliasname','');
        case '1.5.0':
          /* Redefine the block_instances on ID rather than Title.
             Title is a displayable text in the user gui and can also be translated */
          $instancestable = $xartable['block_instances'];
          $typestable = $xartable['block_types'];
          $query = "SELECT DISTINCT i.xar_id, i.xar_name FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'example'";
          $instances = array(
             array('header' => 'Example Block ID:',
             'query' => $query,
             'limit' => 20
               )
        );
        xarDefineInstance('example', 'Block', $instances);

        case '1.5.1': /* current version */
            /* Code to upgrade from version 1.5.0 goes here */
            /* We break out now, being at the end of the upgrade process */
            break;
    }
    /* Update successful */
    return true;
}

/**
 * Delete the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 * @author Example Module Development Team
 * @param none
 * @return bool true on succes of deletion
 */
function example_delete()
{
    /* Get database setup - note that both xarDBGetConn() and xarDBGetTables()
     * return arrays but we handle them differently. For xarDBGetConn()
     * we currently just want the first item, which is the official
     * database handle. For xarDBGetTables() we want to keep the entire
     * tables array together for easy reference later on
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $exampletable = $xartable['example'];
    /* Get a data dictionary object with item create and delete methods */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    /* Drop the example tables */
    $result = $datadict->dropTable($exampletable);

    /* Remove any module aliases before deleting module vars
     * This Assumes one module alias in this case
     */
    $aliasname = xarModGetVar('example','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias =='example')){
        xarModDelAlias($aliasname,'example');
    }

    /* Delete any module variables */
    xarModDelAllVars('example');

    /* UnRegister all blocks that the module uses*/
    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'example',
                'blockType' => 'first'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'example',
                'blockType' => 'others'))) return;

    /* Unregister each of the hooks that have been created */
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
            'example', 'user', 'usermenu')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'search', 'GUI',
                              'example', 'user', 'search')) {
        return false;
    }
    /* Remove Masks and Instances
     * These functions remove all the registered masks and instances of a module
     * from the database. This is not strictly necessary, but it's good housekeeping.
     */
    xarRemoveMasks('example');
    xarRemoveInstances('example');

    /* Category deletion?
     *
     * Categories can be used in more than one module.
     * The categories originally created for this module could also have been used
     * for other modules. If we delete the categories then we must be sure that
     * no other modules are currently using them.
     */

    /* Deletion successful*/
    return true;
}
?>
