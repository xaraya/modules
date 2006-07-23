<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */
/**
 * View the current event queue
 */
function pubsub_admin_viewq($args)
{
    extract($args);
    if (!xarVarFetch('action','str', $action, '')) return;
    if (!xarVarFetch('handlingid','int', $handlingid, 0)) return;

    if (!xarSecurityCheck('AdminPubSub')) return;

    if (!empty($action)) {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;

        switch ($action)
        {
            case 'process':
                if (!xarModAPIFunc('pubsub','admin','processq')) {
                    return;
                }
                xarResponseRedirect(xarModURL('pubsub', 'admin', 'viewq'));
                return true;
                break;

            case 'view':
                if (!empty($handlingid)) {
                    // preview message ?
                }
                break;

            case 'delete':
                if (!empty($handlingid)) {
                    if (!xarModAPIFunc('pubsub','admin','deljob',array('handlingid' => $handlingid))) {
                        return;
                    }
                    xarResponseRedirect(xarModURL('pubsub', 'admin', 'viewq'));
                    return true;
                }
                break;

            default:
                break;
        }
    }

    // The user API function is called
    $events = xarModAPIFunc('pubsub',
                            'admin',
                            'getq');

    $data['items'] = $events;
    // TODO: add a pager (once it exists in BL)
    $data['pager'] = '';
    $data['authid'] = xarSecGenAuthKey();

    // return the template variables defined in this template
    return $data;

} // END ViewAll

?>
