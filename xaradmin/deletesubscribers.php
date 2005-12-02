<?php
/**
* Delete (unsubscribe) subscribers
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
 * Delete (unsubscribe) subscribers
 *
 * @param  $ 'id' the id of the item to be deleted
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function ebulletin_admin_deletesubscribers($args)
{
    // security check
    if (!xarSecurityCheck('DeleteeBulletin', 0)) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('unsub', 'array', $unsub, array(), XARVAR_NOT_REQUIRED)) return;

    // set defaults
    if (empty($unsub)) $unsub = array();

    // Check for confirmation.
    if (empty($confirm)) {

        $subscribers = xarModAPIFunc('ebulletin', 'user', 'getallsubscribers',
            array('ids' => array_keys($unsub))
        );

        // initialize template data
        $data = xarModAPIFunc('ebulletin', 'admin', 'menu');

        // get vars
        $authid = xarSecGenAuthKey();

        // set template data
        $data['subscribers'] = $subscribers;
        $data['authid'] = $authid;

        return $data;
    }

    // security check
    if (!xarSecConfirmAuthKey()) return;

    // call API function to do the deleting
    if (!xarModAPIFunc('ebulletin', 'admin', 'deletesubscribers',
        array('ids' => array_keys($unsub))
    )) return;

    // set status message and return to view
    xarSessionSetVar('statusmsg', xarML('Recipients successfully unsubscribed!'));
    xarResponseRedirect(xarModURL('ebulletin', 'admin', 'viewsubscribers'));

    // success
    return true;
}

?>
