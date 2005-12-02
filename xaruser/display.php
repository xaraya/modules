<?php
 /**
 * File: $Id: 
 * 
 * Display a portion of text
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
 * display a passage
 * This is a standard function to provide detailed informtion on a single item
 * available from the module.
 * 
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['sname'] short name of text to look in
 * @param  $args ['tid'] text ID to look in
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['query'] reference to look up
 */
function bible_user_display($args)
{ 
    extract($args);

    if (!xarVarFetch('startnum', 'int:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int:1:', $numitems, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showcontext', 'int:0', $showcontext, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sname', 'str:1:', $sname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tid', 'int:1:', $tid, null, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'int:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('query', 'str:0:', $query, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('ViewBible')) return; 

    // optional parameters
    if (empty($numitems)) {
        $numitems = xarModGetUserVar('bible', 'user_lookupversesperpage');
    }

    if (!empty($objectid)) {
        $tid = $objectid;
    }

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
            join(',', $invalid), 'user', 'display', 'Bible');
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

    // prepare template variables array
    $data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'lookup')); 
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

    // save user's last sname as default for current session
    xarSessionSetVar('bible_sname', $sname);

    // pass text and sname to template
    $data['sname'] = $sname;

    // update page title
    if (!empty($sname) && !empty($query)) {
        $sep = xarModGetVar('themes', 'SiteTitleSeparator');
        $pagetitle = "$sname$sep$query_orig";
    } else {
        $pagetitle = 'Passage Lookup';
    }
    xarTplSetPageTitle($pagetitle);

    // detect parallel lookup
    preg_match("/\s+para(llel)?=([^ \$]+)/", $query, $match);
    if (!empty($match[0])) {
        $snames = explode(',', $match[2]);
        $query = trim(str_replace($match[0], '', $query));
        $parallel = true;
        array_unshift($snames, $sname);
    } else {
        $snames = array($sname);
        $parallel = false;
    }

    $data['parallel'] = $parallel;

    // get results for each sname requested
    foreach ($snames as $index => $sname) {

        // perform lookup function
        $args = array();
        $args['sname'] = $sname;
        $args['query'] = $query;
        $args['numitems'] = $numitems;
        $args['startnum'] = $startnum;
        $args['showcontext'] = $showcontext;
        $args['parallel'] = $parallel;
        $args['snames'] = $snames;

        $results = xarModAPIFunc('bible', 'user', 'lookup', $args);

        // notify if we found no results
        if (empty($results['lines'])) {
            $results['status'] = xarML("No matches were found in <b>$sname</b> for <b>$query</b>.");
            $data['results'][] = $results;
            continue;
        }

        $results = xarModAPIFunc('bible', 'user', 'formattext', array('data' => $results, 'lookup' => true));
 
        $data['results'][] = $results;

        // first round is the main request, so base our pager on it
        if ($index == 0) {
            $results['snames'] = $snames;
            $data['prevnext'] = xarModFunc('bible', 'user', 'prevnext', $results);
            $data['pager'] = xarTplGetPager($startnum,
                                            $results['hitcount'],
                xarModURL('bible', 'user', 'display', array('sname' => $sname,
                                                            'query' => $query_orig,
                                                            'startnum' => '%%')),
                $numitems);
        }

    }

    // Return the template variables defined in this function
    return $data; 
} 

?>
