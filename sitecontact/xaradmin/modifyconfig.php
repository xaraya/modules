<?php
/**
 * File: $Id$
 * 
 * SiteContact configuration modification
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
  * @subpackage SiteContact
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function sitecontact_admin_modifyconfig()
{
    $data = xarModAPIFunc('sitecontact', 'admin', 'menu');
    if (!xarSecurityCheck('AdminSiteContact')) return;
    $data['authid'] = xarSecGenAuthKey();
    // Specify some labels and values for display
    $data['customtext'] = xarModGetVar('sitecontact', 'customtext');
    $data['customtitle'] = xarModGetVar('sitecontact', 'customtitle');
    $data['optiontext'] = xarModGetVar('sitecontact', 'optiontext');
    $data['webconfirmtext'] = xarModGetVar('sitecontact', 'webconfirmtext');
    $notetouser = xarModGetVar('sitecontact', 'notetouser');
    if (!isset($notetouser) || (trim($notetouser)=='')) {
     $notetouser=$xarModGetVar('sitecontact','defaultnote');
    }
    $data['notetouser']=$notetouser;
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

    // Return the template variables defined in this function
    return $data;
}

?>
