<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Modify a comment
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access private
 * @returns mixed description of return
 */
function comments_userapi_modify($args)
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

    if(isset($itemtype) && !xarVarValidate('int:0:', $itemtype)) {
            $msg .= xarMLbykey('itemtype');
            $error = TRUE;
    }

    if(isset($objectid) && !xarVarValidate('int:1:', $objectid)) {
            $msg .= xarMLbykey('objectid');
            $error = TRUE;
    }

    if(isset($date) && !xarVarValidate('int:1:', $date)) {
            $msg .= xarMLbykey('date');
            $error = TRUE;
    }

    if(isset($status) && !xarVarValidate('enum:1:2:3', $status)) {
            $msg .= xarMLbykey('status');
            $error = TRUE;
    }

    if(isset($useeditstamp) && !xarVarValidate('enum:0:1:2', $useeditstamp)) {
            $msg .= xarMLbykey('useeditstamp');
            $error = TRUE;
    }

    if ($error) {
        throw new BadParameterException($msg);
    }

    $forwarded = xarServer::getVar('HTTP_X_FORWARDED_FOR');
    if (!empty($forwarded)) {
        $hostname = preg_replace('/,.*/', '', $forwarded);
    } else {
        $hostname = xarServer::getVar('REMOTE_ADDR');
    }
    $useeditstamp=xarModVars::get('comments','editstamp');
    $adminid = xarModVars::get('roles','admin');

    /*$dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();*/

    // Let's leave a link for the changelog module if it is hooked to track comments
    /* jojodee: good idea. I'll move it direct to comments template and then can add it to
                any others we like as well, like xarbb.
    if (xarModIsHooked('changelog', 'comments', 0)){
        $url = xarModUrl('changelog', 'admin', 'showlog', array('modid' => '14', 'itemid' => $id));
        $text .= "\n<p>\n";
        $text .= '<a href="' . $url . '" title="' . xarML('See Changes') .'">';
        $text .= '</a>';
        $text .= "\n</p>\n"; //let's keep the begin and end tags together around the wrapped content
    }
    */

    if  (($useeditstamp ==1 ) ||
                     (($useeditstamp == 2 ) && (xarUserGetVar('id')<>$adminid))) {
        $text .= "\n";
        $text .= xarTplModule('comments','user','modifiedby', array(
                              'isauthor' => (xarUserGetVar('id') == $authorid),
                              'postanon'=>$postanon));
        $text .= "\n"; //let's keep the begin and end tags together around the wrapped content
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $object = DataObjectMaster::getObject(array(
                            'name' => 'comments_comments'
        ));

    if (!is_object($object)) return;
    $object->getItem(array('itemid' => $id));

    $object->properties['title']->setValue($title);
    $object->properties['text']->setValue($text);
    $bpostanon = isset($postanon) ? 0 : 1;
    $object->properties['anonpost']->setValue($bpostanon);
    if(isset($itemtype)) {
        $object->properties['itemtype']->setValue($itemtype);
    }
    if(isset($objectid)) {
        $object->properties['objectid']->setValue($objectid);
    }
    if(isset($date)) {
        $object->properties['date']->setValue($date);
    }
    if(isset($status)) {
        $object->properties['status']->setValue($status);
    }

    $object->updateItem();

    /*$sql =  "UPDATE $xartable[comments]
                SET title    = ?,
                    text     = ?,
                    anonpost = ?";
               //WHERE id      = ?";
    $bpostanon = empty($postanon) ? 0 : 1;
    $bindvars = array($title, $text, $bpostanon);

    if(isset($itemtype)) {
        $sql .= ",\nitemtype = ?";
        $bindvars[] = $itemtype;
    }

    if(isset($objectid)) {
        $sql .= ",\nobjectid = ?";
        $bindvars[] = $objectid;
    }

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
    }*/
    // Call update hooks for categories etc.
    $args['module'] = 'comments';
    $args['itemtype'] = 0;
    $args['itemid'] = $id;
    xarModCallHooks('item', 'update', $id, $args);

    return true;
}

?>