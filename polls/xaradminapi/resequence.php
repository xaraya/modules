<?php

/**
 * resequence a poll's options
 */
function polls_adminapi_resequence($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($pid)) {
        $msg = xarML('Missing poll ID');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollsinfotable = $xartable['polls_info'];
    $prefix = xarConfigGetVar('prefix');

    // Get the information
    $sql = "SELECT ".$prefix."_optnum
            FROM $pollsinfotable
            WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid) . "
            ORDER BY ".$prefix."_optnum";
    $result = $dbconn->Execute($sql);

    // Fix sequence numbers
    $seq=1;
    while(list($optnum) = $result->fields) {
        $result->MoveNext();

        if ($optnum != $seq) {
            $query = "UPDATE $pollsinfotable
                SET ".$prefix."_optnum=" . xarVarPrepForStore($seq) . "
                WHERE ".$prefix."_pid=" . xarVarPrepForStore($pid) . "
                AND ".$prefix."_optnum=" . xarVarPrepForStore($optnum);
            $result = $dbconn->Execute($query);
		    if(!$result){
		        return;
		    }

        }
        $seq++;
    }
    $result->Close();

    return;
}

?>