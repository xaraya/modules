<?php
/**
 * Main administration
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Contact Form Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * the main administration function
 * @param none
 * @return array
 */
function contactform_admin_main()
{
    // Check to see the current user has edit access to the contactform module
    if (!xarSecurityCheck('EditContactForm')) return;

    $refererinfo = xarRequest::getInfo(xarServer::getVar('HTTP_REFERER'));
    $info = xarRequest::getInfo();
    $samemodule = $info[0] == $refererinfo[0];

     $filters['where'] = 'name eq "contactform_default"';

     $object = DataObjectMaster::getObjectList(array(
                                    'name' => 'objects',
                                ));
     $items = $object->getItems($filters);
     $item = end($items);
     $data['objectid'] = $item['objectid'];

    if (((bool)xarModVars::get('modules', 'disableoverview') == false) || $samemodule){
        return xarTplModule('contactform','admin','overview', $data);
    } else {
        xarResponse::Redirect(xarModURL('contactform', 'admin', 'view'));
        return true;
    }
}

?>