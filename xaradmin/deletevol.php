<?php
/**
 * Add a volume from the encyclopedia
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_admin_deletevol()
{
    if (!xarVarFetch('vid',       'int', $vid,  NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('confirmed', 'int', $confirmed, NULL, XARVAR_DONT_SET)) return;

    $volume = xarModAPIFunc('encyclopedia',
                         'admin',
                         'volget',
                         array('vid' => $vid));

    if (!$volume) {
        $msg = "No encyclopedia volume was found";
        xarErrorSet(XAR_USER_EXCEPTION, 'NOT_FOUND', new DefaultUserException($msg));
        return;
    }
    if (!xarSecurityCheck('DeleteEncyclopedia',0,'Volume',$volume['volume'] . "::" . $vid)) {return;}

    if (empty($confirmed)) {
        $data['authid'] = xarSecGenAuthKey();
        $data['volume'] = $volume;
        return $data;
    }

    // Check for confirmation.
    if (!xarSecConfirmAuthKey()) return;
    if (xarModAPIFunc('encyclopedia',
                     'admin',
                     'deletevol',
                     array('vid' => $vid))) {
    }

    xarResponseRedirect(xarModURL('encyclopedia', 'admin', 'volumes'));
}
?>