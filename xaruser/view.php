<?php
/**
 * File: $Id:
 *
 * View a keyword search
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf
 */
/**
 * view a keyword search
 */
function bible_user_view($args)
{
    extract($args);

    if (!xarVarFetch('startnum', 'int:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int:1:', $numitems, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tid', 'int:1:', $tid, null, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'int:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sname', 'str:1:', $sname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lastlimits', 'str', $lastlimits, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('query', 'str:0:', $query, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('ViewBible')) return;

    // optional parameters
    if (empty($numitems)) {
        $numitems = xarModGetUserVar('bible', 'user_searchversesperpage');
    }

    // get defaults
    if (!empty($objectid)) $tid = $objectid;

    // validate variables
    $invalid = array();
    if (!is_numeric($numitems) || $numitems < 1) {
        $invalid[] = 'numitems';
    }
    if (empty($sname) && empty($tid)) {
        $invalid[] = 'text identifier';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(',', $invalid), 'user', 'view', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // if no query, redirect to library page for this text
    if (empty($query)) {
        if (!empty($sname)) {
            xarResponseRedirect(xarModURL('bible', 'user', 'library', array('sname' => $sname)));
        } elseif (!empty($tid)) {
            xarResponseRedirect(xarModURL('bible', 'user', 'library', array('tid' => $tid)));
        }
        return;
    }

    // store the original query
    $query_orig = $query;

    // get list of texts
    $texts = xarModAPIFunc('bible', 'user', 'getall',
                           array('state' => 2, 'type' => 1, 'order' => 'sname'));

    // prepare template variables
    $data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'search'));
    $data['status'] = '';
    $data['texts'] = $texts;
    $data['pager'] = '';
    $data['query'] = $query;

    // work out the sname (could have been passed the tid or the sname)
    if (empty($sname)) {
        $text = xarModAPIFunc('bible', 'user', 'get', array('tid' => $tid));
        $sname = $text['sname'];
    } else {
        $text = xarModAPIFunc('bible', 'user', 'get', array('sname' => $sname));
    }
    $data['text'] = $text;
    $data['sname'] = $sname;

    // save user's last sname as default for current session
    xarSessionSetVar('bible_sname', $sname);

    /**
     * detect search limits
     *
     * ... when things like "limit=ot,gos" are in the query
     */
    preg_match("/\blim(it)?s?=([^ ]*)/", $query, $match);
    $query_orig = $query;

    $limits = array();
    if (!empty($match)) {
        // get array of groups and make query print nicely
        $query = trim(str_replace($match[0], '', $query));
        $limits = explode(',', $match[2]);

        // get group aliases and make sure user's groups match something in the array
        list($aliases,
             $placeholder) = xarModAPIFunc('bible', 'user', 'getaliases',
                                           array('type' => 'groups'));
        foreach ($limits as $index => $limit) {
            if (preg_match("/^(last|prev(ious)?)\$/i", $limit)) {
                $limits[$index] = explode(',', $lastlimits);
            } elseif (isset($aliases[$limit])) {
                $limits[$index] = $aliases[$limit];
            } elseif (!empty($limit)) {
                list($swordbook,
                     $placeholder) = xarModAPIFunc('bible', 'user', 'query2book',
                                                   array('query' => $limit));
                if (!empty($swordbook)) {
                    $limits[$index] = array($swordbook);
                } else {
                    $data['status'] = xarML("Unrecognized group or book <b>$limit</b>");
                    return $data;
                }
            }
        }
    }

    // perform search function
    $results = xarModAPIFunc('bible', 'user', 'search',
                            array('tid' => $tid,
                                'sname' => $sname,
                                'query' => $query,
                                'numitems' => $numitems,
                                'startnum' => $startnum,
                                'limits' => $limits,
                                'text' => $text));

    // check if query was not found
    if (empty($results) || empty($results['lines'])) {
        $data['status'] = xarML("<p>No matches were found for \"$query\".</p><p>Please note: Words that are common or shorter than 4 characters long are ignored.");
        return $data;
    }

    // format the results if necessary
    $results = xarModAPIFunc('bible', 'user', 'formattext',
                                array('data' => $results,
                                      'search' => true,
                                      'strongs' => false));

    $data['results'] = $results;

    // get pager
    $data['pager'] = xarTplGetPager($startnum,
                                    $results['hitcount'],
        xarModURL('bible', 'user', 'view', array('sname' => $sname,
                                                 'query' => $query_orig,
                                                 'startnum' => '%%')),
        $numitems);

    // update page title
    if (!empty($sname) && !empty($query)) {
        $sep = xarModGetVar('themes', 'SiteTitleSeparator');
        xarTplSetPageTitle(xarVarPrepForDisplay("$sname$sep$query_orig"));
    } else {
        xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Passage Lookup')));
    }

    // Return the template variables defined in this function
    return $data;
}

?>
