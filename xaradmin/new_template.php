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
 * Create a new template
 *
 */
function pubsub_admin_new_template()
{
    if (!xarSecurityCheck('AddPubsub')) return;

    if (!xarVarFetch('name',    'str',  $name, 'pubsub_templates', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'bool', $data['confirm'], false,  XARVAR_NOT_REQUIRED)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    if(empty($data['object'])) return;

    if ($data['confirm']) {

        // Check for a valid confirmation key
        if(!xarSecConfirmAuthKey()) return;

        // Get the data from the form
        $isvalid = $data['object']->checkInput();

        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTplModule('pubsub','admin','new_template', $data);
        } else {
            // Good data: create the item
            $item = $data['object']->createItem();
            
            // Jump to the next page
            xarController::redirect(xarModURL('pubsub','admin','view_templates'));
            return true;
        }
    }
    // success
    return $data;
}
?>