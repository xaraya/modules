<?php

function dossier_locations_update($args)
{
    extract($args);

    if (!xarVarFetch('contactid', 'id', $contactid)) return;
    if (!xarVarFetch('locationid', 'id', $locationid)) return;
    if (!xarVarFetch('cat_id', 'int::', $cat_id, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('address_1', 'str::', $address_1, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('address_2', 'str::', $address_2, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('city', 'str::', $city, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('us_state', 'str::', $us_state, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('postalcode', 'str::', $postalcode, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('country', 'str::', $country, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('latitude', 'str::', $latitude, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('longitude', 'str::', $longitude, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startdate', 'str::', $startdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('enddate', 'str::', $enddate, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    if(!xarModAPIFunc('dossier',
                    'locations',
                    'update',
                    array('locationid'      => $locationid,
                        'cat_id'            => $cat_id,
                        'address_1'         => $address_1,
                        'address_2'         => $address_2,
                        'city'              => $city,
                        'us_state'          => $us_state,
                        'postalcode'        => $postalcode,
                        'country'           => $country,
                        'latitude'          => $latitude,
                        'longitude'         => $longitude))) {
        return;
    }


    xarSessionSetVar('statusmsg', xarML('Address / Location Updated'));

    xarResponseRedirect(xarModURL('dossier', 'admin', 'display', array('contactid' => $contactid)));

    return true;
}

?>
