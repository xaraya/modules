<?php
/**
 * File: $Id:
 *
 * Main Strong's Concordance function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf
 */
function bible_user_dictionary($args)
{
    // security check
    if (!xarSecurityCheck('ViewBible')) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('sname', 'str:1:', $sname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tid', 'int:1:', $tid, null, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'int:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('query', 'str:1:', $query, '', XARVAR_NOT_REQUIRED)) return;

    // set defaults
    if (!empty($objectid))  $tid = $objectid;

    // validate variables
    $invalid = array();
    if (!empty($sname) && is_numeric($sname)) {
        $invalid[] = 'sname';
    }
    if (!empty($tid) && !is_numeric($tid)) {
        $invalid[] = 'tid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(',', $invalid), 'user', 'library', 'bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // get active dictionaries for the dropdown list
    $texts = xarModAPIFunc('bible', 'user', 'getall',
        array('state' => 2, 'type' => 2, 'order' => 'sname')
    );

    // if no texts, we have to throw some kind of error
    if (empty($texts)) {
        // API function failed, so return false
        if (xarCurrentErrorType() != XAR_NO_EXCEPTION) {
            return;
        // No API error, so we must not have any texts available.  Send system message.
        } else {
            $msg = xarML('No dictionaries are currently available!  '
                . 'Sorry, I am unable to proceed.');
            xarErrorSet(XAR_SYSTEM_MESSAGE, '', new SystemException($msg));
            return;
        }
    }

    // get default text for dropdown list and save the user's current selection
    if (!empty($sname)) {
        $default_sname = $sname;
    } elseif (false === ($default_sname = xarSessionGetVar('bible_dictionaryname'))) {
        $default_sname = $texts[key($texts)]['sname'];
    }
    xarSessionSetVar('bible_dictionaryname', $default_sname);

    /**
    * Three possible actions here
    *   1. view a word - if given query and text identifier
    *   2. browse word list - if given text identifier but no query
    *   3. search form only - if given nothing at all!
    */

    // view a word
    if (!empty($query) && (!empty($tid) || !empty($sname))) {
        $displaytype = 'view';

    // browse word list
    } else if (empty($query) && (!empty($tid) || !empty($sname))) {
        $displaytype = 'browse';

    // show search form only
    } else {
        $displaytype = 'form';
    }

    // act depending on display type
    switch($displaytype) {

    // view a word
    case 'view':

        // get additional HTTP vars
        if (!xarVarFetch('string', 'str:1:', $string, '', XARVAR_NOT_REQUIRED)) return;

        // get text data
        $args = array();
        if (!empty($tid)) $args['tid'] = $tid;
        if (!empty($sname)) $args['sname'] = $sname;
        $text = xarModAPIFunc('bible', 'user', 'get', $args);

        // if no text, we have to throw some kind of error
        if (empty($text)) {
            // API function failed, so return false
            if (xarCurrentErrorType() != XAR_NO_EXCEPTION) {
                return;
            // No API error, so text must exist but is not available(?). Send system message.
            } else {
                $msg = xarML('This dictionary is not currently available!  '
                    . 'Sorry, I am unable to proceed.');
                xarErrorSet(XAR_SYSTEM_MESSAGE, '', new SystemException($msg));
                return;
            }
        }

        // perform lookup function
        $results = xarModAPIFunc('bible', 'user', 'lookupdictionary',
            array('query' => $query, 'sname' => $sname)
        );

        // there was an error
        if (empty($results) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {
            return;

        // we got a result; do post-processing of result
        } elseif (!empty($results)) {

            // replace references to other Strong's words with links
            $def = $results['def'];
            preg_match_all("/(see\s+(hebrew|greek)\s+for\s+)(\d+)/i", $def, $matches, PREG_OFFSET_CAPTURE);

            // reverse order of matches to make replacing easier
            foreach ($matches as $index => $set) {
                $matches[$index] = array_reverse($set);
            }

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
                $url = xarModURL('bible', 'user', 'dictionary', $args);

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
            $results['def'] = $def;
        }

        // set page title
        xarTplSetPageTitle(xarVarPrepForDisplay($query));

        // initialize template data
        $data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'dictionary'));

        // set template vars
        $data['query'] = $query;
        $data['string'] = $string;
        $data['sname'] = $sname;
        $data['text'] = &$text;
        $data['results'] = &$results;

        break;

    // browse word list
    case 'browse':

        // get additional HTTP vars
        if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('numitems', 'int:1:', $numitems, 0, XARVAR_NOT_REQUIRED)) return;

        // get text data
        $args = array();
        if (!empty($tid)) $args['tid'] = $tid;
        if (!empty($sname)) $args['sname'] = $sname;
        $text = xarModAPIFunc('bible', 'user', 'get', $args);

        // if no text, we have to throw some kind of error
        if (empty($text)) {
            // API function failed, so return false
            if (xarCurrentErrorType() != XAR_NO_EXCEPTION) {
                return;
            // No API error, so text must exist but is not available(?). Send system message.
            } else {
                $msg = xarML('This dictionary is not currently available!  '
                    . 'Sorry, I am unable to proceed.');
                xarErrorSet(XAR_SYSTEM_MESSAGE, '', new SystemException($msg));
                return;
            }
        }

        // now many items should we show?
        if (empty($numitems)) {
            $numitems = xarModGetVar('bible', 'user_wordsperpage');
        }

        // get words
        $results = xarModAPIFunc('bible', 'user', 'lookupdictionary',
            array('startnum' => $startnum, 'numitems' => $numitems, 'sname' => $sname)
        );
        if (empty($results) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

        // add links
        foreach ($results as $index => $result) {
            $results[$index]['url'] = xarModURL('bible', 'user', 'dictionary',
                array('sname' => $sname, 'query' => $result['word'])
            );
        }

        // set page title
        xarTplSetPageTitle(xarVarPrepForDisplay($sname));

        // initialize template data
        $data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'dictionary'));

        // set template vars
        $data['sname'] = $sname;
        $data['query'] = '';
        $data['results'] = &$results;

        break;

    // show search form only
    case 'form':

        // set page title
        xarTplSetPageTitle(xarML('Dictionary'));

        // initialize template data
        $data = xarModAPIFunc('bible', 'user', 'menu', array('func' => 'dictionary'));

        // set template vars
        $data['query'] = '';

    }

    // set template vars
    $data['texts'] = $texts;
    $data['default_sname'] = $default_sname;
    $data['displaytype'] = $displaytype;

    return $data;
}

?>
