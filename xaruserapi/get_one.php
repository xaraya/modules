<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
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
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                        new SystemException(__FILE__.' ('.__LINE__.'):  '.$msg));
        return false;
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $ctable = &$xartable['comments_column'];

    // initialize the commentlist array
    $commentlist = array();

    // if the depth is zero then we
    // only want one comment
    $sql = "SELECT  $ctable[title] AS title,
                    $ctable[cdate] AS datetime,
                    $ctable[hostname] AS hostname,
                    $ctable[comment] AS text,
                    $ctable[author] AS author,
                    $ctable[author] AS uid,
                    $ctable[id] AS id,
                    $ctable[pid] AS pid,
                    $ctable[status] AS status,
                    $ctable[left] AS cleft,
                    $ctable[right] AS cright,
                    $ctable[postanon] AS postanon,
                    $ctable[modid] AS modid,
                    $ctable[itemtype] AS itemtype,
                    $ctable[objectid] AS objectid
              FROM  $xartable[comments]
             WHERE  $ctable[id]=$id
               AND  $ctable[status]="._COM_STATUS_ON;

    $result =& $dbconn->Execute($sql);
    if(!$result) return;

    // if we have nothing to return
    // we return nothing ;) duh? lol
    if ($result->EOF) {
        return array();
    }

    if (!xarModLoad('comments','renderer')) {
        $msg = xarML('Unable to load #(1) #(2) - unable to trim excess depth','comments','renderer');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'UNABLE_TO_LOAD', new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
        return;
    }

    // zip through the list of results and
    // add it to the array we will return
    while (!$result->EOF) {
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

    if (!comments_renderer_array_markdepths_bypid($commentlist)) {
        $msg = xarML('Unable to add depth field to comments!');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'SYSTEM_ERROR', new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
        return;
        // FIXME: <rabbitt> this stuff should really be moved out of the comments
        //        module into a "rendering" module of some sort anyway -- or (god forbid) a widget.
    }

    return $commentlist;
}

?>
