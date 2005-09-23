<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitecontact
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 * @author Jo Dalle Nogare
 */
function sitecontact_admin_modifyconfig()
{
    $data = xarModAPIFunc('sitecontact', 'admin', 'menu');
    if (!xarSecurityCheck('AdminSiteContact')) return;
    $data['authid'] = xarSecGenAuthKey();
    /* Specify some labels and values for display */
    $data['customtext'] = xarModGetVar('sitecontact', 'customtext');
    $data['customtitle'] = xarModGetVar('sitecontact', 'customtitle');
    $data['optiontext'] = xarModGetVar('sitecontact', 'optiontext');
    $data['usehtmlemail']= xarModGetVar('sitecontact', 'usehtmlemail');
    $data['allowcopy'] = xarModGetVar('sitecontact', 'allowcopy');
    $data['webconfirmtext'] = xarModGetVar('sitecontact', 'webconfirmtext');
    $notetouser = xarModGetVar('sitecontact', 'notetouser');
    if (!isset($notetouser) || (trim($notetouser)=='')) {
     $notetouser=xarModGetVar('sitecontact','defaultnote');
    }
    $data['notetouser']=$notetouser;

    $scdefaultemail = xarModGetVar('sitecontact', 'scdefaultemail');

    if (!isset($scdefaultemail) || (trim($scdefaultemail)=='')) {
     $scdefaultemail=xarModGetVar('mail','adminmail');
    }
    $data['scdefaultemail']= $scdefaultemail;

    $scdefaultname = xarModGetVar('sitecontact', 'scdefaultname');

    if (!isset($scdefaultname) || ($scdefaultname)=='') {
     $scdefaultname=xarModGetVar('mail','adminname');
    }
    $data['scdefaultname']= $scdefaultname;

    $data['shorturlschecked'] = xarModGetVar('sitecontact', 'SupportShortURLs') ? 'checked' : '';

    $hooks = xarModCallHooks('module', 'modifyconfig', 'sitecontact',
        array('module' => 'sitecontact'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    /* Return the template variables defined in this function */
    return $data;
}
?>