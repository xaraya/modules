<?php

/**
 * File: $Id$
 *
 * Handle the search action from a hooked search from a module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage search
 * @author Marcel van der Boom <marcel@xaraya.com>
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
*/
function search_user_handlesearch() {
    // The module we want to search and the search terms are required.
    xarVarFetch('formodule','str:1:',$search_in_module);
    xarVarFetch('searchterms','str:0:',$search_terms);
    
    // Some modules allow searching only specific itemtypes, the generic
    // searchform supports this.
    xarVarFetch('itemtypes','array:1:',$item_types,array(), XARVAR_NOT_REQUIRED);
    xarVarFetch('object_id','id',$object_id,0,XARVAR_NOT_REQUIRED);
    
    // This may seem strange, but it it intentional.
    function get_search_hook($list) 
    {
        // Bit of a trick to get the module name again, apparently php
        // scoping doesn't give me access to $search_in_module
        xarVarFetch('formodule','str:1:',$search_in_module);
        return ($list['module'] == $search_in_module) && ($list['area'] == "API");
    }

    function highlight_match(&$match,$key, $term) {    
        $match['context'] = str_replace($term, "<span class=\"xar-search-match\">$term</span>",$match['context']);
    }

    // The actual search itself, needs to be handled by the module itself, because
    // that is the only one who has knowledge how to do that.
    // The actual search *SHOULD* be implemented as item:search:API function so
    // it's convenient to use the function in several ways. Modules could decide 
    // to implement their function as a regular API function as well
    
    $data = array();
    // Test if the module is hooked at all
    if(xarModIsHooked($search_in_module)) {
        // At least it's hooked
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
        xarExceptionSet(XAR_USER_EXCEPTION, 'NOT_HOOKED',$msg);
        return;
    }

    // Display the search form again
    $searchform = xarModCallHooks('item','search',$object_id,array(),$search_in_module);
 
    // Pass data to template
    $data['searchform'] = $searchform['search'];
    $data['searchmodule'] = $search_in_module;
    $data['searchterms'] = $search_terms;
    $data['searchresults'] = $searchresults;
    return $data;
}

?>