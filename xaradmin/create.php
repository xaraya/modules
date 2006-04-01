<?php
/**
* Create a new publication
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
 * Create a new publication
 */
function ebulletin_admin_create($args)
{
    // security check
    if (!xarSecConfirmAuthKey()) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('id', 'id', $id, $id, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('desc', 'str:1:', $desc, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('public', 'checkbox', $public, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('from', 'str:1:', $from, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fromname', 'str:1:', $fromname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('replyto', 'str:1:', $replyto, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('replytoname', 'str:1:', $replytoname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('subject', 'str:1:', $subject, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tpl_html', 'str:1:', $tpl_html, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tpl_txt', 'str:1:', $tpl_txt, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numsago', 'str:1:', $numsago, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('unitsago', 'enum:days:weeks:months:years', $unitsago, 'weeks', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startsign', 'enum:before:after', $startsign, 'before', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numsfromnow', 'str:1:', $numsfromnow, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('unitsfromnow', 'enum:days:weeks:months:years', $unitsfromnow, 'days', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('endsign', 'enum:before:after', $endsign, 'after', XARVAR_NOT_REQUIRED)) return;

    // Argument check
    $invalid = array();
    $email_regexp = '/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i';
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
    if (empty($tpl_html) && empty($tpl_txt)) {
        $invalid['template'] = 1;
        $tpl_html = $tpl_txt = '';
    }
    if (!is_numeric($numsago) || $numsago < 0) {
        $invalid['numsago'] = 1;
        $numsago = 1;
    }
    if (!is_numeric($numsfromnow) || $numsfromnow < 0) {
        $invalid['numsfromnow'] = 1;
        $numsfromnow = 0;
    }
    $beforesign = ($startsign == 'before') ? '-' : '+';
    $before = strtotime("$beforesign$numsago $unitsago");
    $aftersign = ($endsign == 'before') ? '-' : '+';
    $after = strtotime("$aftersign$numsfromnow $unitsfromnow");
    if ($before > $after) {
        $invalid['sequence'] = 1;
    }

    // assemble array of data
    $data = array();
    $data['name'] = $name;
    $data['desc'] = $desc;
    $data['public'] = $public;
    $data['from'] = $from;
    $data['fromname'] = $fromname;
    $data['replyto'] = $replyto;
    $data['replytoname'] = $replytoname;
    $data['subject'] = $subject;
    $data['tpl_html'] = $tpl_html;
    $data['tpl_txt'] = $tpl_txt;
    $data['numsago'] = $numsago;
    $data['unitsago'] = $unitsago;
    $data['startsign'] = $startsign;
    $data['numsfromnow'] = $numsfromnow;
    $data['unitsfromnow'] = $unitsfromnow;
    $data['endsign'] = $endsign;

    // check if we have any errors
    if (count($invalid) > 0) {
        $data['invalid'] = $invalid;
        return xarModFunc('ebulletin', 'admin', 'new', $data);
    }

    // let API function do the creating
    $id = xarModAPIFunc('ebulletin', 'admin', 'create', $data);
    if (!isset($id) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // set status message and redirect to publications view page
    xarSessionSetVar('statusmsg', xarML('Publication successfully created!'));
    xarResponseRedirect(xarModURL('ebulletin', 'admin', 'view'));

    // success
    return true;
}

?>
