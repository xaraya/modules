<?php
/**
* Display / Download an issue
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
 * Display / Download an issue
 */
function ebulletin_user_displayissue()
{
    // get HTTP vars
    if (!xarVarFetch('id', 'int:1:', $id)) return;
    if (!xarVarFetch('displaytype', 'enum:html:txt', $displaytype, 'txt', XARVAR_NOT_REQUIRED)) return;

    // validate inputs
    if (empty($displaytype)) $displaytype = 'txt';

    // get issue
    $issue = xarModAPIFunc('ebulletin', 'user', 'getissue', array('id' => $id));
    if (empty($issue) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('ReadeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) return;

    // push output to screen
    if ($displaytype == 'html') {
        echo $issue['body_html'];
    } else {
        echo nl2br(htmlspecialchars($issue['body_txt']));
    }

    // make sure Xaraya doesn't try to display something
    exit;

}

?>
