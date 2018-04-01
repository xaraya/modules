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
    $error = FALSE;

    //Load Table Maintenance API
    sys::import('xaraya.tableddl');

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();

    $fields['mime_type'] = array(
        'id'          => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
        'name'        => array('type'=>'varchar',  'null'=>FALSE,  'size'=>255),
        'state'       => array('type' => 'integer', 'null' => false, 'default' => 3),
    );

    $fields['mime_subtype'] = array(
        'id'            => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
        'name'          => array('type'=>'varchar',  'null'=>FALSE,  'size'=>255),
        'type_id'       => array('type'=>'integer',  'null'=>FALSE),
        'description'   => array('type'=>'varchar',  'null'=>TRUE,  'size'=>255),
        'state'         => array('type' => 'integer', 'null' => false, 'default' => 3),
    );

    $fields['mime_extension'] = array(
        'id'            => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
        'subtype_id'    => array('type'=>'integer',  'null'=>FALSE),
        'name'          => array('type'=>'varchar',  'null'=>FALSE,  'size'=>10)
    );

    $fields['mime_magic'] = array(
        'id'         => array('type' => 'integer', 'unsigned' => true, 'null' => false, 'increment' => true, 'primary_key' => true),
        'subtype_id' => array('type'=>'integer',  'null'=>FALSE),
        'value'      => array('type'=>'varchar',  'null'=>FALSE, 'size'=>255),
        'length'     => array('type'=>'integer',  'null'=>FALSE),
        'offset'     => array('type'=>'integer',  'null'=>FALSE)
    );

    // Create all the tables and, if there are errors
    // just make a note of them for now - we don't want
    // to return right away otherwise we could have
    // some tables created and some not.
    foreach ($fields as $table => $data) {
        $query = xarDBDropTable($xartable[$table]);
        $result = $dbconn->Execute($query);
        $query = xarDBCreateTable($xartable[$table], $data);
        $result = $dbconn->Execute($query);
        if (!$result) {
            $tables[$table] = FALSE;
            $error |= TRUE;
        } else {
            $tables[$table] = TRUE;
            $error |= FALSE;
        }
    }

    // if there were any errors during the
    // table creation, make sure to remove any tables
    // that might have been created
    if ($error) {
        foreach ($tables as $table) {
            $query = xarDBDropTable($xartable[$table]);
            $result = $dbconn->Execute($query);

            if(!$result)
                return;
        }
        return FALSE;
    }


    # --------------------------------------------------------
    #
    # Set up masks
    #
        xarRegisterMask('EditMime','All','mime','All','All','ACCESS_EDIT');
        xarRegisterMask('AddMime','All','mime','All','All','ACCESS_ADD');
        xarRegisterMask('ManageMime','All','mime','All','All','ACCESS_DELETE');
        xarRegisterMask('AdminMime','All','mime','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up privileges
    #
        xarRegisterPrivilege('EditMime','All','mime','All','All','ACCESS_EDIT');
        xarRegisterPrivilege('AddMime','All','mime','All','All','ACCESS_ADD');
        xarRegisterPrivilege('ManageMime','All','mime','All','All','ACCESS_DELETE');
        xarRegisterPrivilege('AdminMime','All','mime','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Create DD objects
    #
        $module = 'mime';
        $objects = array(
                        'mime_types',
                        'mime_subtypes',
                        'mime_magic',
                        'mime_extensions',
                         );

        if(!xarModAPIFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

    # --------------------------------------------------------
    #
    # Set up modvars
    #
//        $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'mime'));
//        $module_settings->initialize();

        xarModVars::set('mime', 'defaultmastertable','mime_types');

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
    $datadict = xarDBNewDataDict($dbconn, 'ALTERTABLE');

    // Upgrade dependent on old version number
    switch($oldversion) {
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
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $module));
}

?>
