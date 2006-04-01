<?php
/**
* Display GUI for creating a new publication
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
* Display GUI for creating a new publication
*/
function ebulletin_admin_new($args)
{
    // security check
    if (!xarSecurityCheck('AddeBulletin')) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('invalid', 'array', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, $name, XARVAR_NOT_REQUIRED)) return;
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

    // get defaults for publication vars
    if (empty($name))         $name = '';
    if (empty($desc))         $desc = '';
    if (empty($public))       $public = 0;
    if (empty($from))         $from = '';
    if (empty($fromname))     $fromname = '';
    if (empty($replyto))      $replyto = '';
    if (empty($replytoname))  $replytoname = '';
    if (empty($subject))      $subject = '';
    if (empty($tpl_txt))      $tpl_txt = '';
    if (empty($tpl_html))     $tpl_html = '';
    if (empty($numsago))      $numsago = xarModGetVar('ebulletin', 'issuenumsago');
    if (empty($unitsago))     $unitsago = xarModGetVar('ebulletin', 'issueunitsago');
    if (empty($startsign))    $startsign = xarModGetVar('ebulletin', 'issuestartsign');
    if (empty($numsfromnow))  $numsfromnow = xarModGetVar('ebulletin', 'issuenumsfromnow');
    if (empty($unitsfromnow)) $unitsfromnow = xarModGetVar('ebulletin', 'issueunitsfromnow');
    if (empty($endsign))      $endsign = xarModGetVar('ebulletin', 'issueendsign');

    // get other vars
    $authid = xarSecGenAuthKey();
    $templates = xarModAPIFunc('ebulletin', 'user', 'gettemplates');
    if (empty($templates) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    $template_dir = xarModGetVar('ebulletin', 'template_dir');

    // get hooks
    $hookoutput = xarModCallHooks('item', 'new', '', array('module' => 'ebulletin'));

    // get units
    $units = array();
    $units[] = array('days', xarML('Days'));
    $units[] = array('weeks', xarML('Weeks'));
    $units[] = array('months', xarML('Months'));
    $units[] = array('years', xarML('Years'));

    // get signs
    $signs = array();
    $signs[] = array('before', xarML('before'));
    $signs[] = array('after', xarML('after'));

    // initialize template array
    $data = xarModAPIFunc('ebulletin', 'admin', 'menu');

    // set publiation vars
    $data['name']         = $name;
    $data['desc']         = $desc;
    $data['public']       = $public;
    $data['from']         = $from;
    $data['fromname']     = $fromname;
    $data['replyto']      = $replyto;
    $data['replytoname']  = $replytoname;
    $data['subject']      = $subject;
    $data['tpl_txt']      = $tpl_txt;
    $data['tpl_html']     = $tpl_html;
    $data['numsago']      = $numsago;
    $data['unitsago']     = $unitsago;
    $data['startsign']    = $startsign;
    $data['numsfromnow']  = $numsfromnow;
    $data['unitsfromnow'] = $unitsfromnow;
    $data['endsign']      = $endsign;

    // set other vars
    $data['authid']    = $authid;
    $data['invalid']   = $invalid;
    $data['templates'] = $templates;
    $data['units']     = $units;
    $data['signs']     = $signs;
    $data['template_dir'] = $template_dir;
    $data['hookoutput']   = $hookoutput;

    return $data;
}

?>
