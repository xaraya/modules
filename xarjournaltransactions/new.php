<?php

function labaccounting_journaltransactions_new($args)
{
    extract($args);

    if (!xarVarFetch('journalid', 'int', $journalid)) return;
    if (!xarVarFetch('inline', 'str', $inline, $inline, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creatorid', 'int', $creatorid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('monthshown', 'int', $monthshown, date("m"), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('yearshown', 'int', $yearshown, date("Y"), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str', $returnurl, 0, XARVAR_NOT_REQUIRED)) return;
    
    $data = array();

    if (!xarSecurityCheck('AddJournal')) {
        return;
    }
    
    $journalinfo = xarModAPIFunc('labaccounting', 'journals', 'get', array('journalid' => $journalid));
    
    if($journalinfo == false) return;
    
    $journallist = xarModAPIFunc('labaccounting', 'journals', 'getall', array('journaltype' => $journalinfo['journaltype']));
    
    if($journallist == false) return;
    
    $data['journallist'] = $journallist;
    
    $items = xarModAPIFunc('labaccounting', 'journals', 'getall',
                            array('journaltype' => $journalinfo['journaltype'],
                                'nested' => true));
                                
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    $data['items'] = $items;
    
    if(!empty($returnurl)) {
        $data['returnurl'] = $returnurl;
    } else {
        $data['returnurl'] = xarModURL('labaccounting', 'journals', 'display', array('journalid' => $journalid, 'yearshown' => $yearshown));
    }

    $data['authid'] = xarSecGenAuthKey();
    $data['journalid'] = $journalid;
    $data['inline'] = $inline;
    $data['creatorid'] = $creatorid;
    $data['transdate'] = date("Y-m-d", mktime(0, 0, 0, $monthshown, date("d"), $yearshown));
    
    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add'));

    return $data;
}

?>
