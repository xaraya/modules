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
 * Create a new item of a mimeobject
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function mime_admin_new()
    {
        if (!xarSecurity::check('AddMime')) {
            return;
        }

        if (!xarVar::fetch('name', 'str', $name, 'mime_types', xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('confirm', 'bool', $data['confirm'], false, xarVar::NOT_REQUIRED)) {
            return;
        }

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['tplmodule'] = 'mime';
        $data['authid'] = xarSec::genAuthKey('mime');

        if ($data['confirm']) {
        
            // we only retrieve 'preview' from the input here - the rest is handled by checkInput()
            if (!xarVar::fetch('preview', 'str', $preview, null, xarVar::DONT_SET)) {
                return;
            }

            // Check for a valid confirmation key
            if (!xarSec::confirmAuthKey()) {
                return;
            }
            
            // Get the data from the form
            $isvalid = $data['object']->checkInput();
            
            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                return xarTpl::module('mime', 'admin', 'new', $data);
            } else {
                // Good data: create the item
                $itemid = $data['object']->createItem();
                
                // Jump to the next page
                xarController::redirect(xarController::URL('mime', 'admin', 'view'));
                return true;
            }
        }
        return $data;
    }
