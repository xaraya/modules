<?php
/**
    Gets errors
*/
function mailbag_adminapi_geterrors($args)
{
    extract($args);
    
    $dbconn   =& xarDBGetConn();
    $xartable =  xarDBGetTables();
    
    $table = $xartable['mailbag_errors'];

    $sql = "SELECT  xar_msgid,
                    xar_subject,
                    xar_from,
                    xar_to,
                    xar_msg_time,
                    xar_msg_text,
                    xar_header,
                    xar_errorcode 
            FROM $table ";

    if(!empty($errorcode))
        $sql .= " WHERE xar_errorcode = $errorcode ";
            
    $result = $dbconn->Execute($sql);
    if (!isset($result)) return;

    $errcount = 0;
    while(list($msg_id, $subject, $from, $to, $msg_time, $msg_text, $header, $errorcode) = $result->fields)
    {
        $msgindex[$i]['err_id'] = $msg_id;
        $msgindex[$i]['from'] = $from;
        $msgindex[$i]['to'] = $to."@". xarModGetVar('mailbag', 'emaildomain');
        $msgindex[$i]['subject'] = $subject;
        $msgindex[$i]['date'] = !empty($msg_time) ? strtotime($msg_time) : 0;
        $msgindex[$i]['text'] = $msg_text;
        $msgindex[$i]['header'] = $header;
        $i++;
        $errcount++;
        $result->MoveNext();
    }

    $result->Close();
    
    return $errcount;
}
?>