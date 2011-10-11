<?php
/**
 * Ephemerids Module
 *
 * @package modules
 * @subpackage ephemerids module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/15.html
 * @author Volodymyr Metenchuk
 */
/**
 * init ephemerids module
 */
sys::import('xaraya.tableddl');

function ephemerids_init()
{
    // Get database information
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    // Create tables
    $ephemtable = $xartable['ephem'];

    // Define the table structure
    $fields = array(
        'xar_eid'=>array('type'=>'integer', 'unsigned' => true,'null'=>FALSE,'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_tid'=>array('type'=>'integer', 'unsigned' => true,'size'=>4,'null'=>FALSE,'default'=>'1'),
        'xar_did'=>array('type'=>'integer', 'unsigned' => true,'size'=>'small','size'=>2,'null'=>FALSE,'default'=>'0'),
        'xar_mid'=>array('type'=>'integer', 'unsigned' => true,'size'=>'small','size'=>2,'null'=>FALSE,'default'=>'0'),
        'xar_yid'=>array('type'=>'integer', 'unsigned' => true,'size'=>4,'null'=>FALSE,'default'=>'0'),
        'xar_content'=>array('type'=>'text','null'=>FALSE),
        'xar_elanguage'=>array('type'=>'varchar','size'=>32,'null'=>FALSE)
    );

    // Create the Table
    $query = xarDBCreateTable($ephemtable,$fields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Set up module variables
    xarModVars::set('ephemerids', 'itemsperpage', 20);

    // Register blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'ephemerids',
                             'blockType'=> 'ephem'))) return;

    // Register Masks
    xarRegisterMask('OverviewEphemerids','All','ephemerids','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadEphemerids','All','ephemerids','All','All','ACCESS_READ');
    xarRegisterMask('EditEphemerids','All','ephemerids','All','All','ACCESS_EDIT');
    xarRegisterMask('AddEphemerids','All','ephemerids','All','All','ACCESS_ADD');
    xarRegisterMask('ManageEphemerids','All','ephemerids','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminEphemerids','All','ephemerids','All','All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * upgrade
 */
function ephemerids_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '2.0.0':
            break;
    }
    return true;
}

/**
 * delete the ephemerids module
 */
function ephemerids_delete()
{
    return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => 'epheremids'));
}

?>