<?php
/**
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xproject module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function dossier_locationsapi_create($args)
{
    extract($args);

    $invalid = array();
    if ( 
        (!isset($address_1) || !is_string($address_1) || empty($address_1) )
        &&       
        (!isset($city) || !is_string($city) )
        &&       
        (!isset($us_state) || !is_string($us_state) )
        && 
        (!isset($country) || !is_string($country) )
       ) {
        $invalid[] = 'address/city/state/country';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'locations', 'create', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    
    if(!isset($cat_id)) $cat_id = 0;
    if(!isset($latitude)) $latitude = "";
    if(!isset($longitude)) $longitude = "";
    
    if ($us_state == "Please select" || empty($us_state)) {
        $us_state = ''; //[missing state]';
    }
    
    if ($country == "Please select" || empty($country)) {
        $country = ''; //[missing country]';
    }

    if (!xarSecurityCheck('PublicDossierAccess', 1)) {
        $msg = xarML('Not authorized to add #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $locationstable = $xartable['dossier_locations'];

    $nextId = $dbconn->GenId($locationstable);

    $query = "INSERT INTO $locationstable (
                    locationid,
                    cat_id,
                    address_1,
                    address_2,
                    city,
                    us_state,
                    postalcode,
                    country,
                    latitude,
                    longitude)
                VALUES (?,?,?,?,?,?,?,?,?,?)";

    $bindvars = array($nextId,
                    $cat_id,
                    isset($address_1) ? $address_1 : "",
                    isset($address_2) ? $address_2 : "",
                    isset($city) ? $city : "",
                    isset($us_state) ? $us_state : "",
                    isset($postalcode) ? $postalcode : "",
                    isset($country) ? $country : "",
                    isset($latitude) ? $latitude : "",
                    isset($longitude) ? $longitude : "");

    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;

    $locationid = $dbconn->PO_Insert_ID($locationstable, 'locationid');

    return $locationid;
}

?>
