<?php
/**
 * Gallery
 *
 * @package   Xaraya eXtensible Management System
 * @copyright (C) 2006 by Brian McGilligan
 * @license   New BSD License <http://www.abrasiontechnology.com/index.php/page/7>
 * @link      http://www.abrasiontechnology.com/
 *
 * @subpackage Gallery module
 * @author     Brian McGilligan
 */

function gallery_init()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    // Creates all the tables according their field defs
    gallery_db_sync_tables();

    // Set up module variables
    xarModSetVar('gallery', 'name',           'Gallery');
    xarModSetVar('gallery', 'SupportShortURLs', 0);
    xarModSetVar('gallery', 'obfuscate_file_name', true);
    xarModSetVar('gallery', 'enable_lightbox', false);
    xarModSetVar('gallery', 'items_per_page', 10);
    xarModSetVar('gallery', 'files_per_page', 10);
    xarModSetVar('gallery', 'cols_per_page',  2);
    xarModSetVar('gallery', 'file_path',      'Path Goes Here!');
    xarModSetVar('gallery', 'sort',           'album_id') ;
    xarModSetVar('gallery', 'sort_order',     'ASC');

    $new_gallery_success = "Congradulations! Your Album was successfully created.  You can see and modify your own photos, but others will not be able to see them until an administrator approves your album.";
    xarModsetVar('gallery', 'new_album_success', $new_gallery_success);
    $new_file_success = "Your photos were uploaded successfully.";
    xarModsetVar('gallery', 'new_file_success', $new_file_success);

    $result = xarModAPIFunc('gallery', 'admin', 'init_security');
    if( $result == false ){ return false; }

    if (xarCurrentErrorType() !== XAR_NO_EXCEPTION) {
        // if there was an error, make sure to remove the tables
        // so the user can try the install again
        gallery_delete();
        return;
    }

    // Initialisation successful
    return true;
}

function gallery_delete()
{
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    /* Drop the tables */
    $result = $datadict->dropTable($xartable['gallery_albums']);
    if( !$result ){ return false; }
    $result = $datadict->dropTable($xartable['gallery_album_settings']);
    if( !$result ){ return false; }
    $result = $datadict->dropTable($xartable['gallery_files']);
    if( !$result ){ return false; }
    $result = $datadict->dropTable($xartable['gallery_files_linkage']);
    if( !$result ){ return false; }

    // Delete All module vars
    xarModDelAllVars('gallery');

    // Remove Masks and Instances
    xarRemoveMasks('gallery');
    xarRemoveInstances('gallery');

    // Deletion successful
    return true;
}

/**
* upgrade the module from an old version
*/
function gallery_upgrade($oldversion)
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Upgrade dependent on old version number
    switch($oldversion) {
        case '0.8.0':
        case '0.8.1':
        case '0.9.0':

        case '0.9.1':
            $new_gallery_success = "Congradulations! Your Album was successfully created.  You can see and modify your own photos, but others will not be able to see them until an administrator approves your album.";
            xarModsetVar('gallery', 'new_album_success', $new_gallery_success);
            $new_file_success = "Your photos were uploaded successfully.";
            xarModsetVar('gallery', 'new_file_success', $new_file_success);

        case '0.9.2':
            // update field names in tables
            $sql = "ALTER TABLE {$xartable['gallery_albums']} CHANGE gallery_id album_id INTEGER NOT NULL AUTO_INCREMENT";
            $dbconn->Execute($sql);
            $sql = "ALTER TABLE {$xartable['gallery_album_settings']} CHANGE gallery_id album_id INTEGER NOT NULL";
            $dbconn->Execute($sql);
            $sql = "ALTER TABLE {$xartable['gallery_files_linkage']} CHANGE gallery_id album_id INTEGER NOT NULL";
            $dbconn->Execute($sql);

        case '0.9.3':
        case '0.9.4':

        case '0.9.5':
            $result = xarModAPIFunc('gallery', 'admin', 'init_security');
            if( $result == false ){ return false; }

        case '0.9.6':
            // Removes and privileges that may have been created
            xarRemoveMasks('gallery');
            xarRemoveInstances('gallery');

            // obfuscating uploaded file paths is now optional
            xarModSetVar('gallery', 'obfuscate_file_name', true);
            xarModSetVar('gallery', 'enable_lightbox', false);

            gallery_db_sync_tables();
            break;
    }
    return true;
}

function gallery_db_sync_tables()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    $fields = gallery_db_get_fields();

    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($xartable['gallery_albums'], $fields['gallery_albums']);
    if (!$result) {return;}

    $result = $datadict->changeTable($xartable['gallery_album_settings'], $fields['gallery_album_settings']);
    if (!$result) {return;}

    $result = $datadict->changeTable($xartable['gallery_files'], $fields['file']);
    if (!$result) {return;}

    $result = $datadict->changeTable($xartable['gallery_files_linkage'], $fields['linkage']);
    if (!$result) {return;}

    return true;
}


function gallery_db_get_fields()
{
    $fields = array(
        'gallery_albums' => "
            album_id           I    AUTO PRIMARY,
            name           c(50) NotNull DEFAULT '',
            display_name   c(50) NotNull DEFAULT '',
            description       X2 NotNull DEFAULT '',
            status         c(10) NotNull DEFAULT '',
            uid                I NotNull DEFAULT 0,
            created            I NotNull DEFAULT 0,
            modified           I NotNull DEFAULT 0,
            display_order      I NotNull DEFAULT 0
        ",
        'gallery_album_settings' => "
            album_id           I NotNull DEFAULT 0,
            watermark_id       I NotNull DEFAULT 0,
            preview_file  c(255) NotNull DEFAULT '',
            file_quality       I NotNull DEFAULT 100,
            files_per_page     I NotNull DEFAULT 10,
            cols_per_page      I NotNull DEFAULT 2,
            file_width     c(10) NotNull DEFAULT '150px',
            show_date       c(1) NotNull DEFAULT '',
            sort_order     c(20) NotNull DEFAULT 'file_id|ASC'
        ",
        'file' => "
            file_id            I     AUTO PRIMARY,
            name          c(100)  NotNull DEFAULT '',
            summary       c(255)  NotNull DEFAULT '',
            file          c(255)  NotNull DEFAULT '',
            external_id   c(255)  NotNull DEFAULT '',
            status         c(10)  NotNull DEFAULT '',
            created            I  NotNull DEFAULT 0,
            modified           I  NotNull DEFAULT 0,
            file_type      c(25)  NotNull DEFAULT '',
            file_size          I  NotNull DEFAULT 0,
            uid                I  NotNull DEFAULT 0,
            display_order      I  NotNull DEFAULT 0
        ",
        'linkage' => "
            id                 I     AUTO PRIMARY,
            album_id           I  NotNull DEFAULT 0,
            file_id            I  NotNull DEFAULT 0,
            display_order      I  NotNull DEFAULT 0
        "
    );

    return $fields;
}

?>
