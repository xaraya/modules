<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
/**
 * create a new headline
 * @param $args['url'] url of the item
 * @return int headline ID on success, false on failure
 */
function headlines_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if (!isset($url)) {
        $msg = xarML('Invalid Parameter Count in #(3)_#(1)_#(2)', 'admin', 'create', 'Headlines');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // Security Check
    if(!xarSecurityCheck('AddHeadlines')) return;

    $order = xarModAPIFunc('headlines', 'user', 'countitems');

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $headlinestable = $xartable['headlines'];
    // Get next ID in table
    $nextId = $dbconn->GenId($headlinestable);
    // Add item
    $query = "INSERT INTO $headlinestable (
              xar_hid,
              xar_url,
              xar_order)
            VALUES (
              ?,
              ?,
              ?)";

    $bindvars = array($nextId, $url, $order);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $hid = $dbconn->PO_Insert_ID($headlinestable, 'xar_hid');
    // Let any hooks know that we have created a new link

    $item['module']='headlines';
    $item['itemid']=$hid;
    xarModCallHooks('item', 'create', $hid, $item);

    // Return the id of the newly created link to the calling process
    return $hid;
}
?>