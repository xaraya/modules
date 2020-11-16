<?php
/**
 * Mime Module
 *
 * @package modules
 * @subpackage mime module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/999
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Delete an item
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function mime_admin_delete()
    {
        if (!xarSecurity::check('ManageMime')) {
            return;
        }

        if (!xarVar::fetch('name', 'str:1', $name, 'mime_types', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('itemid', 'int', $data['itemid'], '', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('confirm', 'str:1', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
            return;
        }

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['object']->getItem(array('itemid' => $data['itemid']));

        $data['tplmodule'] = 'mime';
        $data['authid'] = xarSec::genAuthKey('mime');

        if ($data['confirm']) {
        
            // Check for a valid confirmation key
            if (!xarSec::confirmAuthKey()) {
                return;
            }

            // Delete the item
            $item = $data['object']->deleteItem();
                
            // Jump to the next page
            xarController::redirect(xarController::URL('mime', 'admin', 'view'));
            return true;
        }
        return $data;
    }
