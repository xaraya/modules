<?php

/**
 * create a new forum
 * @param $args['fname'] name of forum
 * @param $args['fdesc'] description of forum
 * @param $args['tid'] topic id to update
 * @returns int
 * @return autolink ID on success, false on failure
 */
function subitems_adminapi_ddobjectlink_update($args)
{

    // Get arguments from argument array
    extract($args);

    if(!isset($objectid))
    	$invalid[] = "objectid";

    // params in arg
    $params = array("template" => "xar_template",
                    "itemtype" => "xar_itemtype",
                    "module" => "xar_module");
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


    // Security Check
    // it would have to be ModxarBB, but because posting results in an update, it has to be Post Permission
 //   if(!xarSecurityCheck('ReadxarBB',1,'Forum',"$fid:All")) return;    // todo

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // now
    $time = date('Y-m-d G:i:s');

    foreach($params as $vvar => $field)	{
    	if(isset($$vvar))
	    	$update[] = $field ."='".xarVarPrepForStore($$vvar)."'";
    }

    // Update item
    $query = "UPDATE {$xartable['subitems_ddobjects']} SET ".join(",",$update)." WHERE xar_objectid = $objectid";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have created a new topic
    $args['module'] = 'subitems';
    $args['itemtype'] = 1; // topic
    $args['itemid'] = $objectid;
    xarModCallHooks('item', 'modify', $objectid, $args);

    // Return the id of the newly created link to the calling process
    return true;
}

?>