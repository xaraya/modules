<?php
/**
 * MIME initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage mime
 * @author Carl P. Corliss
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
    xarDBLoadTableMaintenanceAPI();

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $fields['mime_type'] = array(
        'xar_mime_type_id'          => array('type'=>'integer',  'null'=>FALSE, 'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_mime_type_name'        => array('type'=>'varchar',  'null'=>FALSE,  'size'=>255),
    );

    $fields['mime_subtype'] = array(
        'xar_mime_type_id'          => array('type'=>'integer',  'null'=>FALSE),
        'xar_mime_subtype_id'       => array('type'=>'integer',  'null'=>FALSE, 'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_mime_subtype_name'     => array('type'=>'varchar',  'null'=>FALSE,  'size'=>255),
        'xar_mime_subtype_desc'     => array('type'=>'varchar',  'null'=>TRUE,  'size'=>255),
    );

    $fields['mime_extension'] = array(
        'xar_mime_subtype_id'       => array('type'=>'integer',  'null'=>FALSE),
        'xar_mime_extension_id'     => array('type'=>'integer',  'null'=>FALSE,  'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_mime_extension_name'   => array('type'=>'varchar',  'null'=>FALSE,  'size'=>10)
    );

    $fields['mime_magic'] = array(
        'xar_mime_subtype_id'       => array('type'=>'integer',  'null'=>FALSE),
        'xar_mime_magic_id'         => array('type'=>'integer',  'null'=>FALSE, 'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_mime_magic_value'      => array('type'=>'varchar',  'null'=>FALSE, 'size'=>255),
        'xar_mime_magic_length'     => array('type'=>'integer',  'null'=>FALSE),
        'xar_mime_magic_offset'     => array('type'=>'integer',  'null'=>FALSE)
    );

    // Create all the tables and, if there are errors
    // just make a note of them for now - we don't want
    // to return right away otherwise we could have
    // some tables created and some not.
    foreach ($fields as $table => $data) {
        $query = xarDBCreateTable($xartable[$table], $data);

        $result =& $dbconn->Execute($query);
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
            $result =& $dbconn->Execute($query);

            if(!$result)
                return;
        }
        return FALSE;
    }

    if (!file_exists('modules/mime/xarincludes/mime.magic.php')) {

        $msg = xarML('Could not open #(1) for inclusion', 'modules/mime/xarincludes/mime.magic.php');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'MISSING_FILE', new SystemException($msg));

        mime_delete();
        return FALSE;
    } else {
        include('modules/mime/xarincludes/mime.magic.php');

        if (!isset($mime_list) || empty($mime_list)) {
            $msg = xarML('Missing mime magic list! Please report this as a bug.');
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'MISSING_MIME_MAGIC_LIST', new SystemException($msg));

            mime_delete();
            return FALSE;
        }

        xarModAPIFunc('mime','user','import_mimelist', array('mimeList' => $mime_list));
    }


    // Initialisation successful
    return TRUE;
}

/**
 *  Delete all tables, unregister hooks, remove
 *  priviledge instances, and anything else related to
 *  this module
 */




function mime_delete()
{
    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Get database information
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    // Delete tables
    $queries[0] = xarDBDropTable($xartable['mime_type']);
    $queries[1] = xarDBDropTable($xartable['mime_subtype']);
    $queries[2] = xarDBDropTable($xartable['mime_extension']);
    $queries[3] = xarDBDropTable($xartable['mime_magic']);

    foreach( $queries as $query) {
        $result =& $dbconn->Execute($query);
    }

    // Deletion successful
    return TRUE;

}

/**
* upgrade the mime module from an old version
*/
function mime_upgrade($oldversion)
{
    // Set up database objects
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.0.0':
            // fall through to the next upgrade
        case '0.1.0':
            mime_init();
            include_once "modules/mime/xarincludes/mime.magic.php";
            xarModAPIFunc('mime','user','import_mimelist', array('mimeList' => $mime_list));
            // fall through to the next upgrade
        case '0.1.1':
            // fall through to the next upgrade
        case '0.2.0':
            // Code to upgrade from version 2.0 goes here
            // fall through to the next upgrade
        case '0.2.5':
            // Code to upgrade from version 2.5 goes here
        case '1.0.0':
            // Upgrade from version 1.0.0 to 1.1.0

            // Add a description column to the mime_subtype table
            $result = $datadict->changeTable($xartable['mime_subtype'], 'xar_mime_subtype_desc C(255) DEFAULT NULL');
            if (!$result) {xarErrorHandled();}
    }

    return true;
}

?>
