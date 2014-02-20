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
 * Get a single comment.
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer    $args['id']       the id of a comment
 * @returns  array   an array containing the sole comment that was requested
                     or an empty array if no comment found
 */
function comments_userapi_get_one( $args )
{

    extract($args);

    if(!isset($id) || empty($id)) {
        $msg = xarML('Missing or Invalid argument [#(1)] for #(2) function #(3) in module #(4)',
                                 'id','userapi','get_one','comments');
        throw new Exception($msg);
    }

    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();

    // initialize the commentlist array
    $commentlist = array();

    sys::import('modules.dynamicdata.class.objects.master');
    $object = DataObjectMaster::getObject(array('name' => 'comments_comments'));
    $object->getItem(array('itemid' => $id));
    $values = $object->getFieldValues();
    $values['position_atomic'] = $object->properties['position']->atomic_value;

    if ($values['status'] != _COM_STATUS_ON) return array();

    $values['postanon'] = $values['anonpost'];
    $values['datetime'] = $values['date'];
    $values['role_id'] = $values['author'];
    //comments_renderer_wrap_words($values['text'],80);
//    $values['author'] = xarUserGetVar('name',$values['author']);

    $arr[0] = $values;
    $values = $arr;

    // if the depth is zero then we
    // only want one comment
   /* $sql = "SELECT  title AS title,
                    date AS datetime,
                    hostname AS hostname,
                    text AS text,
                    author AS author,
                    author AS role_id,
                    id AS id,
                    parent_id AS parent_id,
                    status AS status,
                    left_id AS left_id,
                    right_id AS right_id,
                    anonpost AS postanon,
                    moduleid AS moduleid,
                    itemtype AS itemtype,
                    objectid AS objectid
              FROM  $xartable[comments]
             WHERE  id=$id
               AND  status="._COM_STATUS_ON;

    $result =& $dbconn->Execute($sql);
    if(!$result) return;

    // if we have nothing to return
    // we return nothing ;) duh? lol
    if ($result->EOF) {
        return array();
    }*/

    if (!xarModLoad('comments','renderer')) {
        $msg = xarML('Unable to load #(1) #(2) - unable to trim excess depth','comments','renderer');
        throw new Exception($msg);
    }

    // zip through the list of results and
    // add it to the array we will return
    /*while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        // FIXME delete after date output testing
        // $row['date'] = xarLocaleFormatDate("%B %d, %Y %I:%M %p",$row['datetime']);
        $row['date'] = $row['datetime'];
        $row['author'] = xarUserGetVar('name',$row['author']);
        comments_renderer_wrap_words($row['text'],80);
        $commentlist[] = $row;
        $result->MoveNext();
    }

    $result->Close();
    */

    if (!comments_renderer_array_markdepths_bypid($values)) {
        $msg = xarML('Unable to add depth field to comments!');
        throw new Exception($msg);
        // FIXME: <rabbitt> this stuff should really be moved out of the comments
        //        module into a "rendering" module of some sort anyway -- or (god forbid) a widget.
    }

    return $values;
}

?>
