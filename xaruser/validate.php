<?php
/**
 * File: $Id:
 * 
 * Evaluate a query and direct to appropriate function
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */
function bible_user_validate()
{
    // grab passed variables from queryform
    if (!xarVarFetch('query', 'str:0:', $query, $query, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sname', 'str:1:', $sname, $sname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lastlimits', 'str', $lastlimits, '', XARVAR_NOT_REQUIRED)) return;

    // additional variables from keyword search form
    if (!xarVarFetch('function', 'str:1', $function, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('searchtype', 'str:1', $searchtype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('words', 'array', $words, array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('bools', 'array', $bools, array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('limits', 'array', $limits, array(), XARVAR_NOT_REQUIRED)) return;

    // additional variables from passage lookup form
    if (!xarVarFetch('refparts', 'array', $refparts, array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('parallel', 'array', $parallel, array(), XARVAR_NOT_REQUIRED)) return;

    // security check
    if (!xarSecurityCheck('ViewBible')) return;

    if (empty($function)) {
        // throw error
        return;
    } elseif ($function == 'view') {

        // initialize query placeholder
        $query = '';

        // enter "searchtype: " if necessary
        if (!empty($searchtype) && $searchtype != 'normal') $query = $searchtype.':';

        // enter any words
        foreach ($words as $index => $word) {
            if (!empty($word)) {
                $query .= ' ';
                if (!empty($bools[$index])) $query .= $bools[$index];
                if (preg_match("/ /", $word)) {
                    $query .= '"'.str_replace('"', '', $word).'"';
                } else {
                    $query .= $word;
                }
            }
        }
        $query = trim($query);

        // enter limits
        if (!empty($limits)) $query .= ' limits='.join(',', $limits);

    } elseif ($function == 'display') {

        $query = '';

        if (empty($refparts)) {
            // throw error
            return;
        } elseif (!empty($refparts[0])) {
            // start with the book
            $query .= $refparts[0];
            if (!empty($refparts[1])) {
                // add the chapter
                $query .= ' ' . $refparts[1];
                if (!empty($refparts[2])) {
                    // add the verse
                    $query .= ':' . $refparts[2];
                    if (!empty($refparts[3])) {
                        // add an ending verse
                        $query .= '-'.$refparts[3];
                    }
                }
            }
        }

        // handle any parallel selections
        if (!empty($parallel)) {
            $key = array_search($sname, $parallel);
            if (is_numeric($key)) unset($parallel[$key]);
            $query .= ' parallel=' . join(',', $parallel);
            
        }

    }

    // redirect to the appropriate function
    $args = array();
    if (!empty($sname)) $args['sname'] = $sname;
    if (!empty($query)) $args['query'] = $query;

    // generate redirect url
    $url = xarModURL('bible', 'user', $function, $args);
    xarResponseRedirect($url);

    return true;


} 

?>
