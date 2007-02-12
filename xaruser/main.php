<?php
/**
 * Search System - Present searches via hooks
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Search Module
 * @link http://xaraya.com/index.php/release/32.html
 * @author Search Module Development Team
 */
/**
 * The standard search hook.
 *
 * @param  $args ['q'] is the search question
 * @param  $args ['author'] is the search for an author of an article
 * @return array
 */
function search_user_main()
{
    if (!xarVarFetch('q', 'str:1:', $q, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('author', 'str:1:', $author, '', XARVAR_NOT_REQUIRED)) return;
    // Security Check
    if (!xarSecurityCheck('ReadSearch')) return;

    $separator = "LINESPLIT";

    // E_ALL Check for an empty array or insert latest search.
    $oldsearch = xarModGetVar('search', 'lastsearch');
    if (empty($oldsearch)){
        $insert['term'] = 'Last Search';
        $insert['date'] = date('Y-m-d G:i:s');
        $insert['uid']  = xarUserGetVar('name');
        //We are simply throwing something into the modvar so we don't get ugly errors.
        // This is really only run once.  TODO, throw this in the init and upgrade for
        // The search to remove this processing.
        $firstsearch = $insert['term'] . '|' . $insert['date'] . '|' . $insert['uid'];
        $firstsearch = serialize($firstsearch);
        xarModSetVar('search', 'lastsearch', $firstsearch);
    } elseif ((!empty($q)) || (!empty($author)))  {
        if (!empty($q)){
            $insert['term'] = $q;
        } else {
            $insert['term'] = $author;
        }
        $insert['date'] = date('Y-m-d G:i:s');
        $insert['uid']  = xarUserGetVar('name');
        $content = array();
        // A little more complicated than the first search.  We need to get what's out
        // there first so we can process it.
        $oldsearch = unserialize($oldsearch);
        $searchitems = array();
        // Similar to what we are doing to display, only we are just creating a single
        // entity of the old search terms.
        $searchlines = explode($separator, $oldsearch);
        foreach ($searchlines as $searchline) {
            $link = explode('|', $searchline);
            if (count($link) < 3) continue;
            $content[] = $link[0] . '|' . $link[1] . '|' . $link[2];
        }
        // Now we are just processing the new search terms.
        if ($insert['term'] != $separator) {
            $content[] = $insert['term'] . '|' . $insert['date'] . '|' . $insert['uid'];
        }
        // While we are in a readible array, we might as well pop it now.
        $searchnum = count($content);
        if ($searchnum >= 10) {
            $dropsearch = array_shift($content);
        }
        $newsearch = implode($separator, $content);
        $newsearch = serialize($newsearch);
        xarModSetVar('search', 'lastsearch', $newsearch);
    }

    // In order to have the list up to date, we need to call the var again.
    // Otherwise the search term is one off of the searches.
    $search = xarModGetVar('search', 'lastsearch');
    // Lets Prep It All For Display Now.
    $search = unserialize($search);
    $searchitems = array();

    if (!empty($search)) {
        $searchlines = explode($separator, $search);
        foreach ($searchlines as $searchline) {
            $link = explode('|', $searchline);
            if (count($link) < 3) continue;
            $term = xarVarPrepForDisplay($link[0]);
            $date = xarVarPrepForDisplay($link[1]);
            $name  = xarVarPrepForDisplay($link[2]);
            $searchurl = xarModUrl('search', 'user', 'main', array('q' => $term));
            $searchitems[] = array('term' => $term, 'date' => $date, 'sname' => $name, 'searchurl' => $searchurl);
        }
    }

    $data['searchitems'] = $searchitems;

    if (!empty($q)) {
        $data['query'] = xarVarPrepForDisplay($q);
    } else {
        $data['query'] = '';
    }
    if (!empty($author)) {
        $data['name'] = xarVarPrepForDisplay($author);
    } else {
        $data['name'] = '';
    }
    // Hooks
    $data['output'] = xarModCallHooks('item', 'search', '', array());

    if (empty($data['output'])) {
        $data['message'] = xarML('There are no search options configured.');
    } elseif (is_array($data['output'])) {
        $data['output'] = join('', $data['output']);
    }

    if (empty($data['message'])) {
        $data['message'] = '';
    }

    if (!empty($q)){
        xarTplSetPageTitle(xarVarPrepForDisplay($q));
    } else {
        xarTplSetPageTitle(xarVarPrepForDisplay($author));
    }

    return $data;
}

?>
