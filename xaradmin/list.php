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
 * list polls
 */
function polls_admin_list()
{

    //extract($args);

    if (!xarVarFetch('status', 'int:1:4', $status, 1, XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('AdminPolls')){ return;}


    if ($status == 4) {
        $stat = null;
    } else {
        $stat = $status;
    }

    $data = array();
    $data['status'] = $status;
    $authid = xarSecGenAuthKey();

    $polls = xarModAPIFunc('polls', 'user', 'getall', array('status' => $stat));

    if (empty($polls)) {
        return $data;
    }

    $data['polls'] = array();

    foreach ($polls as $poll) {

        $row = $poll; //array();
        //$options = array();

        //$row['title'] = $poll['title'];
        //$row['start_date'] = $poll['start_date'];
        //$row['end_date']   = $poll['end_date'];
        //$row['private']    = $poll['private'];
        //$row['votes']      = $poll['votes'];

        // FIXME: overwriting a flag with a translated string just makes things harder in the template.
        switch ($poll['type']) {
            case 'single':
                $row['type'] = xarML('Single');
                break;
            case 'multi':
                $row['type'] = xarML('Multiple');
                break;
        }

        // FIXME: overwriting a flag with a translated string just makes things harder in the template.
        if ($poll['open'] == 0) {
                $row['open'] = xarML('Closed');
        } else {
                $row['open'] = xarML('Open');
        }

        $modinfo = xarModGetInfo($poll['modid']);

        $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',array(), 0);

        if ($poll['itemtype'] == 0) {
            $moditem['modname'] = ucwords($modinfo['displayname']). ' - ' . $poll['itemid'];
        } else {
            if (isset($mytypes) && !empty($mytypes[$poll['itemtype']])) {
                $moditem['modname'] = ucwords($modinfo['displayname']) . ' - ' . $mytypes[$poll['itemtype']]['label'] . ' - ' . $poll['itemid'];
            } else {
                $moditem['modname'] = ucwords($modinfo['displayname']) . ' ' . $poll['itemtype']. ' - ' . $poll['itemid'];
            }
        }

        // TODO: add link to itemid
        if ($modinfo['displayname'] == 'Polls') {
            $row['hook'] = null;
        } else {
            $row['hook'] = $moditem['modname'];
        }

        // So long as the poll is open or upcoming, offer a close link.
        if (($poll['state'] == 'open' ||  $poll['state'] == 'upcoming') && $poll['open']) {
            $row['close_confirm'] = xarML('Are you sure to close poll "#(1)"', addslashes($poll['title']));

            $row['action_close'] = xarModURL(
                'polls', 'admin', 'close',
                array('pid' => $poll['pid'], 'authid' => $authid, 'status' => $status)
            );

            $row['action_modify'] = xarModURL(
                'polls', 'admin', 'modify', 
                array('pid' => $poll['pid'])
            );
        }

        // If the poll is closed, offer a reopen link.
        if ($poll['state'] == 'closed' || !$poll['open']) {
            $row['open_confirm'] = xarML('Are you sure to reopen poll "#(1)"', addslashes($poll['title']));

            $row['action_open'] = xarModURL(
                'polls', 'admin', 'open',
                array('pid' => $poll['pid'], 'authid' => $authid, 'status' => $status)
            );
        }

        // The poll may be reset if it is open and has at least one vote.
        if ($row['votes'] > 0 && $poll['state'] == 'open' && $poll['open']) {
            $row['action_reset'] = xarModURL(
                'polls', 'admin', 'reset', 
                array('pid' => $poll['pid'], 'authid' => $authid)
            );
        }

        $row['delete_confirm'] = xarML('Are you sure to delete poll "#(1)"', addslashes($poll['title']));
        $row['action_delete'] = xarModURL(
            'polls', 'admin', 'delete',
            array('pid' => $poll['pid'], 'authid' => $authid, 'status' => $status)
        );

        $data['polls'][] = $row;
    }

    return $data;
}

?>
