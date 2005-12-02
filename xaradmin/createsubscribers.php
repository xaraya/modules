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
#    if (!xarSecConfirmAuthKey()) return;
    if (!xarSecurityCheck('AddeBulletin', 0)) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('pid', 'id', $pid, $pid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('registered', 'array', $registered, $registered, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('unregistered', 'str:1:', $unregistered, $unregistered, XARVAR_NOT_REQUIRED)) return;

    // set defaults
    if (empty($unregistered)) $unregistered = '';
    if (empty($registered)) $registered = array();

    // validate inputs
    if (!isset($pid) || !is_numeric($pid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'publication ID', 'admin', 'createsubscribers', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // call API function to do the subscribing
    $results = xarModAPIFunc('ebulletin', 'admin', 'createsubscribers', array(
        'pid' => $pid,
        'registered' => $registered,
        'unregistered' => $unregistered
    ));
    if (empty($results) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // initialize template data
    $data = xarModAPIFunc('ebulletin', 'admin', 'menu');

    // set temmplate vars
    $data['results'] = $results;

    // success (or otherwise)
    return $data;

}

?>
