<?php
/*
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Recommend Module
 */
/**
 * Modify the confirmation email for users
 */
function recommend_admin_modifyconfig()
{ 
    /* Security Check */
    if (!xarSecurityCheck('EditRole')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($phase)) {
        case 'modify':
        default:

            $data['title']          = xarModGetVar('recommend', 'title');
            $data['template']       = xarModGetVar('recommend', 'template');
            $data['numbersent']     = xarModGetVar('recommend', 'numbersent');
            if (empty($data['numbersent'])){
                $data['numbersent'] = '0';
            }
            $data['lastemailaddy']  = xarModGetVar('recommend', 'lastsentemail');
            if (empty($data['lastemailaddy'])){
                $data['lastemailaddy'] = '';
            }
            $data['lastemailname']  = xarModGetVar('recommend', 'lastsentname');
            if (empty($data['lastemailname'])){
                $data['lastemailname'] = '';
            }
            $data['date']           = xarModGetVar('recommend', 'date');
            $data['username']       = xarModGetVar('recommend', 'username');
            $data['authid']         = xarSecGenAuthKey(); 
            $data['submitlabel']    = xarML('Submit');
            $data['shorturlschecked'] = xarModGetVar('recommend', 'SupportShortURLs') ? 'checked' : '';

            /* dynamic properties (if any) */
            /*
            $data['properties'] = null;
            if (xarModIsAvailable('dynamicdata')) {
                // get the Dynamic Object defined for this module (and itemtype, if relevant)
                $object = &xarModAPIFunc('dynamicdata', 'user', 'getobject',
                    array('module' => 'roles'));
                if (isset($object) && !empty($object->objectid)) {
                    // get the Dynamic Properties of this object
                    $data['properties'] = &$object->getProperties();
                } 
            } 
            */
            break;

        case 'update':

            if (!xarVarFetch('template', 'str:1:', $template)) return;
            if (!xarVarFetch('title', 'str:1:', $title)) return;
            if (!xarVarFetch('usernote', 'checkbox', $usernote, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('storerecommendations', 'checkbox', $storerecommendations, false, XARVAR_NOT_REQUIRED)) return;
            /* Confirm authorisation code */
            if (!xarSecConfirmAuthKey()) return;

            xarModSetVar('recommend', 'template', $template);
            xarModSetVar('recommend', 'title', $title);
            xarModSetVar('recommend', 'usernote', $usernote);
            xarModSetVar('recommend', 'storerecommendations', $storerecommendations);
            xarModSetVar('recommend', 'SupportShortURLs', $shorturls);

            xarResponseRedirect(xarModURL('recommend', 'admin', 'modifyconfig'));
            return true;

            break;
    } 

    return $data;
} 

?>