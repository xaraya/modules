<?php
/**
 * Handle the actual search
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
 * Handle a generic search request
 *
 * This function is the standard target of the item:search:gui hook
 * of the search module. It is passed the result of the generic search
 * form.
 *
 * @author  Marcel van der Boom <marcel@xaraya.com>
 * @access  public
 * @return array
*/
function search_user_handlesearch()
{
    // The module we want to search and the search terms are required.
    xarVarFetch('formodule','str:1:',$search_in_module);

    // What did we search for, let the session remember it.
    xarVarFetch('searchterms','str:0:',$search_terms);
    xarSessionSetVar('searchterms', $search_terms);

    // If startnum was passed in get it, if not set it to 1
    xarVarFetch('startnum','int::',$startnum,1,XARVAR_NOT_REQUIRED);

    // Some modules allow searching only specific itemtypes, the generic
    // searchform supports this.
    xarVarFetch('itemtypes','array:1:',$item_types,array(), XARVAR_NOT_REQUIRED);
    xarSessionSetVar('checked_itemtypes',$item_types);

    xarVarFetch('object_id','id',$object_id,0,XARVAR_NOT_REQUIRED);

    // This may seem strange, but it it intentional.
    function get_search_hook($list)
    {
        // Bit of a trick to get the module name again, apparently php
        // scoping doesn't give me access to $search_in_module
        xarVarFetch('formodule','str:1:',$search_in_module);
        return ($list['module'] == $search_in_module) && ($list['area'] == "API");
    }

    function highlight_match(&$match,$key, $term)
    {
        // FIXME: This doesn't belong in code, it's a template function
        // <xar:transform> or something like that. Transform hook seems a bit over the top for this
        $match['context'] = str_replace($term, "<span class=\"xar-search-match\">$term</span>",$match['context']);
    }

    // The actual search itself, needs to be handled by the module itself, because
    // that is the only one who has knowledge how to do that.
    // The actual search *SHOULD* be implemented as item:search:API function so
    // it's convenient to use the function in several ways. Modules could decide
    // to implement their function as a regular API function as well

    $data = array();
    // Test if the module is hooked at generic level
    if(xarModIsHooked($search_in_module)) {
        // Now call the item:search:api function of the calling module and present
        // the search results with the template belonging to this function.

        // Which modules are hooked in on search:api?
        $hooklist = xarModGetHooklist('search','item','search');
        // reduce it to the module we're interested in
        $the_hook = array_filter($hooklist, 'get_search_hook');
        $the_hook = $the_hook[0];
        $searchresults = xarModAPIFunc($search_in_module,
                                       $the_hook['type'],
                                       $the_hook['func'],
                                       array('terms' => $search_terms,
                                             'itemtypes' => $item_types,
                                             'object_id' => $object_id));
        // The search results array contains a 'context' element, highlight the stuff in there
        // which we searched for
        array_walk($searchresults,'highlight_match',$search_terms);
        //var_dump($searchresults);
    } else {
        // If module is not hooked how are we going to get results, not possible
        // set a user exception for this
        $msg = xarML('The module #(1) does not provide an API search function, or is not hooked into the search module', $search_in_module);
        xarErrorSet(XAR_USER_EXCEPTION, 'NOT_HOOKED',$msg);
        return;
    }

    // Display the search form again, can we count on the array having one element?
    $searchform = xarModCallHooks('item','search',$object_id,array('module' => $search_in_module));

    $total = count($searchresults);
    $itemsperpage = xarModGetUserVar('search','resultsperpage');
    $searchresults = array_slice($searchresults,$startnum-1, $itemsperpage);
    if($total < $itemsperpage) $itemsperpage = $total;

    $urltemplate = xarModUrl('search','user','handlesearch',array('startnum' => '%%',
                                                                  'formodule' => $search_in_module,
                                                                  'searchterms' => $search_terms,
                                                                  'object_id' => $object_id,
                                                                  'itemtypes' => $item_types));
    $data['pager'] =  xarTplGetPager($startnum, $total, $urltemplate, $itemsperpage);

    // Pass data to template
    $data['searchform'] = $searchform;
    $data['searchmodule'] = $search_in_module;
    $data['searchterms'] = $search_terms;
    $data['searchresults'] = $searchresults;
    $data['searchtotal'] = $total;
    $data['searchstart'] = ($total !=0)?$startnum:$startnum-1;
    $data['searchend'] = min($total,($startnum + $itemsperpage -1));
    return $data;
}

?>
