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
    
    function mailer_user_display()
    {
        if (!xarSecurityCheck('ReadMailer')) return;

        if (!xarVarFetch('name',       'str',    $name,            'mailer_mailer', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('itemid' ,    'int',    $data['itemid'] , 0 ,          XARVAR_NOT_REQUIRED)) return;

        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['tplmodule'] = 'mailer';

        if (xarModIsAvailable('realms')) {
            $userrealmid = xarModAPIfunc('realms', 'admin', 'getrealmid');
            $realmid = xarModAPIfunc('realms', 'admin', 'getrealmid', array('itemid' => $data['itemid'], 'tablename' => 'mailer_mails'));
            if($userrealmid != 0 && $userrealmid != $realmid) return;
        }
        
        $data['object']->getItem(array('itemid' => $data['itemid']));
        $data['fields'] = $data['object']->getFieldValues();
        $data['mail'] = xarMod::apiFunc('mailer','user','prepare',array('id' => $data['itemid']));
        return $data;
    }
?>