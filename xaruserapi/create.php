<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
//Psspl: modifided the code for post anonymously 

sys::import('modules.messages.xarincludes.defines');

function messages_userapi_create( $args )
{
    extract($args);

    if (!isset($subject)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'subject', 'userapi', 'create', 'messages');
        throw new Exception($msg);
    }

    if (!isset($body)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'body', 'userapi', 'create', 'messages');
        throw new Exception($msg);
    }

    if (!isset($recipient)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'recipient', 'userapi', 'create', 'messages');
        throw new Exception($msg);
    }

    $author_status = 2;
    if (!isset($draft) || $draft != true) {
        $recipient_status = 0;
    } else {
        $recipient_status = 1;
    }    

    if (!isset($pid) || empty($pid)) {
        $pid = 0;
    }

    if (!isset($author)) {
        $author = xarUserGetVar('id');
    }

    if (!isset($recipient) || empty($recipient)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                                 'recipient', 'userapi', 'create', 'messages');
        throw new BadParameterException($msg);
    }

    // check the authorisation key
    if (!xarSecConfirmAuthKey()) return; // throw back

    //Psspl:Modifided the code for postanon_to field.   
    if(!isset($postanon_to)){
        $postanon_to = 0;
    }       

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    // parentid != zero then we need to verify the parent is valid
    if ($pid != 0) {
        $parent = xarModAPIFunc('messages','user','get_one',
                                   array('id' => $pid));

        if (!$parent) {
            $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                     'parent', 'userapi', 'create', 'messages');
            throw new BadParameterException($msg);
            return;
        }
    }

    // grab the left and right values from the parent
    $parent_left = $parent['left_id'];
    $parent_right = $parent['right_id'];

    // there should be -at-least- one affected row -- if not
    // then raise an exception. btw, at the very least,
    // the 'right' value of the parent node would have been affected.
    if (!xarModAPIFunc('messages',
                       'user',
                       'create_gap',
                        array('startpoint' => $parent_right))) {

        $msg  = xarML('Unable to create gap in tree for message insertion! Messages table has possibly been corrupted.');
        $msg .= xarML('Please seek help on the public-developer list xaraya_public-dev@xaraya.com, or in the #support channel on Xaraya\'s IRC network.');
        throw new Exception($msg);
    }

    $cdate    = time();
    $left     = $parent_right;
    $right    = $left + 1;

    if (!isset($id)) {
        $id = $dbconn->GenId($xartable['messages']);
    }

    $sql = "INSERT INTO $xartable[messages]
                (id,
                 author,
                 recipient,
                 title,
                 date,
                 text,
                 left_id,
                 right_id,
                 author_status,
                 recipient_status,
                 author_delete,
                 recipient_delete,
                 pid,
                 status,
                 anonpost)
          VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $bdate = (isset($date)) ? $date : $cdate;
    $bpostanon = (empty($postanon)) ? 0 : 1;
    $bindvars = array($id, $author, $recipient, $title, $bdate, $comment, $left, $right, $author_status, $recipient_status, 0, 0, $pid, $status, $bpostanon);

    $result = &$dbconn->Execute($sql,$bindvars);

    if (!$result) {
        return;
    } else {
        $id = $dbconn->PO_Insert_ID($xartable['messages'], 'id');
        // CHECKME: find some cleaner way to update the page cache if necessary
        if (function_exists('xarOutputFlushCached') &&
            xarModVars::get('xarcachemanager','FlushOnNewMessage')) {
            xarOutputFlushCached("messages-block");
        }
        // Call create hooks for categories, hitcount etc.
        $args['module'] = 'messages';
        $args['itemtype'] = 0;
        $args['itemid'] = $id;

        xarModCallHooks('item', 'create', $id, $args);
        return $id;
    }
}

?>
