<?php
/**
    Gets all Maillists
*/
function mailbag_adminapi_getmaillist($args)
{
    $dbconn   =& xarDBGetConn();
    $xartable =  xarDBGetTables();
    
    $table = $xartable['mailbag_maillists'];

    $sql = "SELECT  xar_lid,
                    xar_from_email,
                    xar_to_email,
                    xar_in_subject,
                    xar_desc,
                    xar_to_topic,
                    xar_cid,
                    xar_admin_uid, 
                    xar_no_comment,
                    xar_no_homepage
            FROM $table
            ORDER BY xar_lid";
    $result = $dbconn->Execute($sql);
    if (!isset($result)) return;

    $rblacklist = array();
    while(list($lid, $from_email, $to_email, $in_subject, $desc, 
               $to_topic, $cid, $auid, $no_comment, $status) = $result->fields)
    {
        $rblacklist[$lid] = array('lid'        => $lid,
                                  'from_email' => $from_email,
                                  'to_email'   => $to_email,
                                  'in_subject' => $in_subject,
                                  'desc'       => $desc,
                                  'to_topic'   => $to_topic,
                                  'cid'        => $cid,
                                  'admin_uid'  => $auid,
                                  'no_comment' => $no_comment,
                                  'status'     => $status);
        $result->MoveNext();
    }

    $result->Close();
    
    return $rblacklist;
}
?>