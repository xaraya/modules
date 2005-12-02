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

    // get other vars
    $itemsperpage = xarModGetVar('ebulletin', 'admin_issuesperpage');

    // get pager
    $pager = xarTplGetPager(
        $startnum,
        xarModAPIFunc('ebulletin', 'user', 'countissues'),
        xarServerGetCurrentURL(array('startnum' => '%%')),
        $itemsperpage
    );
    if (empty($pager) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // get issues
    $issues = xarModAPIFunc('ebulletin', 'user', 'getallissues',
         array('startnum' => $startnum, 'numitems' => $itemsperpage)
    );
    if (empty($issues) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // add options for each issue
    foreach ($issues as $index => $issue) {

        // format vars
        $issue['published_string'] = ($issue['published']) ? xarML('Yes') : xarML('No');
        $issue['issuedate'] = xarLocaleGetFormattedDate('short', strtotime($issue['issuedate']));

        // view
        if (xarSecurityCheck('VieweBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {
            $issue['viewurl'] = xarModURL('ebulletin', 'admin', 'display',
                array('id' => $issue['id'])
            );
        }

        // edit and regenerate
        if (xarSecurityCheck('EditeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {

            $issue['editurl'] = xarModURL('ebulletin', 'admin', 'modifyissue',
                array('id' => $issue['id'])
            );
            $issue['regenerateurl'] = xarModURL('ebulletin', 'admin', 'regenerateissue', array(
                'id' => $issue['id'],
                'authid' => xarSecGenAuthKey(),
                'return' => xarModURL('ebulletin', 'admin', 'viewissues'))
            );
        }

        // delete
        if (xarSecurityCheck('DeleteeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {
            $issue['deleteurl'] = xarModURL('ebulletin', 'admin', 'deleteissue', array(
                'id' => $issue['id'])
            );
        }

        // publish
        if (xarSecurityCheck('AddeBulletin', 0, 'Publication', "$issue[pubname]:$issue[pid]")) {
            $issue['publishurl'] = xarModURL('ebulletin', 'admin', 'publishissue', array(
                'id' => $issue['id'])
            );
        }
        $issues[$index] = $issue;

    }

    // get publications
    $pubs = xarModAPIFunc('ebulletin', 'user', 'getall');
    if (empty($pubs) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // initialize template data
    $data = xarModAPIFunc('ebulletin', 'admin', 'menu');

    // set template vars
    $data['issues'] = $issues;
    $data['pubs'] = $pubs;
    $data['pager'] = $pager;

    return $data;

}

?>