<?php

/**
 * create a new forum
 * @param $args['fname'] name of forum
 * @param $args['fdesc'] description of forum
 * @param $args['tid'] topic id to update
 * @returns int
 * @return autolink ID on success, false on failure
 */
function xarbb_userapi_updatetopic($args)
{

    // Get arguments from argument array
    extract($args);

    if(!isset($tid))
    	$invalid[] = "tid";

    // params in arg
    $params = array("fid" => "xar_fid",
    				"ttitle" => "xar_ttitle",
                    "tpost" => "xar_tpost",
                    "tposter" => "xar_tposter",
                    "time" => "xar_ttime",
                    "tposter" => "xar_tposter",
                    "treplies" => "xar_treplies",
                    "treplier" => "xar_treplier",
                    "tftime"   => "xar_tftime");
    foreach($params as $vvar => $dummy)	{
    	if(isset($$vvar))	{
			$set = true;
            break;
        }
    }
    if(    !isset($set)   )
    	$invalid[] = "at least one of these has to be set: ".join(",",array_keys($fields));

    // Argument check - make sure that at least on paramter is present
    // if not then set an appropriate error message and return
    if ( isset($invalid) ) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'create', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // for sec check
    if(!$topic = xarModAPIFunc('xarbb','user','gettopic',array('tid' => $tid))) return;    

    // Security Check
    // it would have to be ModxarBB, but because posting results in an update, it has to be Post Permission
    if(!xarSecurityCheck('PostxarBB',1,'Forum',$topic['catid'].':'.$topic['fid'])) return;    // todo

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xbbtopicstable = $xartable['xbbtopics'];

    // now
    $time = date('Y-m-d G:i:s');

    foreach($params as $vvar => $field)	{
    	if(isset($$vvar))
	    	$update[] = $field ."='".xarVarPrepForStore($$vvar)."'";
    }

    // Update item
    $query = "UPDATE $xbbtopicstable SET ".join(",",$update)." WHERE xar_tid = $tid";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have created a new topic
    $args['module'] = 'xarbb';
    $args['itemtype'] = 2; // topic
    $args['itemid'] = $tid;
    xarModCallHooks('item', 'update', $tid, $args);

    // Return the id of the newly created link to the calling process
    return true;
}

?>
