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
function ebulletin_admin_viewissues()
{
    // security check
    if (!xarSecurityCheck('EditeBulletin')) return;

    // get HTTP vars
    if (!xarVarFetch('startnum', 'str:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'str:1:', $numitems, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('order', 'enum:pubname:subject:date:published', $order, 'date', XARVAR_NOT_REQUIRED)) return;
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

    // get pager
    $pager = xarTplGetPager(
        $startnum,
        xarModAPIFunc('ebulletin', 'user', 'countissues'),
        xarServerGetCurrentURL(array('startnum' => '%%')),
        $numitems
    );
    if (empty($pager) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // get issues
    $issues = xarModAPIFunc('ebulletin', 'user', 'getallissues',
        array(
            'startnum' => $startnum,
            'numitems' => $numitems,
            'order'    => $order,
            'sort'     => $sort
        )
    );
    if (empty($issues) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // add options for each issue
    foreach ($issues as $index => $issue) {

        // format vars
        $issue['published_string'] = ($issue['published']) ? xarML('Yes') : xarML('No');
        $issue['issuedate'] = xarLocaleGetFormattedDate('short', strtotime($issue['issuedate']));

        if (xarSecurityCheck('EditeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {

            // edit
            $issue['urls'][] = array(
                'url' => xarModURL('ebulletin', 'admin', 'modifyissue',
                    array('id' => $issue['id'])
                ),
                'title' => xarML('Edit this issue'),
                'label' => xarML('Edit'),
                'onclick' => $issue['published'] ? 'return confirm(\''.xarML('This issue has already been published.  Are you sure you want to edit it?').'\');' : ''
            );
        }

        if (xarSecurityCheck('DeleteeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {
            // delete
            $issue['urls'][] = array(
                'url' => xarModURL('ebulletin', 'admin', 'deleteissue', array(
                    'id' => $issue['id'])
                ),
                'title' => xarML('Delete this issue.'),
                'label' => xarML('Delete'),
                'onclick' => $issue['published'] ? 'return confirm(\''.xarML('This issue has already been published.  Are you sure you want to delete it?').'\');' : ''
            );
        }

        if (xarSecurityCheck('AddeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {

            // publish
            $issue['urls'][] = array(
                'url' => xarModURL('ebulletin', 'admin', 'publishissue', array(
                    'id' => $issue['id']
                )),
                'title' => xarML('Publish this issue to its regular distribution list.'),
                'label' => xarML('Publish'),
                'onclick' => $issue['published'] ? 'return confirm(\''.xarML('This issue has already been published.  Are you sure you want to publish it again?').'\');' : ''
            );

        }

        if (xarSecurityCheck('AddeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {

            // test
            $issue['urls'][] = array(
                'url' => xarModURL('ebulletin', 'admin', 'sendtest', array(
                    'id' => $issue['id']
                )),
                'title' => xarML('Send this issue to one recipient.'),
                'label' => xarML('Test'),
                'onclick' => $issue['published'] ? 'return confirm(\''.xarML('This issue has already been published.  Are you sure you want to do a send test?').'\');' : ''
            );

        }

        // add modified issue back into array
        $issues[$index] = $issue;

    }

    // get publications
    $pubs = xarModAPIFunc('ebulletin', 'user', 'getall', array('order' => 'id'));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // set template vars
    $data = array();
    $data['issues']     = $issues;
    $data['pubs']       = $pubs;
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