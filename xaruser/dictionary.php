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

    // get default text for dropdown display
    $sname = xarSessionGetVar('bible_dictionaryname');
    if (empty($sname)) {
        // none is set for this session, so use the first one in the texts list
        $sname = $texts[key($texts)]['sname'];
        xarSessionSetVar('bible_dictionaryname', $sname);
    }

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

            foreach ($matches[0] as $index => $match) {
                // get vars from this match
                $line = $matches[0][$index][0];
                $lang = $matches[2][$index][0];
                $num = $matches[3][$index][0];

                // apply duct tape and bailing wire
                $lang = ucfirst(strtolower($lang));
                $this_sname = "Strongs$lang";
                $num = preg_replace("/^0+/", '', $num);

                $url = xarModURL('bible', 'user', 'dictionary', array(
                    'sname' => $this_sname, 'query' => $num
                ));

                // remove entries from original
                $def = str_replace($line, '', $def);
                $def = trim($def);

                // append an array of data for "see LANG for DDDDD" links
                $results['see'][$lang][$num] = array(
                    'sname' => $this_sname,
                    'url'   => $url,
                    'num'   => $num
                );
            } // foreach "See LANG for DDDDDD" match

            // sort and re-index "See LANG for DDDDDD" matches
            if (!empty($results['see'])) {
                foreach ($results['see'] as $lang => $row) {
                    ksort($row);
                    $row = array_slice($row, 0);
                    $results['see'][$lang] = $row;
                }
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

        // get pager
        $pager = xarTplGetPager(
            $startnum,
            xarModAPIFunc('bible', 'user', 'countwords', array('sname' => $sname)),
            xarModURL('bible', 'user', 'dictionary', array('sname' => $sname, 'startnum' => '%%', 'numitems' => $numitems)),
            $numitems
        );

        // get words
        $results = xarModAPIFunc('bible', 'user', 'lookupdictionary',
            array('startnum' => $startnum, 'numitems' => $numitems, 'sname' => $sname)
        );
        if (empty($results) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

        // how many rows to display per column in our table?
        $cols = 2; // make this dynamic?
        $rowpercol = ceil(count($results)/$cols);

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
        $data['rowpercol'] = $rowpercol;
        $data['pager'] = &$pager;

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
    $data['default_sname'] = $sname;
    $data['displaytype'] = $displaytype;

    return $data;
}

?>
