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
    xarVarFetch('searchterms','str:1:',$search_terms);
    
    // Some modules allow searching only specific itemtypes, the generic
    // searchform supports this.
    xarVarFetch('itemtypes','array:1:',$item_types,array(), XARVAR_NOT_REQUIRED);
    
    // The actual search itself, needs to be handled by the module itself, because
    // that is the only one who has knowledge how to do that.
    // The actual search *SHOULD* be implemented as item:search:API function so
    // it's convenient to use the function in several ways. Modules could decide 
    // to implement their function as a regular API function as well
    
    // Test if the module is hooked at all
    if(xarModIsHooked($search_in_module)) {
        // At least it's hooked
    } else {
        // If module is not hooked how are we going to get results, not possible
        // set a user exception for this
        $msg = xarML('The module #(1) does not provide an API search function, or is not hooked into the search module', $search_in_module);
        xarExceptionSet(XAR_USER_EXCEPTION, 'NOT_HOOKED',$msg);
        return;
    }
}

?>