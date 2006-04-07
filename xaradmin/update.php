<?php
/**
* Update a publication
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
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('ebulletin','admin','modify') to update a current item
 *
 * @param  $ 'pid' the pid of the publication
 * @param  $ 'from' the from of the publication
 * @param  $ 'replyto' the replyto of the publication
 * @param  $ 'subject' the subject of the publication
 * @param  $ 'body' the body of the publication
 * @param  $ 'range' the range of the publication
 */
function ebulletin_admin_update($args)
{
    // security check
    if (!xarSecConfirmAuthKey()) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('id', 'id', $id, $id, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('template', 'str:1:', $template, $template, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('name', 'str:1:', $name, $name, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('description', 'str:1:', $description, $description, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('public', 'checkbox', $public, $public, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('from', 'str:1:', $from, $from, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fromname', 'str:1:', $fromname, $fromname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('replyto', 'str:1:', $replyto, $replyto, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('replytoname', 'str:1:', $replytoname, $replytoname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('subject', 'str:1:', $subject, $subject, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('html', 'checkbox', $html, $html, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startday', 'int', $startday, $startday, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('endday', 'int', $endday, $endday, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaulttheme', 'str:1:', $defaulttheme, $defaulttheme, XARVAR_NOT_REQUIRED)) return;

    // Argument check
    $invalid = array();
    $email_regexp = '/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i';
    if (empty($template) || !is_string($template)) {
        $invalid['template'] = 1;
        $template = '';
    }
    if (empty($name) || !is_string($name)) {
        $invalid['name'] = 1;
        $name = '';
    }
    if (empty($from) || !is_string($from) || !preg_match($email_regexp, $from)) {
        $invalid['from'] = 1;
        $from = '';
    }
    if (!empty($replyto) && !preg_match($email_regexp, $replyto)) {
        $invalid['replyto'] = 1;
        $replyto = '';
    }
    if (empty($subject) || !is_string($subject)) {
        $invalid['subject'] = 1;
        $subject = '';
    }
    if (!empty($defaulttheme) && !is_string($defaulttheme)) {
        $invalid['defaulttheme'] = 1;
        $defaulttheme = '';
    }
    if ($endday < $startday) {
        $invalid['range'] = 1;
    }

    // assemble array of data
    $data = array();
    $data['id']           = $id;
    $data['template']     = $template;
    $data['name']         = $name;
    $data['description']  = $description;
    $data['public']       = $public;
    $data['from']         = $from;
    $data['fromname']     = $fromname;
    $data['replyto']      = $replyto;
    $data['replytoname']  = $replytoname;
    $data['subject']      = $subject;
    $data['html']         = $html;
    $data['startday']     = $startday;
    $data['endday']       = $endday;
    $data['defaulttheme'] = $defaulttheme;

    // check if we have any errors
    if (count($invalid) > 0) {
        $data['invalid'] = $invalid;
        return xarModFunc('ebulletin', 'admin', 'modify', $data);
    }

    // let API function do the updating
    xarModAPIFunc('ebulletin', 'admin', 'update', $data);
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // set status message and redirect to publications view page
    xarSessionSetVar('statusmsg', xarML('Publication successfully updated!'));
    xarResponseRedirect(xarModURL('ebulletin', 'admin', 'modify', array('id' => $id)));

    // success
    return true;
}

?>
