<?php

function recommend_userapi_getcredit($args)
{
    extract($args);

    if (!isset($recipient_email) || !is_string($recipient_email)) {
//        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
//            'recipient_email', 'user', 'getcredit', 'recommend');
//        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
//            new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $recommend_recipients_table = $xartable['recommend_recipients'];

    $query = "SELECT xar_recipientid,
                    xar_sentby_uid,
                    xar_senddate,
                    xar_recipient_email,
                    xar_extradata
            FROM $recommend_recipients_table
            WHERE xar_recipient_email = ?
            ORDER BY xar_senddate DESC
            LIMIT 1";

    $bindvars = array($recipient_email);
    $result = &$dbconn->Execute($query,$bindvars);
    
    if (!$result) return;

    if ($result->EOF) {
        return false;
    }

    list($recipientid,$sentby_uid,$senddate,$recipient_email,$extradata) = $result->fields;

    $result->Close();

    $creditinfo = array(); //xarUserGetVars($sentby_uid);
    $creditinfo['recipientid'] = $recipientid;
    $creditinfo['sentby_uid'] = $sentby_uid;
    $creditinfo['senddate'] = $senddate;
    $creditinfo['recipient_email'] = $recipient_email;
    $creditinfo['extradata'] = $extradata;

    return $creditinfo;
}

?>
