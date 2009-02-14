<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function accessmethods_log_create($args)
{
    extract($args);

    if (!xarVarFetch('siteid', 'id', $featureid)) return;
    if (!xarVarFetch('userid', 'id', $userid)) return;
    if (!xarVarFetch('details', 'text:html', $details, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('changetype', 'str::', $changetype, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    $logid = xarModAPIFunc('accessmethods',
                        'log',
                        'create',
                        array('siteid'   => $siteid,
                            'userid'        => $userid,
                            'details'        => $details,
                            'changetype'    => $changetype));


    if (!isset($logid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('LOGCREATED'));

    xarResponseRedirect(xarModURL('accessmethods', 'admin', 'display', array('siteid' => $siteid)));

    return true;
}

?>
