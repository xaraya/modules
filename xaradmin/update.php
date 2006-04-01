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
    if (!xarVarFetch('name', 'str:1:', $name, $name, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('desc', 'str:1:', $desc, $desc, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('public', 'checkbox', $public, $public, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('from', 'str:1:', $from, $from, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fromname', 'str:1:', $fromname, $fromname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('replyto', 'str:1:', $replyto, $replyto, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('replytoname', 'str:1:', $replytoname, $replytoname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('subject', 'str:1:', $subject, $subject, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tpl_html', 'str:1:', $tpl_html, $tpl_html, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tpl_txt', 'str:1:', $tpl_txt, $tpl_txt, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numsago', 'str:1:', $numsago, $numsago, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('unitsago', 'enum:days:weeks:months:years', $unitsago, $unitsago, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startsign', 'enum:before:after', $startsign, $startsign, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numsfromnow', 'str:1:', $numsfromnow, $numsfromnow, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('unitsfromnow', 'enum:days:weeks:months:years', $unitsfromnow, $unitsfromnow, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('endsign', 'enum:before:after', $endsign, $endsign, XARVAR_NOT_REQUIRED)) return;

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
    $data['id'] = $id;
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
        return xarModFunc('ebulletin', 'admin', 'modify', $data);
    }

    // let API function do the updating
    $id = xarModAPIFunc('ebulletin', 'admin', 'update', $data);
    if (empty($id) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // set status message and redirect to publications view page
    xarSessionSetVar('statusmsg', xarML('Publication successfully updated!'));
    xarResponseRedirect(xarModURL('ebulletin', 'admin', 'view'));

    // success
    return true;
}

?>
