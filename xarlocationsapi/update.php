<?php
/**
 * Update a project page item
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Update an example item
 *
 * @author the Example module development team
 * @param  $args ['exid'] the ID of the item
 * @param  $args ['name'] the new name of the item
 * @param  $args ['number'] the new number of the item
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function dossier_locationsapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($locationid) || !is_numeric($locationid)) {
        $invalid[] = 'location ID';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'locations', 'update', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('dossier',
                            'locations',
                            'get',
                            array('locationid' => $locationid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('PublicDossierAccess', 1, 'Contact', "All:All:All:All")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $locationstable = $xartable['dossier_locations'];

    $query = "UPDATE $locationstable
            SET cat_id = ?,
                  address_1 = ?,
                  address_2 = ?,
                  city = ?,
                  us_state = ?,
                  postalcode = ?,
                  country = ?,
                  latitude = ?,
                  longitude = ?
            WHERE locationid = ?";
            
    $bindvars = array(
              $cat_id,
              $address_1,
              $address_2,
              $city,
              $us_state,
              $postalcode,
              $country,
              $latitude,
              $longitude,
              $locationid);

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) { // return;
        $msg = xarML('SQL: #(1)',
            $dbconn->ErrorMsg());
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    return true;
}
?>
