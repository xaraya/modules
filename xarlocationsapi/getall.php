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
function dossier_locationsapi_getall($args)
{
    extract($args);

    $invalid = array();
    if (!isset($contactid) || !is_numeric($contactid)) {
        $invalid[] = 'contactid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'locations', 'getall', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!xarSecurityCheck('PublicDossierAccess', 1, 'Contact', "All:All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $locationstable = $xartable['dossier_locations'];

    $sql = "SELECT locationid,
                    cat_id,
                    address_1,
                    address_2,
                    city,
                    us_state,
                    postalcode,
                    country,
                    latitude,
                    longitude
            FROM $locationstable";

    $result = $dbconn->Execute($sql);

    if (!$result) return;
    
    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($locationid,
            $cat_id,
            $address_1,
            $address_2,
            $city,
            $us_state,
            $postalcode,
            $country,
            $latitude,
            $longitude) = $result->fields;
        if (xarSecurityCheck('PublicAccess', 0, 'Item', "$us_state:All:$city")) {                    
            $items[$locationid] = array(
                            'locationid'    => $locationid,
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
