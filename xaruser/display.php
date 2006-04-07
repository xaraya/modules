<?php
/**
* Show an <iframe> in order to display an issue
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
 * show frame for displaying issue
 */
function ebulletin_user_display($args)
{
    extract($args);

    // get HTTP vars
    if (!xarVarFetch('id', 'int:1:', $id)) return;

    // get issue and pub
    $issue = xarModAPIFunc('ebulletin', 'user', 'getissue', array('id' => $id));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $issue['pid']));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('ReadeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) return;

    // format some vars
    $issue['published_string'] = ($issue['published']) ? xarML('Yes') : xarML('No');
    $issue['issuedate'] = xarLocaleGetFormattedDate('short', strtotime($issue['issuedate']));

    // links for the iframe
    $htmlurl = $pub['html'] ? xarModURL('ebulletin', 'user', 'displayissue', array('id' => $id, 'displaytype' => 'html')) : '';
    $txturl = xarModURL('ebulletin', 'user', 'displayissue',
        array('id' => $id, 'displaytype' => 'txt')
    );

    // initialize template data
    $data = xarModAPIFunc('ebulletin', 'user', 'menu', array('tab' => 'archive'));

    // set template vars
    $data['id']      = $id;
    $data['issue']   = $issue;
    $data['htmlurl'] = $htmlurl;
    $data['txturl']  = $txturl;

    return $data;

}

?>
