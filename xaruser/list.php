<?php
/**
 * Polls Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * list polls
 */
function polls_user_list($args)
{
    extract($args);

    if (!xarVarFetch('catid','str',$catid,0,XARVAR_DONT_SET)) return;

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if(!xarSecurityCheck('ListPolls', 1)){
        return;
    }

    $items = xarModAPIFunc('polls',
                          'user',
                          'getall',
                          array('modid' => xarModGetIDFromName('polls'),
                                'catid' => $catid));
    $data = array();
    $data['catid'] = $catid;

    if (!$items) {
        return $data;
    }

    // The return value of the function is checked here, and if the function
    // suceeded then an appropriate message is posted.  Note that if the
    // function did not succeed then the API function should have already
    // posted a failure message so no action is required

    $data['previewresults'] = xarModVars::Get('polls', 'previewresults');
    $data['showtotalvotes'] = xarModVars::Get('polls', 'showtotalvotes');
    $data['polls'] = array();
    // TODO - loop through each item and display it
    foreach ($items as $item) {
        $poll = array();

                switch ($item['type']) {
            //case 'single':
            case 0:
                $poll['type'] = xarML('Single');
                break;
            //case 'multi':
            case 1:
                $poll['type'] = xarML('Multiple');
                break;
        }

        $poll['title'] = $item['title'];
        //$poll['type'] = $item['type'];
        $poll['private'] = $item['private'];
        $poll['votes'] = $item['votes'];
        $poll['start_date'] = $item['start_date'];
        $poll['end_date'] = $item['end_date'];
        if($item['open'] == '1'){
            $poll['open'] = 1;
        } else {
            $poll['open'] = 0;
        }

        if (xarSecurityCheck('VotePolls',0,'Polls',"$item[pid]:$item[type]")) {


            $poll['canvote'] = xarModAPIFunc('polls',
                                             'user',
                                             'usercanvote',
                                             array('pid' => $item['pid']));

            $poll['action_vote'] = xarModURL('polls', 'user', 'display',
                                             array('pid' => $item['pid']));
        } else {
            $poll['canvote'] = 0;
        }

        $poll['action_results'] = xarModURL('polls', 'user', 'results',
                                  array('pid' => $item['pid']));

        $data['polls'][] = $poll;
    }

    return $data;
}

?>
