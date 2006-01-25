<?php
/**
* Validate a subscribe request
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Validate a subscribe request
*
*/
function ebulletin_user_validatesubscriber($args)
{
    extract($args);

    // if they're logged in, they don't need to be here
    if (xarUserIsLoggedIn()) {
        xarSessionSetVar('statusmsg', xarML('Validation only needed for unregistered members.  Please log out if you need to validate a subscription update.'));
        xarResponseRedirect(xarModURL('ebulletin', 'user', 'main'));
        return true;
    }

    // get phase (defer other xarVarFetch() calls to specific phases
    if (!xarVarFetch('phase', 'str:1', $phase, '', XARVAR_NOT_REQUIRED)) return;

    switch($phase) {
        case 'init':
        default:

            // get HTTP vars
            if (!xarVarFetch('subscriptions', 'array', $subscriptions, array(), XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('email', 'str:1:', $email, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('name', 'str:1:', $name, '', XARVAR_NOT_REQUIRED)) return;

            // set defaults
            if (empty($subscriptions)) $subscriptions = array();
            if (empty($email)) $email = '';
            if (empty($name)) $name = '';

            // validate vars
            $invalid = array();
            $email_regexp = '/^[a-z0-9]+([_\\.-][a-z0-9]+)*'
                . '@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i';
            if (!preg_match($email_regexp, $email)) {
                $invalid['email'] = 1;
            }
            if (empty($name)) {
                $invalid['name'] = 1;
            }

            // put vars together for ease of handling
            $i = array();
            $inputs['email'] = $email;
            $inputs['name'] = $name;
            $inputs['subscriptions'] = $subscriptions;

            // check if we have any errors
            if (count($invalid) > 0) {
                $inputs['invalid'] = $invalid;
                return xarModFunc('ebulletin', 'user', 'main', $inputs);
            }

            // generate validation code
            $code = substr($email, 0, 1).md5($email.microtime());

            // put everything in a module var, keyed by code
            xarModSetVar('ebulletin', $code, serialize($inputs));

            // get vars
            $adminname  = xarModGetVar('mail', 'adminname');
            $adminemail = xarModGetVar('mail', 'adminmail');
            $sitename   = xarModGetVar('themes', 'SiteName');
            $siteslogan = xarModGetVar('themes', 'SiteSlogan');
            $footer     = xarModGetVar('themes', 'Footer');
            $baseurl    = xarServerGetBaseURL();
            $subject    = xarML('Your subscription change at #(1)', $sitename);

            // generate validation URL
            $validateurl = $baseurl;
            $validateurl = preg_replace("/(\w+\.php)?\$/i", '', $validateurl);
            $validateurl = preg_replace("/\/+\$/i", '', $validateurl);
            $validateurl .= "/ebval.php?phase=val&c=$code";

            // generate message
            $message = xarTplModule('ebulletin', 'user', 'getvalidationemail', array(
                'adminname'   => $adminname,
                'adminemail'  => $adminemail,
                'name'        => $name,
                'email'       => $email,
                'validateurl' => $validateurl,
                'subject'     => $subject,
                'sitename'    => $sitename,
                'siteslogan'  => $siteslogan,
                'footer'      => $footer,
                'baseurl'     => $baseurl
            ));

            // send email
            $mail = array();
            $mail['info']        = $email;
            $mail['name']        = $name;
            $mail['from']        = $adminemail;
            $mail['fromname']    = $adminname;
            $mail['subject']     = $subject;
            $mail['message']     = strip_tags($message);
            $mail['htmlmessage'] = $message;
            $mail['priority']    = 1;

            // try sending mail
            if (!xarModAPIFunc('mail', 'admin', 'sendhtmlmail', $mail)) return;

            // initialize template vars
            $data = xarModAPIFunc('ebulletin', 'user', 'menu');

            // set template vars
            $data               = array_merge($data, $inputs);
            $data['email']      = xarVarPrepEmailDisplay($email);
            $data['name']       = xarVarPrepForDisplay($name);
            $data['adminname']  = xarVarPrepForDisplay($adminname);
            $data['adminemail'] = xarVarPrepEmailDisplay($adminemail);
            $data['subject']    = $subject;
            $data['phase']      = $phase;

            return $data;
            break;

        // completion of a subscription request
        case 'val':
            if (!xarVarFetch('code', 'str:33:33', $code)) return;

            // retrieve user's request info
            $request = @unserialize(xarModGetVar('ebulletin', $code));
            if (empty($request)) {
                $msg = xarML('Unable to match validation code.  Could not complete your subscription request.  (Have you completed it once already?)');
                xarErrorSet(XAR_USER_EXCEPTION, 'NO_SUCH_CODE', new SystemException($msg));
                return;
            }

            // let API func do the updating
            if (!xarModAPIFunc('ebulletin', 'user', 'updatesubscriptions', $request)) return;

            // initialize template vars
            $data = xarModAPIFunc('ebulletin', 'user', 'menu');

            // set template vars
            $data          = array_merge($data, $request);
            $data['phase'] = $phase;
            $data['email'] = xarVarPrepEmailDisplay($request['email']);
            $data['name']  = xarVarPrepForDisplay($request['name']);

            return $data;
            break;
    }

}

?>