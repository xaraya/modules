<?php
/**
 * Dossier Module - A Contact and Customer Service Management Module
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @link http://xaraya.com/index.php/release/829.html
 * @author Chad Kraeft
 */
function dossier_locations_create($args)
{
    extract($args);

    if (!xarVarFetch('contactid', 'id', $contactid)) return;
    if (!xarVarFetch('cat_id', 'id', $cat_id, 0, XARVAR_NOT_REQUIRED)) return;
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

    $locationid = xarModAPIFunc('dossier',
                        'locations',
                        'create',
                        array('cat_id'            => $cat_id,
                            'address_1'         => $address_1,
                            'address_2'         => $address_2,
                            'city'              => $city,
                            'us_state'          => $us_state,
                            'postalcode'        => $postalcode,
                            'country'           => $country,
                            'latitude'          => $latitude,
                            'longitude'         => $longitude));


    if (!isset($locationid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('LOCATIONCREATED'));

    if($contactid > 0) {
        if(!xarModAPIFunc('dossier',
                    'locations',
                    'createdata',
                    array('contactid'       => $contactid,
                        'locationid'        => $locationid,
                        'startdate'         => $startdate,
                        'enddate'           => $enddate))) {
            return;
        }
    }
    xarResponseRedirect(xarModURL('dossier', 'admin', 'display', array('contactid' => $contactid)));
    
    return true;
}

?>
