<?php

function xarpages_funcapi_example($args)
{
    // Do some custom processing for the page.
    // The array passed in will include the following:
    //  'ancestors' } arrays of pages related to the
    //  'children'  } current page in the hierarchy
    //  'siblings'  }
    //  'current_page'  - the current page
    //  'inheritted'    - the current page overlayed onto all ancestors
    //  'pages'         - all pages relevant to this page
    //  'pid'           - ID of the current page
    //
    // Return values can be any of the following:
    //  array   - $args modified in some way
    //  true    - processing successful, but $args not modified
    //  false   - processing unsuccessful or processing must stop; results
    //            in an immediate return from the page processing
    //  NULL    - treated by the calling function as 'custom function not found';
    //            has the same effect as 'true' for the present time
    //  other   - other data types and values are treated as 'true' for now; that
    //            may be extended if there are other actions that the calling
    //            function may need to perform, e.g. to redirect to another page.

    // Example:
    // If the GET parameter 'cid' has been set, and is numeric
    // then pass it into the page as parameter 'cid'.

    xarVarFetch('cid', 'id', $cid, NULL, XARVAR_NOT_REQUIRED);

    if (!empty($cid)) {
        // I would recomment putting all the custom values under the 'custom'
        // element of $args. That shoud avoid any namespace clashes with values
        // supplied by the xarpages module itself (e.g. it may support categories
        // natively in the future, and so 'cid' or 'cids' is likely to be needed).
        $args['custom']['cid'] = $cid;
    }

    return $args;
}

?>