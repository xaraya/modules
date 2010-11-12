<?php
/**
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
/**
 * 
 */
function messages_adminapi_replace($args)
{
    extract ($args);

    $sitename   = xarModVars::get('themes', 'SiteName');
    $siteslogan = xarModVars::get('themes', 'SiteSlogan');
    $siteadmin  = xarModVars::get('mail', 'adminname');
    $siteurl    = xarServer::getBaseURL();

    $name = xarUserGetVar('name');
    $uid = xarUserGetVar('id');

    $replacements = array(
		'/%%name%%/' => "$name",
		'/%%sitename%%/' => "$sitename",
		'/%%siteslogan%%/' => "$siteslogan",
		'/%%siteurl%%/' => "$siteurl",
		'/%%uid%%/' => "$uid",
		'/%%siteadmin%%/' => "$siteadmin"); 

    $replacements = array_merge($replacements, xarModVars::get('messages','searchstrings')); 

    $message = preg_replace($search,
                            $replace,
                            $message);

    $subject = preg_replace($search,
                            $replace,
                            $subject);

    $htmlmessage = preg_replace($search,
                                $replace,
                                $htmlmessage);

    return array('message'      => $message,
                 'subject'      => $subject,
                 'htmlmessage'  => $htmlmessage);

}
?>
