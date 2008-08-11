<?php
/**
 * Get a single comment.
 *
 * @author   Carl P. Corliss (aka rabbitt)
 * @access   public
 * @param    integer    $args['id']
 * the id of a comment
 * @param    string  $folder 
 *        the folder type
 * @returns  array   an array containing the sole comment that was requested
                     or an empty array if no comment found
 */
//Psspl:Added function for getting comment entry according to the folder type.
//Psspl:Included file for debbuging.
include_once("./modules/commonutil.php");
function messages_userapi_get_one( $args )
{

    extract($args);

    if(!isset($id) || empty($id)) {
        $msg = xarML('Missing or Invalid argument [#(1)] for #(2) function #(3) in module #(4)',
                                 'id','userapi', 'get_one', 'comments');
        throw new Exception($msg);
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
	$prefix = xarDB::getPrefix();
	$tableName = $prefix."_comments"; 
	//TracePrint($tableName, "table");
    // initialize the commentlist array
    $commentlist = array();

    // if the depth is zero then we
    // only want one comment
    if (!isset($folder)) {
    	$folder = 'inbox';
    	
    }
    //TracePrint($xartable,"xartables");
    TracePrint($args,"id");
    $sql = "SELECT  title AS title,
                    date AS datetime,
                    hostname AS hostname,
                    text AS text,
                    author AS author,
                    author AS role_id,
                    id AS id,
                    pid AS pid,
                    status AS status,
                    left_id AS left_id,
                    right_id AS right_id,
                    anonpost AS postanon,
                    anonpost_to AS postanon_to,
                    modid AS modid,
                    itemtype AS itemtype,
                    objectid AS objectid
              FROM  $xartable[comments]
              WHERE  id=$id";
	if ($folder=='drafts') {
    	//$sql .= " AND  status="._COM_STATUS_OFF;
    }
    else{
    	//$sql .= " AND  status="._COM_STATUS_ON;
    }
       

    $result =& $dbconn->Execute($sql);
     
    if(!$result) return;

    // if we have nothing to return
    // we return nothing ;) duh? lol
    if ($result->EOF) {
        return array();
    }

    if (!xarModLoad('comments', 'renderer')) {
        $msg = xarML('Unable to load #(1) #(2) - unable to trim excess depth','comments','renderer');
        throw new Exception($msg);
    }

    // zip through the list of results and
    // add it to the array we will return
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        // FIXME delete after date output testing
        // $row['date'] = xarLocaleFormatDate("%B %d, %Y %I:%M %p",$row['datetime']);
        $row['date'] = $row['datetime'];
        $row['author'] = xarUserGetVar('name',$row['author']);
        comments_renderer_wrap_words($row['text'], 80);
        $commentlist[] = $row;
        $result->MoveNext();
    }

    $result->Close();

    if (!comments_renderer_array_markdepths_bypid($commentlist)) {
        $msg = xarML('Unable to add depth field to comments!');
        throw new Exception($msg);
        // FIXME: <rabbitt> this stuff should really be moved out of the comments
        //        module into a "rendering" module of some sort anyway -- or (god forbid) a widget.
    }
	
    
    return $commentlist;
    
}

?>
