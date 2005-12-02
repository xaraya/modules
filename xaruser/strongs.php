<?php
 /**
 * File: $Id: 
 * 
 * Display a Strong's Concordance definition
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
 * display a Strong's Concordance definition
 * 
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['query'] reference to look up
 */
function bible_user_strongs($args)
{ 
    extract($args);

    if (!xarVarFetch('sname', 'str:1:', $sname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tid', 'int:1:', $tid, null, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'int:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('query', 'str:0:', $query, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('string', 'str:1:', $string, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecurityCheck('ViewBible')) return; 

    if (!empty($objectid)) {
        $tid = $objectid;
    }

    // validate variables
    $invalid = array();
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

    // generate args for lookup and get functions
    $args = array();
    if (isset($tid)) $args['tid'] = $tid;
    if (isset($sname)) $args['sname'] = $sname;
    $args['query'] = $query;

    // get text data
    $text = xarModAPIFunc('bible', 'user', 'get', $args);

    // make sure we have short name and text ID
    if (empty($sname)) $sname = $text['sname'];
    if (empty($tid)) $tid = $text['tid'];

    // get list of texts for queryform
    $texts = xarModAPIFunc('bible', 'user', 'getall',
                           array('state' => 2, 'type' => 2, 'order' => 'sname'));

    // prepare template variables array
    $data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'concordance')); 
    $data['status'] = '';
    $data['texts'] = $texts;
    $data['query'] = $query;
    $data['sname'] = $sname;
    $data['text'] = $text;
    $data['string'] = $string;

    // perform lookup function
    $results = xarModAPIFunc('bible', 'user', 'strongslookup', $args);

    // replace references to other Strong's words with links
    if (isset($results['entry'])) {
        $def = $results['entry']['def'];
        preg_match_all("/(see\s+(hebrew|greek)\s+for\s+)(\d+)/i", $def, $matches, PREG_OFFSET_CAPTURE);

        // reverse order of matches to make replacing easier
        foreach ($matches as $index => $set) $matches[$index] = array_reverse($set);

        foreach ($matches[0] as $index => $match) {
            $args = array();
            // figure out if we need hebrew or greek
            if (preg_match("/^greek\$/i", $matches[2][$index][0])) {
                $this_sname = 'StrongsGreek';
            } elseif (preg_match("/^hebrew\$/i", $matches[2][$index][0])) {
                $this_sname = 'StrongsHebrew';
            } else {
                $this_sname = $sname;
            }
            // make sure we don't link to a word that doesn't exist
            if (($this_sname == 'StrongsGreek' && $matches[3][$index][0] > 5624) ||
                ($this_sname == 'StrongsHebrew' && $matches[3][$index][0] > 8674)) {
                continue;
            }
            $args['sname'] = $this_sname;
            $args['query'] = preg_replace("/^0*/", '', $matches[3][$index][0]);
            $url = xarModURL('bible', 'user', 'strongs', $args);

            $seefor = ucfirst($matches[1][$index][0]);
            $seefor = str_replace('HEBREW', 'Hebrew', $seefor);
            $seefor = str_replace('GREEK', 'Greek', $seefor);
            $replace = "$seefor <a href=\"$url\">".$matches[3][$index][0]."</a>.";
            $def = substr_replace($def, $replace, $match[1], strlen($match[0]));
        }

        // add line break before the first "see LANG for DDDDD"
        if (!empty($match[1])) {
            $def = substr_replace($def, '<br />', $match[1], 0);
        }
        $results['entry']['def'] = $def;
    }

    // notify if we found no results
    if (empty($results)) {
        $data['status'] = xarML("No matches were found in <b>$sname</b> for <b>$query</b>.");
        return $data;
    }

    $data['results'] = $results;

    if (!empty($sname) && !empty($query)) {
        $sep = xarModGetVar('themes', 'SiteTitleSeparator');
        $pagetitle = "$sname$sep$results[ref]";
    } elseif (isset($results['type']) && $results['type'] == 'toc') {
        $sep = xarModGetVar('themes', 'SiteTitleSeparator');
        $pagetitle = $sname.$sep.xarML('Table of Contents');
    } else {
        $pagetitle = xarML('Strong\'s Concordance');
    }
    xarTplSetPageTitle($pagetitle);

    // Return the template variables defined in this function
    return $data; 
} 

?>
