<?php
/**
 * Polls module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Polls Module
 * @link http://xaraya.com/index.php/release/23.html
 * @author Jim McDonalds, dracos, mikespub et al.
 */
/**
 * update a poll
 */
function polls_admin_update()
{

    // Get parameters
    if (!xarVarFetch('pid', 'id', $pid)) return;
    if (!xarVarFetch('polltype', 'str:1:', $type, 'single', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private', 'int:0:1', $private, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('title', 'str:1:', $title, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('start_date', 'str:1:', $start_date, time(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('end_date', 'str:1:', $end_date, NULL, XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    if ((!isset($title))) throw new EmptyParameterException('title');

    if($private != 1){
        $private = 0;
    }

    if (!empty($start_date)) {
        $start_date .= ' GMT';
        $start_date = strtotime($start_date);
        // adjust for the user's timezone offset
        $start_date -= xarMLS_userOffset() * 3600;
        }


    if (!empty($end_date)) {
        $end_date .= ' GMT';
        $end_date = strtotime($end_date);
        // adjust for the user's timezone offset
        $end_date -= xarMLS_userOffset() * 3600;
    }

    // Get poll info
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    if (!$poll) {
        throw new BadParameterException($pid,'Poll not found (#(1))');
    }

    // security check
    if (!xarSecurityCheck('EditPolls',1,'Polls',"$poll[pid]:$poll[type]")) {
        return;
    }
    $options = $poll['options'];

    $title = preg_replace("/:/", "&#58", $title);

    // Pass to API
    $updated = xarModAPIFunc('polls',
                      'admin',
                      'update',
                      array('pid' => $pid,
                           'title' => $title,
                           'type' => $type,
                           'private' => $private,
                           'start_date' => $start_date,
                           'end_date' => $end_date));
    if(!$updated){
       return false;
    }

    // Success
    xarSessionSetVar('statusmsg', xarML('Poll Updated'));

    xarResponseRedirect(xarModURL('polls', 'admin', 'list'));

    return true;
}

?>
