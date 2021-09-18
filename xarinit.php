<?php
/**
 * Mime Module
 *
 * @package modules
 * @subpackage mime module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/999
 * @author Carl Corliss <rabbitt@xaraya.com>
 */

/**
 * initialise the mime module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function mime_init()
{
    $error = false;

    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();

    $fields['mime_type'] = [
        'id'          => ['type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true],
        'name'        => ['type'=>'varchar',  'null'=>false,  'size'=>255],
        'state'       => ['type' => 'integer', 'null' => false, 'default' => 3],
    ];

    $fields['mime_subtype'] = [
        'id'            => ['type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true],
        'name'          => ['type'=>'varchar',  'null'=>false,  'size'=>255],
        'type_id'       => ['type'=>'integer',  'null'=>false],
        'description'   => ['type'=>'varchar',  'null'=>true,  'size'=>255],
        'state'         => ['type' => 'integer', 'null' => false, 'default' => 3],
    ];

    $fields['mime_extension'] = [
        'id'            => ['type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true],
        'subtype_id'    => ['type'=>'integer',  'null'=>false],
        'name'          => ['type'=>'varchar',  'null'=>false,  'size'=>10],
    ];

    $fields['mime_magic'] = [
        'id'         => ['type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true],
        'subtype_id' => ['type'=>'integer',  'null'=>false],
        'value'      => ['type'=>'varchar',  'null'=>false, 'size'=>255],
        'length'     => ['type'=>'integer',  'null'=>false],
        'offset'     => ['type'=>'integer',  'null'=>false],
    ];

    // Create all the tables and, if there are errors
    // just make a note of them for now - we don't want
    // to return right away otherwise we could have
    // some tables created and some not.
    foreach ($fields as $table => $data) {
        $query = xarTableDDL::dropTable($xartable[$table]);
        $result = $dbconn->Execute($query);
        $query = xarTableDDL::createTable($xartable[$table], $data);
        $result = $dbconn->Execute($query);
        if (!$result) {
            $tables[$table] = false;
            $error |= true;
        } else {
            $tables[$table] = true;
            $error |= false;
        }
    }

    // if there were any errors during the
    // table creation, make sure to remove any tables
    // that might have been created
    if ($error) {
        foreach ($tables as $table) {
            $query = xarTableDDL::dropTable($xartable[$table]);
            $result = $dbconn->Execute($query);

            if (!$result) {
                return;
            }
        }
        return false;
    }


    # --------------------------------------------------------
    #
    # Set up masks
    #
    xarMasks::register('EditMime', 'All', 'mime', 'All', 'All', 'ACCESS_EDIT');
    xarMasks::register('AddMime', 'All', 'mime', 'All', 'All', 'ACCESS_ADD');
    xarMasks::register('ManageMime', 'All', 'mime', 'All', 'All', 'ACCESS_DELETE');
    xarMasks::register('AdminMime', 'All', 'mime', 'All', 'All', 'ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up privileges
    #
    xarPrivileges::register('EditMime', 'All', 'mime', 'All', 'All', 'ACCESS_EDIT');
    xarPrivileges::register('AddMime', 'All', 'mime', 'All', 'All', 'ACCESS_ADD');
    xarPrivileges::register('ManageMime', 'All', 'mime', 'All', 'All', 'ACCESS_DELETE');
    xarPrivileges::register('AdminMime', 'All', 'mime', 'All', 'All', 'ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Create DD objects
    #
    $module = 'mime';
    $objects = [
                        'mime_types',
                        'mime_subtypes',
                        'mime_magic',
                        'mime_extensions',
                         ];

    if (!xarMod::apiFunc('modules', 'admin', 'standardinstall', ['module' => $module, 'objects' => $objects])) {
        return;
    }

    # --------------------------------------------------------
    #
    # Set up modvars
    #
//        $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'mime'));
//        $module_settings->initialize();

    xarModVars::set('mime', 'defaultmastertable', 'mime_types');

    // Initialisation successful
    return true;
}

/**
* upgrade the mime module from an old version
*/
function mime_upgrade($oldversion)
{
    // Set up database objects
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $datadict = xarTableDDL::init($dbconn, 'ALTERTABLE');

    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '1.1.0':
            // Upgrade from version 1.1.0
    }

    return true;
}

/**
 *  Uninstall this module
 */

function mime_delete()
{
    $module = 'mime';
    return xarMod::apiFunc('modules', 'admin', 'standarddeinstall', ['module' => $module]);
}
