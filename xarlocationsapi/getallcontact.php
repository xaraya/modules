<?php

/**
 *
 *
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xproject module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function dossier_locationsapi_getallcontact($args)
{
    extract($args);

    $invalid = array();
    if (!isset($contactid) || !is_numeric($contactid)) {
        $invalid[] = 'contactid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'locations', 'getcontact', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $items = array();

    $item = xarModAPIFunc('dossier',
                        'user',
                        'get',
                        array('contactid' => $contactid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('PublicDossierAccess', 1, 'Contact', $item['cat_id'].":".$item['userid'].":".$item['company'].":".$item['agentuid'])) {
        /* Fail silently
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

    $locationstable = $xartable['dossier_locations'];
    $locationdatatable = $xartable['dossier_locationdata'];

    $query = "SELECT b.contactid,
                    b.locationid,
                    b.startdate,
                    b.enddate,
                    a.cat_id,
                    a.address_1,
                    a.address_2,
                    a.city,
                    a.us_state,
                    a.postalcode,
                    a.country,
                    a.latitude,
                    a.longitude
            FROM $locationstable a, $locationdatatable b
            WHERE a.locationid = b.locationid
            AND b.contactid = ?";
            
    $whereclause = array();
    if(!empty($city)) {
        $whereclause[] = "city = '".$city."'";
    }
    if(!empty($us_state)) {
        $whereclause[] = "us_state = '".$us_state."'";
    }
    if(!empty($postalcode)) {
        $whereclause[] = "postalcode = ".$postalcode;
    }
    if(!empty($country)) {
        $whereclause[] = "country = ".$country;
    }
    if(count($whereclause) > 0) {
        $sql .= " AND ".implode(" AND ", $whereclause);
    }

    $bindvars = array($contactid);

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($contactid,
            $locationid,
            $startdate,
            $enddate,
            $cat_id,
            $address_1,
            $address_2,
            $city,
            $us_state,
            $postalcode,
            $country,
            $latitude,
            $longitude) = $result->fields;
        if (xarSecurityCheck('PublicDossierAccess', 0, 'Contact')) {                    
            $items[$locationid] = array(
                            'contactid'     => $contactid,
                            'locationid'    => $locationid,
                            'startdate'     => $startdate,
                            'enddate'       => $enddate,
                            'cat_id'        => $cat_id,
                            'address_1'     => $address_1,
                            'address_2'     => $address_2,
                            'city'          => $city,
                            'us_state'      => $us_state,
                            'postalcode'    => $postalcode,
                            'country'       => $country,
                            'latitude'      => $latitude,
                            'longitude'     => $longitude);
        }
    }

    $result->Close();

    return $items;
}

?>
