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
    //  $args   - modified in some way (an array)
    //  true    - processing successful, but $args not modified
    //  false   - processing unsuccessful or processing must stop; results
    //            in an immediate exit from the page processing

    // Example:
    // If the GET parameter 'cid' has been set, and is numeric
    // then pass it into the page as parameter 'cid'.

    xarVarFetch('cid', 'id', $cid, NULL, XARVAR_NOT_REQUIRED);

    if (!empty($cid)) {
        $args['cid'] = $cid;
    }

    return $args;
}

?>