<?php

function dossier_locationsapi_get($args)
{
    extract($args);

    if (!isset($locationid) || !is_numeric($locationid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'location ID', 'locations', 'get', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $locationstable = $xartable['dossier_locations'];

    $query = "SELECT locationid,
                    cat_id,
                    address_1,
                    address_2,
                    city,
                    us_state,
                    postalcode,
                    country,
                    latitude,
                    longitude
            FROM $locationstable
            WHERE locationid = $locationid
            AND locationid = ?";
    $result = &$dbconn->Execute($query,array($locationid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

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

    $result->Close();

    if (!xarSecurityCheck('PublicDossierAccess', 1, 'Contact')) {
        $msg = xarML('Not authorized to view this location.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'AUTH_FAILED',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $item = array('locationid'  => $locationid,
                'cat_id'        => $cat_id,
                'address_1'      => $address_1,
                'address_2'      => $address_2,
                'city'          => $city,
                'us_state'      => $us_state,
                'postalcode'    => $postalcode,
                'country'       => $country,
                'latitude'      => $latitude,
                'longitude'     => $longitude);

    return $item;
}

?>
