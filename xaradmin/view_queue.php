<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * View the current event queue
 */
function pubsub_admin_view_queue($args)
{
    if (!xarSecurityCheck('ManagePubSub')) return;
    
    extract($args);
    if (!xarVarFetch('action','str', $action, '')) return;
    if (!xarVarFetch('id','int', $id, 0)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObjectList(array('name' => 'pubsub_process'));

    if (!empty($action)) {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;

        switch ($action)
        {
            case 'process':
                if (!xarMod::apiFunc('pubsub','admin','processq')) {
                    return;
                }
                xarController::redirect(xarModURL('pubsub', 'admin', 'view_queue'));
                return true;
                break;

            case 'view':
                if (!empty($id)) {
                    // preview message ?
                }
                break;

            case 'delete':
                if (!empty($id)) {
                    if (!xarMod::apiFunc('pubsub','admin','deljob',array('id' => $id))) {
                        return;
                    }
                    xarController::redirect(xarModURL('pubsub', 'admin', 'view_process'));
                    return true;
                }
                break;

            default:
                break;
        }
    }
    return $data;

}

?>