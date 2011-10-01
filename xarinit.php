<?php
/**
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 *
 * @subpackage shouter
 * @link http://xaraya.com/index.php/release/236.html
 * @author Neil Whittaker
 */
sys::import('xaraya.tableddl');
/**
 * Initialize the module
 */
function shouter_init()
{
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $shoutertable = $xartable['shouter'];
    $fields = array(
            'shout_id'      => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
            'name'      => array('type' => 'varchar', 'size' => 32, 'null' => false),
            'date'      => array('type' => 'integer', 'null' => FALSE),
            'shout'     => array('type' => 'varchar', 'null' => FALSE,  'size'=>255),
    );
    // make sure to delete existing table if one exists
    $query = xarDBDropTable($xartable['shouter']);
    if (empty($query)) return;

    $query = xarDBCreateTable($shoutertable, $fields);
    if (empty($query)) return;

    $result = &$dbconn->Execute($query);
    if (!$result) return;

    /*
    xarModVars::set('shouter', 'anonymouspost', false);
    xarModVars::set('shouter', 'shoutblockrefresh', 5);
    xarModVars::set('shouter', 'lightrow', 'FFFFFF');
    xarModVars::set('shouter', 'darkrow', 'E0E0E0');
    xarModVars::set('shouter', 'blockwidth', 180);
    xarModVars::set('shouter', 'itemsperpage', 10);
    xarModVars::set('shouter', 'allowsmilies', true);
    xarModVars::set('shouter', 'blockwrap', 19);

    */
    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                 array('modName' => 'shouter',
                       'blockType' => 'shout'))) return;
    // Register hooks:
    // Enable smilies hook for the shouter module
    if (xarModIsAvailable('smilies')) {
        xarModAPIFunc('modules','admin','enablehooks',
                array('callerModName' => 'shouter', 'hookModName' => 'smilies'));
    }

    $instancestable = $xartable['block_instances'];
    $typestable = $xartable['block_types'];
    $query = "SELECT DISTINCT i.xar_title FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'shouter'";
    $instances = array(
                 array('header' => 'Shouter Block Title:',
                       'query' => $query,
                       'limit' => 20
                 )
                 );
    xarDefineInstance('shouter', 'Block', $instances);

    xarRegisterMask('ReadShouterBlock', 'All', 'shouter', 'Block', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ViewShouter', 'All', 'shouter', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadShouter', 'All', 'shouter', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditShouter', 'All', 'shouter', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddShouter', 'All', 'shouter', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('ManageShouter', 'All', 'shouter', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminShouter', 'All', 'shouter', 'Item', 'All:All:All', 'ACCESS_ADMIN');

    return true;
}

/**
 * Delete the module
 */
function shouter_delete()
{
    return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => 'shouter'));
}
?>
