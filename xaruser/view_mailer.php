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
 * View item of the mailer_mails object. 
 *
 */
    sys::import('modules.dynamicdata.class.objects.master');
    
    function mailer_user_view_mailer()
    {
        if (!xarSecurityCheck('ReadMailer')) return;
        
        if (!xarVarFetch('name',       'str',    $name,            'mailer_mails', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('itemid' ,    'int',    $data['itemid'] , 0 ,          XARVAR_NOT_REQUIRED)) return;
        
        $data['object'] = DataObjectMaster::getObject(array('name' => $name));
        $data['object']->getItem(array('itemid' => $data['itemid']));
        $data['tplmodule'] = 'mailer';
        
        return $data;
    }
?>