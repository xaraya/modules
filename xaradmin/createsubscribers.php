<?php
/**
* Create (subscribe) subscribers
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * Create (subscribe) subscribers

* pid
* unregistered - string to parse
* registered - array of uid's
 */
function ebulletin_admin_createsubscribers($args)
{
    // security checks
    if (!xarSecConfirmAuthKey()) return;
    if (!xarSecurityCheck('AddeBulletin', 0)) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('pid', 'id', $pid, $pid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('stype', 'enum:reg:non', $stype, 'reg', XARVAR_NOT_REQUIRED)) return;

    // get names based on subscription type
    if (empty($stype) || $stype == 'reg') {
        if (!xarVarFetch('names', 'array', $names, $names, XARVAR_NOT_REQUIRED)) return;
    } elseif ($stype == 'non') {
        if (!xarVarFetch('names', 'str:0:', $names, $names, XARVAR_NOT_REQUIRED)) return;
    }

    // call API function to do the subscribing
    $results = xarModAPIFunc('ebulletin', 'admin', 'createsubscribers', array(
        'pid' => $pid,
        'stype' => $stype,
        'names' => $names
    ));
    if (empty($results) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // set template vars
    $data = array();
    $data['results'] = $results;
    $data['stype']   = $stype;

    // success (or otherwise)
    return $data;

}

?>
