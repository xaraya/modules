<?php
/**
 * Mailer Module
 *
 * @package modules
 * @subpackage mailer module
 * @copyright (C) 2010 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Modify an item of the mailer object
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function mailer_admin_modify()
    {
        if (!xarSecurityCheck('EditMailer')) return;

        if (!xarVarFetch('name',       'str',    $name,            'mailer_mailer', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('itemid' ,    'int',    $data['itemid'] , 0 ,          XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('confirm',    'bool',   $data['confirm'], false,       XARVAR_NOT_REQUIRED)) return;

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['tplmodule'] = 'mailer';

        if (xarModIsAvailable('realms')) {
            $userrealmid = xarModAPIfunc('realms', 'admin', 'getrealmid');
            $realmid = xarModAPIfunc('realms', 'admin', 'getrealmid', array('itemid' => $data['itemid'], 'tablename' => 'mailer_mails'));
            if($userrealmid != 0 && $userrealmid != $realmid) return;
        }
        
        if ($data['confirm']) {
        
            // Check for a valid confirmation key
            if(!xarSecConfirmAuthKey()) return;

            // Get the data from the form
            $isvalid = $data['object']->checkInput();
            
            if (!$isvalid) {
                // Bad data: redisplay the form with error messages
                return xarTplModule('mailer','admin','modify', $data);        
            } else {
                // Good data: create the item
                $item = $data['object']->updateItem();
                
                // Jump to the next page
                xarController::redirect(xarModURL('mailer','admin','view'));
                return true;
            }
        } else {
            $data['object']->getItem(array('itemid' => $data['itemid']));
        }
        return $data;
    }
?>