<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
 /**
 * Create a new forum
 *
 * This is a standard adminapi function to create a component itemtype
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @param  string   $args['fid']        forum id
 * @param  string   $args['component']  crispbb component (forum|topics|posts)
 * @return int itemtype id on success, false on failure
 * @throws BAD_PARAM, DATABASE_ERROR
 */
function crispbb_adminapi_createitemtype($args)
{
    extract($args);
    $invalid = array();
    $components = array('forum', 'topics', 'posts');
    if (!isset($fid) || !is_numeric($fid)) $invalid[] = 'fid';
    if (empty($component) || !in_array($component, $components)) $invalid[] = 'component';

    if (count($invalid) > 0) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array(join(', ', $invalid), 'admin', 'createitemtype', 'crispBB');
        throw new BadParameterException($vars, $msg);
        return;
    }

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $itemtypestable = $xartable['crispbb_itemtypes'];
    $nextId = $dbconn->GenId($itemtypestable);
    $query = "INSERT INTO $itemtypestable (
        id,
        fid,
        component
        )
        VALUES (?,?,?)";
    $bindvars = array($nextId, $fid, $component);
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;
    $itemtype = $dbconn->PO_Insert_ID($itemtypestable, 'id');

    return $itemtype;
}
?>