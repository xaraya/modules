<?php
/**
* View issues
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
 * view issues
 */
function ebulletin_user_viewissues()
{
    // security check
    if (!xarSecurityCheck('ReadeBulletin')) return;

    // get HTTP vars
    if (!xarVarFetch('pid', 'id', $pid)) return;
    if (!xarVarFetch('startnum', 'str:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'str:1:', $numitems, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('order', 'enum:id:subject:date:published', $order, 'date', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sort', 'enum:ASC:DESC', $sort, 'DESC', XARVAR_NOT_REQUIRED)) return;

    // get other vars
    if (empty($numitems) || !is_numeric($numitems)) {
        $numitems = xarSessionGetVar('ebulletin_issuesperpage');
        if (empty($numitems)) {
            $numitems = xarModGetVar('ebulletin', 'admin_issuesperpage');
        }
    } else {
        xarSessionSetVar('ebulletin_issuesperpage', $numitems);
    }

    $nextsort = ($sort == 'ASC') ? 'DESC' : 'ASC';
    $sort_img = xarTplGetImage('s_'.strtolower($sort).'.png');
    $currenturl = xarServerGetCurrentURL();

    // get publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $pid));
    if (empty($pager) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // get pager
    $pager = xarTplGetPager(
        $startnum,
        xarModAPIFunc('ebulletin', 'user', 'countissues',
            array('pid' => $pid, 'published' => true)
        ),
        xarServerGetCurrentURL(array('startnum' => '%%', 'published' => true)),
        $numitems
    );
    if (empty($pager) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // get issues
    $issues = xarModAPIFunc('ebulletin', 'user', 'getallissues',
        array(
            'startnum'  => $startnum,
            'numitems'  => $numitems,
            'order'     => $order,
            'sort'      => $sort,
            'pid'       => $pid,
            'published' => true
        )
    );
    if (empty($issues) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // add options for each issue
    foreach ($issues as $index => $issue) {

        // format vars
        $issue['issuedate'] = xarLocaleGetFormattedDate('short', strtotime($issue['issuedate']));

        if (xarSecurityCheck('VieweBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {

            // view
            $issue['viewurl'] = array(
                'url' => xarModURL('ebulletin', 'user', 'display',
                    array('id' => $issue['id'])
                ),
                'title' => xarML('View this issue'),
                'label' => empty($issue['subject']) ? xarML('(No subject)') : $issue['subject']
            );
        }

        // add modified issue back into array
        $issues[$index] = $issue;

    }

    // initialize template data
    $data = xarModAPIFunc('ebulletin', 'user', 'menu', array('tab' => 'archive'));

    // set template vars
    $data['issues']     = $issues;
    $data['pub']        = $pub;
    $data['pager']      = $pager;
    $data['sort']       = $sort;
    $data['nextsort']   = $nextsort;
    $data['sort_img']   = $sort_img;
    $data['order']      = $order;
    $data['currenturl'] = $currenturl;
    $data['numitems']   = $numitems;

    return $data;

}

?>