<?php
/**
 * Messages module
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage  messages
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Modify a message
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */

sys::import('modules.messages.xarincludes.defines');

function messages_userapi_modify($args)
{

    extract($args);

    $msg = xarML('Missing or Invalid Parameters: ');
    $error = FALSE;

    if (!isset($title)) {
        $msg .= xarMLbykey('title ');
        $error = TRUE;
    }

    if (!isset($id)) {
        $msg .= xarMLbykey('id ');
        $error = TRUE;
    }

    if (!isset($text)) {
        $msg .= xarMLbykey('text ');
        $error = TRUE;
    }

    if (!isset($postanon)) {
        $msg .= xarMLbykey('postanon ');
        $error = TRUE;
    }

    if(isset($date) && !xarVarValidate('int:1:', $date)) {
            $msg .= xarMLbykey('date');
            $error = TRUE;
    }

    if(isset($author_status) && !xarVarValidate('enum:0:1:2', $author_status)) {
            $msg .= xarMLbykey('author status');
            $error = TRUE;
    }

    if(isset($recipient_status) && !xarVarValidate('enum:0:1:2', $recipient_status)) {
            $msg .= xarMLbykey('recipient status');
            $error = TRUE;
    }

    if(isset($useeditstamp) && !xarVarValidate('enum:0:1:2', $useeditstamp)) {
            $msg .= xarMLbykey('useeditstamp');
            $error = TRUE;
    }

    if ($error) {
        throw new BadParameterException($msg);
    }

    $useeditstamp=xarModVars::get('messages','editstamp');
    $adminid = xarModVars::get('roles','admin');

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    // Let's leave a link for the changelog module if it is hooked to track messages
    /* jojodee: good idea. I'll move it direct to messages template and then can add it to
                any others we like as well, like xarbb.
    if (xarModIsHooked('changelog', 'messages', 0)){
        $url = xarModUrl('changelog', 'admin', 'showlog', array('modid' => '6', 'itemid' => $id));
        $text .= "\n<p>\n";
        $text .= '<a href="' . $url . '" title="' . xarML('See Changes') .'">';
        $text .= '</a>';
        $text .= "\n</p>\n"; //let's keep the begin and end tags together around the wrapped content
    }
    */

    if  (($useeditstamp ==1 ) ||
                     (($useeditstamp == 2 ) && (xarUserGetVar('id')<>$adminid))) {
        $text .= "\n";
        $text .= xarTplModule('messages','user','modifiedby', array(
                              'isauthor' => (xarUserGetVar('id') == $author_id),
                              'postanon'=>$postanon));
        $text .= "\n"; //let's keep the begin and end tags together around the wrapped content
    }

    $sql =  "UPDATE $xartable[messages]
                SET title    = ?,
                    text     = ?,
                    anonpost = ?";
               //WHERE id      = ?";
    $bpostanon = empty($postanon) ? 0 : 1;
    $bindvars = array($title, $text, $bpostanon);

    if(isset($date)) {
        $sql .= ",\ndate = ?";
        $bindvars[] = $date;
    }

    if(isset($status)) {
        $sql .= ",\nstatus = ?";
        $bindvars[] = $status;
    }

    $sql .= "\nWHERE id = ?";
    $bindvars[] = $id;
    $result = &$dbconn->Execute($sql,$bindvars);

    if (!$result) {
        return;
    }
    // Call update hooks for categories etc.
    $args['module'] = 'messages';
    $args['itemtype'] = 0;
    $args['itemid'] = $id;
    xarModCallHooks('item', 'update', $id, $args);

    return true;
}

?>
