<?php
/**
 * File: $Id: xarinit.php,v 1.1.1.1 2005/11/28 18:55:21 curtis Exp $
 *
 * Bible initialization functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf
 */

function bible_init()
{
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();

    // Texts table
    $texttable = $xartable['bible_texts'];

    $fields = array('xar_tid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_sname' => array('type' => 'varchar', 'size' => 32, 'null' => false),
        'xar_lname' => array('type' => 'varchar', 'size' => 128, 'null' => false),
        'xar_file' => array('type' => 'varchar', 'size' => 128, 'null' => false),
        'xar_md5' => array('type' => 'varchar', 'size' => 32, 'null' => false),
        'xar_config_exists' => array('type' => 'integer', 'size' => 1, 'null'=> false, 'default'=> '0'),
        'xar_md5_config' => array('type' => 'varchar', 'size' => 32, 'null' => false),
        'xar_state' => array('type' => 'integer', 'size' => 'tiny', 'null' => false),
        'xar_type' => array('type' => 'integer', 'size' => 'tiny', 'null' => false)
        );

    // create the table
    $query = xarDBCreateTable($texttable, $fields);
    if (empty($query)) return; // throw back
    $result = $dbconn->Execute($query);
    if (!$result) return;

    // Book aliases table
    $aliastable = $xartable['bible_aliases'];

    $fields = array('xar_aid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_sword' => array('type' => 'varchar', 'size' => 32, 'null' => false),
        'xar_display' => array('type' => 'varchar', 'size' => 32, 'null' => false),
        'xar_aliases' => array('type' => 'text', 'null' => false)
        );

    // create the table
    $query = xarDBCreateTable($aliastable, $fields);
    if (empty($query)) return; // throw back
    $result = $dbconn->Execute($query);
    if (!$result) return;

    // fill aliases table with data
    $sword = array('Genesis', 'Exodus', 'Leviticus', 'Numbers', 'Deuteronomy', 'Joshua', 'Judges', 'Ruth', 'I Samuel', 'II Samuel', 'I Kings', 'II Kings', 'I Chronicles', 'II Chronicles', 'Ezra', 'Nehemiah', 'Esther', 'Job', 'Psalms', 'Proverbs', 'Ecclesiastes', 'Song of Solomon', 'Isaiah', 'Jeremiah', 'Lamentations', 'Ezekiel', 'Daniel', 'Hosea', 'Joel', 'Amos', 'Obadiah', 'Jonah', 'Micah', 'Nahum', 'Habakkuk', 'Zephaniah', 'Haggai', 'Zechariah', 'Malachi', 'Matthew', 'Mark', 'Luke', 'John', 'Acts', 'Romans', 'I Corinthians', 'II Corinthians', 'Galatians', 'Ephesians', 'Philippians', 'Colossians', 'I Thessalonians', 'II Thessalonians', 'I Timothy', 'II Timothy', 'Titus', 'Philemon', 'Hebrews', 'James', 'I Peter', 'II Peter', 'I John', 'II John', 'III John', 'Jude', 'Revelation of John');

    $display = array(xarML('Genesis'), xarML('Exodus'), xarML('Leviticus'), xarML('Numbers'), xarML('Deuteronomy'), xarML('Joshua'), xarML('Judges'), xarML('Ruth'), xarML('1 Samuel'), xarML('2 Samuel'), xarML('1 Kings'), xarML('2 Kings'), xarML('1 Chronicles'), xarML('2 Chronicles'), xarML('Ezra'), xarML('Nehemiah'), xarML('Esther'), xarML('Job'), xarML('Psalms'), xarML('Proverbs'), xarML('Ecclesiastes'), xarML('Song of Solomon'), xarML('Isaiah'), xarML('Jeremiah'), xarML('Lamentations'), xarML('Ezekiel'), xarML('Daniel'), xarML('Hosea'), xarML('Joel'), xarML('Amos'), xarML('Obadiah'), xarML('Jonah'), xarML('Micah'), xarML('Nahum'), xarML('Habakkuk'), xarML('Zephaniah'), xarML('Haggai'), xarML('Zechariah'), xarML('Malachi'), xarML('Matthew'), xarML('Mark'), xarML('Luke'), xarML('John'), xarML('Acts'), xarML('Romans'), xarML('1 Corinthians'), xarML('2 Corinthians'), xarML('Galatians'), xarML('Ephesians'), xarML('Philippians'), xarML('Colossians'), xarML('1 Thessalonians'), xarML('2 Thessalonians'), xarML('1 Timothy'), xarML('2 Timothy'), xarML('Titus'), xarML('Philemon'), xarML('Hebrews'), xarML('James'), xarML('1 Peter'), xarML('2 Peter'), xarML('1 John'), xarML('2 John'), xarML('3 John'), xarML('Jude'), xarML('Revelation'));

    $aliases = array(xarML('gen'), xarML('ex,exod'), xarML('lev'), xarML('num'), xarML('deut,dt'), xarML('josh'), xarML('judg,jdg'), xarML('rut,ru'), xarML('1sam'), xarML('2sam'), xarML('1kgs,1kg,1 kg'), xarML('2kgs,2kg,2 kg'), xarML('1chron,1 chron,1chr,1 chr'), xarML('2chron,2 chron,2chr,2 chr'), xarML('ez,ezr'), xarML('neh'), xarML('es,est'), xarML('job'), xarML('ps,psalm'), xarML('pro,prov,pr'), xarML('eccl,ecc'), xarML('ss'), xarML('isa'), xarML('jer'), xarML('lam'), xarML('ez,ezk'), xarML('dan'), xarML('hos'), xarML('joel'), xarML('am'), xarML('obad'), xarML('jnh'), xarML('mic'), xarML('nah'), xarML('hab'), xarML('zeph'), xarML('hag'), xarML('zech'), xarML('mal'), xarML('mt,matt'), xarML('mk'), xarML('lk'), xarML('jn'), xarML('ac'), xarML('rom'), xarML('1cor,1 cor'), xarML('2cor,2 cor'), xarML('gal'), xarML('eph'), xarML('phil'), xarML('col'), xarML('1thess,1 thess,1th,1 th'), xarML('2thess,2 thess,2th,2 th'), xarML('1tim,1 tim,1ti,1 ti'), xarML('2tim,2tim,2ti,2 ti'), xarML('ti,tit'), xarML('phil'), xarML('heb'), xarML('jas'), xarML('1pet,1 pet'), xarML('2pet,2 pet'), xarML('1jo,1jn'), xarML('2jo,2jn'), xarML('3jo,3jn'), xarML('jud'), xarML('rev,apoc'));

    $query = "INSERT INTO $aliastable
              (xar_aid, xar_sword, xar_display, xar_aliases)
              VALUES\n";
    $bindvars = $queries = array();
    for ($i = 0; $i < 66; $i++) {
        $queries[] = "('', ?, ?, ?)";
        $bindvars[] = $sword[$i];
        $bindvars[] = $display[$i];
        $bindvars[] = $aliases[$i];
    }
    $query .= join(', ', $queries);

    // insert the data
    $result = $dbconn->Execute($query, $bindvars);
    if (!$result) return;

    // set module variables
    xarModSetVar('bible', 'admin_textsperpage', 10);
    xarModSetVar('bible', 'user_searchversesperpage', 10);
    xarModSetVar('bible', 'user_lookupversesperpage', 20);
    xarModSetVar('bible', 'user_wordsperpage', 40);
    xarModSetVar('bible', 'textdir', xarPreCoreGetVarDirPath().'/bible');
    xarModSetVar('bible', 'SupportShortURLs', 0);
    xarModSetVar('bible', 'altdb', 0);
    xarModSetVar('bible', 'altdbtype', 'mysql');
    xarModSetVar('bible', 'altdbhost', 'localhost');
    xarModSetVar('bible', 'altdbname', '');
    xarModSetVar('bible', 'altdbuname', '');
    xarModSetVar('bible', 'altdbpass', '');

    // Register blocks
    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
            array('modName' => 'bible', 'blockType' => 'random'))) return;

    // define regular instances
    $query1 = "SELECT DISTINCT xar_sname FROM " . $texttable;
    $query2 = "SELECT DISTINCT xar_tid FROM " . $texttable;
    $instances = array(
        array('header' => 'Text Short Name:',
            'query' => $query1,
            'limit' => 20
            ),
        array('header' => 'Text ID:',
            'query' => $query2,
            'limit' => 20
            )
        );
    xarDefineInstance('bible', 'Text', $instances);

    // block instances
    $instancestable = $xartable['block_instances'];
    $typestable = $xartable['block_types'];
    $query = "SELECT DISTINCT i.xar_title FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'bible'";
    $instances = array(
        array('header' => 'Bible Block Title:',
            'query' => $query,
            'limit' => 20
            )
        );
    xarDefineInstance('bible', 'Block', $instances);

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */
    xarRegisterMask('ReadBibleBlock', 'All', 'bible', 'Block', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ViewBible',   'All', 'bible', 'Item', 'All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadBible',   'All', 'bible', 'Item', 'All:All', 'ACCESS_READ');
    xarRegisterMask('EditBible',   'All', 'bible', 'Item', 'All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddBible',    'All', 'bible', 'Item', 'All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteBible', 'All', 'bible', 'Item', 'All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminBible',  'All', 'bible', 'Item', 'All:All', 'ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * upgrade the bible module from an old version
 * This function can be called multiple times
 */
function bible_upgrade($oldversion)
{
    // Update successful
    return true;
}

/**
 * delete the bible module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function bible_delete()
{
    /**
     * Delete tables
     *
     * This module permits text tables to be stored in an alternate
     * database.  So we can't just do a simple delete of all Bible
     * tables.  We need to do two separate deletes, one inside each DB.
     */

    // get db setup
    $dbconn = xarDBGetConn();
    $textsdbconn = xarModAPIFunc('bible', 'user', 'getdbconn');
    $xartable = xarDBGetTables();

    // drop all text tables we can find
    $texts = xarModAPIFunc('bible', 'user', 'getall');
    $tables = array();
    foreach ($texts as $tid => $text) {
        $tables[] = "$xartable[bible_text]_$tid";
    }
    $query = "DROP TABLE IF EXISTS " . join(', ', $tables);
    $textsdbconn->Execute($query);
    if ($textsdbconn->ErrorNo()) return;

    // now drop the Bible tables in Xar's DB
    $tables = array($xartable['bible_texts'],
                    $xartable['bible_aliases']);
    $query = "DROP TABLE IF EXISTS " . join(', ', $tables);
    $dbconn->Execute($query);
    if ($dbconn->ErrorNo()) return;

    // Remove module variables, masks, and instances
    xarModDelAllVars('bible');
    xarRemoveMasks('bible');
    xarRemoveInstances('bible');

    // Deletion successful
    return true;
}

?>
