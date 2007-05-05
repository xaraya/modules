<?php
/**
 * Default user function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Members module
 */
/**
 * Display member listing as pdf
 */
function members_user_pdfview($args)
{
    if(!xarVarFetch('startnum', 'int:1', $data['startnum'], 1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('phase', 'enum:active:viewall', $phase, 'viewall', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('filetype', 'str:1', $filetype, '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('letter', 'str:1', $letter, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('search', 'str:1:100', $search, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('order', 'str', $order, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('cids', 'array', $cids, NULL, XARVAR_DONT_SET)) {return;}

    $role = xarRoles::get(xarModVars::get('members','defaultgroup'));
    $data['defaultgroup'] = $role->name;
    if (!xarSecurityCheck('ReadMembers')) return;
    $data['pdfmessage']='';
    $data['filetype'] = 'pdf';

    $html = xarTplModule('members','user','view', $data);

    if (xarModIsAvailable('export')) {
        xarModAPIFunc('export','user','export',array('filetype' => 'pdf', 'html' => $html));
        exit;
    } else {
      $data['pdfmessage'] = xarML('The PDF directory function is currently not available');
      return xarTplModule('members','user','view',$data);
    }

}

?>