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
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Delete a biller template
 *
 */

function pubsub_admin_delete_template()
{
    if (!xarSecurityCheck('ManageBiller')) return;

    if (!xarVarFetch('name',       'str:1',  $name,            'pubsub_templates', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid' ,    'int',    $data['itemid'] , '' ,                XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'str:1',  $data['confirm'], false,              XARVAR_NOT_REQUIRED)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    $data['tplmodule'] = 'pubsub';
    $data['authid'] = xarSecGenAuthKey('pubsub');

    if ($data['confirm']) {
    
        // Check for a valid confirmation key
        if(!xarSecConfirmAuthKey()) return;

        // Delete the item
        $item = $data['object']->deleteItem();
            
        // Jump to the next page
        xarController::redirect(xarModURL('pubsub','admin','view_templates'));
        return true;
    }
    return $data;
}
?>