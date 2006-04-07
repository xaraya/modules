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
    if (!xarVarFetch('id', 'int:1:', $id, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('unsub', 'array', $unsub, array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('stype', 'enum:non:reg', $stype, 'reg', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('return', 'str:1:', $return, '', XARVAR_NOT_REQUIRED)) return;

    // set defaults
    if (empty($unsub)) $unsub = array();
    if (!empty($id)) $unsub = array($id => 1);

    // Check for confirmation.
    if (empty($confirm)) {

        switch($stype) {
        case 'non':
            $subscribers = xarModAPIFunc('ebulletin', 'user', 'getallsubscribers_non',
                array('ids' => array_keys($unsub))
            );
            break;
        case 'reg':
        default:
            $subscribers = xarModAPIFunc('ebulletin', 'user', 'getallsubscribers_reg',
                array('ids' => array_keys($unsub))
            );
            break;

        }

        // set template vars
        $data = array();
        $data['subscribers'] = $subscribers;
        $data['authid']      = xarSecGenAuthKey();
        $data['return']      = $return;

        return $data;
    }

    // security check
    if (!xarSecConfirmAuthKey()) return;

    // call API function to do the deleting
    if (!xarModAPIFunc('ebulletin', 'admin', 'deletesubscribers',
        array('ids' => array_keys($unsub))
    )) return;

    // set status message and return to view
    xarSessionSetVar('statusmsg', xarML('Successfully unsubscribed!'));
    if (empty($return)) $return = xarModURL('ebulletin', 'admin', 'viewsubscribers');
    xarResponseRedirect($return);

    // success
    return true;
}

?>
