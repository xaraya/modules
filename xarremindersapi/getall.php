<?php

function dossier_remindersapi_getall($args)
{
    extract($args);

    $invalid = array();
//    if (!isset($ownerid) || !is_numeric($ownerid)) {
//        $invalid[] = 'ownerid';
//    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'reminders', 'getall', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $items = array();

    if (!xarSecurityCheck('ViewDossierReminders', 0, 'Reminders', "All:All:All:All")) {//TODO: security
        /* FAIL SILENTLY
        $msg = xarML('Not authorized to access #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
        */
        return $items;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $reminderstable = $xartable['dossier_reminders'];

    $sql = "SELECT reminderid,
                  contactid,
                  ownerid,
                  reminderdate,
                  warningtime,
                  notes
            FROM $reminderstable";
            
    $whereclause = array();
    if(!empty($ownerid)) {
        $whereclause[] = "ownerid = $ownerid";
    }
    if(!empty($startdate)) {
        $whereclause[] = "DATE_SUB(reminderdate, INTERVAL warningtime MINUTE) >= '".$startdate."'";
    }
    if(!empty($enddate)) {
        $whereclause[] = "reminderdate <= '".$enddate."'";
    }
    if(count($whereclause) > 0) {
        $sql .= " WHERE ".implode(" AND ", $whereclause);
    }
            
    $sql .= " ORDER BY reminderdate";
    
    $result = $dbconn->Execute($sql);

    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($reminderid,
              $contactid,
              $ownerid,
              $reminderdate,
              $warningtime,
              $notes) = $result->fields;
        if (xarSecurityCheck('ViewDossierReminders', 0, 'Reminders')) {
            $items[] = array('reminderid'       => $reminderid,
                              'contactid'          => $contactid, 
                              'ownerid'         => $ownerid,
                              'reminderdate'       => $reminderdate,
                              'warningtime'        => $warningtime,
                              'notes'           => $notes);
        }
    }

//    echo "<pre>";
//    print_r($items);
//    echo "</pre>";
//    die();
    $result->Close();

    return $items;
}

?>
