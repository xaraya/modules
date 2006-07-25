<?php
/**
 * Access Methods Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Access Methods Module
 * @link http://xaraya.com/index.php/release/333.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
function accessmethods_admin_create($args)
{
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

    $siteid = xarModAPIFunc('accessmethods',
                        'admin',
                        'create',
                        array('clientid' 	=> $clientid,
                            'webmasterid'	=> $webmasterid,
                            'site_name'	    => $site_name,
                            'accesstype'	    => $accesstype,
                            'url'	        => $url,
                            'description'	=> $description,
                            'status'	    => $status,
                            'sla'		    => $sla,
                            'accesslogin'	=> $accesslogin,
                            'accesspwd'	    => $accesspwd,
                            'related_sites'	=> $related_sites));


    if (!isset($siteid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    xarSessionSetVar('statusmsg', xarMLByKey('ACCESSCREATED'));

    xarResponseRedirect(xarModURL('accessmethods', 'admin', 'view'));

    return true;
}

?>
