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
 *//**
 * Standard function to delete a forum
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_adminapi_deletepost($args)
{

    extract($args);
    if (!isset($pid) || !is_numeric($pid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'post ID', 'admin', 'deletepost', 'crispbb');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $post = xarModAPIFunc('crispbb', 'user', 'getpost', array('pid' => $pid));

    if (empty($post['purgereplyurl'])) {
        $errorMsg['message'] = xarML('You do not have the privileges required for this action');
        $errorMsg['return_url'] = xarModURL('crispbb', 'user', 'main');
        $errorMsg['type'] = 'NO_PRIVILEGES';
        $errorMsg['pageTitle'] = xarML('No Privileges');
        xarTPLSetPageTitle(xarVarPrepForDisplay($errorMsg['pageTitle']));
        return xarTPLModule('crispbb', 'user', 'error', $errorMsg);
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $poststable = $xartable['crispbb_posts'];

    // remove post
    $query = "DELETE FROM $poststable WHERE xar_pid = " . $pid;
    $result = &$dbconn->Execute($query,array());
    if (!$result) return;

    $item['module'] = 'crispbb';
    $item['itemtype'] = $post['poststype'];
    $item['itemid'] = $pid;
    xarModCallHooks('item', 'delete', $pid, $item);

    /* Let the calling process know that we have finished successfully */
    return true;
}
?>