<?php

function labaccounting_journalsapi_get($args)
{
    extract($args);

    if (!isset($journalid) || !is_numeric($journalid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'journals', 'get', 'labaccounting');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $journals_table = $xartable['labaccounting_journals'];

    $query = "SELECT journalid,
                    parentid,
                    owneruid,
                    contactid,
                    currency,
                    account_title,
                    journaltype,
                    agentuid,
                    acctnum,
                    acctlogin,
                    accturl,
                    acctpwd,
                    notes,
                    opendate,
                    closedate,
                    billdate,
                    status,
                    invoicefreq,
                    invoicefrequnits
            FROM $journals_table
            WHERE journalid = ?";
    $result = &$dbconn->Execute($query,array($journalid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    list($journalid,
        $parentid,
        $owneruid,
        $contactid,
        $currency,
        $account_title,
        $journaltype,
        $agentuid,
        $acctnum,
        $acctlogin,
        $accturl,
        $acctpwd,
        $notes,
        $opendate,
        $closedate,
        $billdate,
        $status,
        $invoicefreq,
        $invoicefrequnits) = $result->fields;
        
    $result->Close();
    
    $uid = xarUserGetVar('uid');

    if ((xarSecurityCheck('ReadJournal', 1, 'Journal', $journaltype) && $owneruid != $uid) 
        && !xarSecurityCheck('ManageJournal', 1, 'Journal', $journaltype)) {
        return;
    }

    $item = array('journalid'           => $journalid,
                    'parentid'          => $parentid,
                    'owneruid'          => $owneruid,
                    'contactid'         => $contactid,
                    'currency'          => $currency,
                    'account_title'     => $account_title,
                    'journaltype'       => $journaltype,
                    'agentuid'          => $agentuid,
                    'acctnum'           => $acctnum,
                    'acctlogin'         => $acctlogin,
                    'accturl'           => $accturl,
                    'acctpwd'           => $acctpwd,
                    'notes'             => $notes,
                    'opendate'          => $opendate == "0000-00-00" ? "" : $opendate,
                    'closedate'         => $closedate == "0000-00-00" ? "" : $closedate,
                    'billdate'          => $billdate == "0000-00-00" ? "" : $billdate,
                    'status'            => $status,
                    'invoicefreq'       => $invoicefreq,
                    'invoicefrequnits'  => $invoicefrequnits);

    return $item;
}

?>