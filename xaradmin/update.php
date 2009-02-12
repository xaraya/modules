<?php

function accessmethods_admin_update($args)
{
    if (!xarVarFetch('siteid', 'id', $siteid, $siteid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('clientid', 'id', $clientid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('webmasterid', 'id', $webmasterid, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('site_name', 'str:1:', $site_name, $site_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('accesstype', 'str:1:', $accesstype, $accesstype, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('url', 'str::', $url, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'html:basic', $description, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status', 'str:1:', $status, $status, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sla', 'str:1:', $sla, $sla, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('accesslogin', 'str::', $accesslogin, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('accesspwd', 'str::', $accesspwd, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('related_sites', 'array::', $related_sites, $related_sites, XARVAR_NOT_REQUIRED)) return;

    extract($args);
    if (!xarSecConfirmAuthKey()) return;
    if(!xarModAPIFunc('accessmethods',
					'admin',
					'update',
					array('siteid'	    => $siteid,
						'clientid' 	    => $clientid,
                        'webmasterid'	=> $webmasterid,
                        'site_name'	    => $site_name,
                        'accesstype'	=> $accesstype,
                        'url'	        => $url,
                        'description'	=> $description,
                        'status'	    => $status,
                        'sla'		    => $sla,
                        'accesslogin'   => $accesslogin,
                        'accesspwd'	    => $accesspwd,
                        'related_sites'	=> $related_sites))) {
		return;
	}


	xarSessionSetVar('statusmsg', xarML('Access Method Updated'));

    xarResponseRedirect(xarModURL('accessmethods', 'admin', 'view'));

    return true;
}

?>
