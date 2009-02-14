<?php

function accessmethods_admin_view($args)
{
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby', 'str::', $sortby, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sla', 'str::', $sla, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('accesstype', 'str::', $accesstype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('webmasterid', 'int::', $webmasterid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('clientid', 'int::', $clientid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('inline', 'isset::', $inline, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('q', 'str::', $q, false, XARVAR_NOT_REQUIRED)) return;
 
    extract($args);
    
    if (!xarSecurityCheck('AdminAccessMethods')) {
        return false;
    }
    
    $data = xarModAPIFunc('accessmethods', 'admin', 'menu');

    $data['sortby'] = $sortby;
    $data['sla'] = $sla;
    $data['q'] = $q;
    $data['accesstype'] = $accesstype;
    $data['webmasterid'] = $webmasterid;
    $data['clientid'] = $clientid;
    $data['inline'] = $inline;
    
    $items = xarModAPIFunc('accessmethods', 'user', 'getall',
                            array('sortby'      => $sortby,
                                  'sla'         => $sla,
                                  'q'           => $q,
                                  'accesstype'  => $accesstype,
                                  'startnum'    => $startnum,
                                  'numitems'    => xarModGetVar('accessmethods','itemsperpage')));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        $items[$i]['link'] = xarModURL('accessmethods', 'admin', 'display',
                                        array('siteid' => $item['siteid']));
        if (xarSecurityCheck('CommentAccessMethods', 0, 'All', "$item[webmasterid]")) {
            $items[$i]['editurl'] = xarModURL('accessmethods', 'admin', 'modify',
                                                array('siteid' => $item['siteid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        if (xarSecurityCheck('ModerateAccessMethods', 0, 'All', "$item[webmasterid]")) {
            $items[$i]['deleteurl'] = xarModURL('accessmethods', 'admin', 'delete',
                                                array('siteid' => $item['siteid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
    }
    
    $data['items'] = $items;
    
    $uid = xarUserGetVar('uid');
    
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('accessmethods', 'user', 'countitems', 
                                            array('sla'       =>$sla,
                                                'accesstype'=>$accesstype)),
                                    xarModURL('accessmethods', 'admin', 'view', 
                                            array('startnum' => '%%',
                                                'sortby'    => $sortby,
                                                'sla'       =>$sla,
                                                'accesstype'=>$accesstype)),
                                    xarModGetUserVar('accessmethods', 'itemsperpage', $uid));
        
	return $data;
}

?>
