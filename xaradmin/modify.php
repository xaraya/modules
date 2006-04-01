<?php
/**
* GUI for modifying a publication
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
 * modify an item
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 *
 * @param  $ 'id' the id of the item to be modified
 */
function ebulletin_admin_modify($args)
{
    extract($args);

    // get HTTP vars
    if (!xarVarFetch('id', 'id', $id)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, $name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('desc', 'str:1:', $desc, $desc, XARVAR_NOT_REQUIRED)) return;
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

    // make sure we have an ID
    if (!empty($objectid)) $id = $objectid;

    // retrieve this publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $id));
    if (!isset($pub) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('EditeBulletin', 1, 'Publication', "$pub[name]:$id")) return;

    // get publication vars
    if (!isset($name))         $name = $pub['name'];
    if (!isset($desc))         $desc = $pub['desc'];
    if (!isset($public))       $public = $pub['public'];
    if (!isset($from))         $from = $pub['from'];
    if (!isset($fromname))     $fromname = $pub['fromname'];
    if (!isset($replyto))      $replyto = $pub['replyto'];
    if (!isset($replytoname))  $replytoname = $pub['replytoname'];
    if (!isset($subject))      $subject = $pub['subject'];
    if (!isset($tpl_txt))      $tpl_txt = $pub['tpl_txt'];
    if (!isset($tpl_html))     $tpl_html = $pub['tpl_html'];
    if (!isset($numsago))      $numsago = $pub['numsago'];
    if (!isset($unitsago))     $unitsago = $pub['unitsago'];
    if (!isset($startsign))    $startsign = $pub['startsign'];
    if (!isset($numsfromnow))  $numsfromnow = $pub['numsfromnow'];
    if (!isset($unitsfromnow)) $unitsfromnow = $pub['unitsfromnow'];
    if (!isset($endsign))      $endsign = $pub['endsign'];

    // get other vars
    $authid = xarSecGenAuthKey();
    $templates = xarModAPIFunc('ebulletin', 'user', 'gettemplates');
    if (empty($templates) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    $template_dir = xarModGetVar('ebulletin', 'template_dir');

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

    // get hooks
    $item = $pub;
    $item['module'] = 'ebulletin';
    $hookoutput = xarModCallHooks('item', 'modify', $id, $item);

    // initialize template array
    $data = xarModAPIFunc('ebulletin', 'admin', 'menu');

    // set publiation vars
    $data['id']           = $id;
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
    $data['hookitems'] = &$hookoutput;

    return $data;
}

?>
