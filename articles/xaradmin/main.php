<?php

/**
 * the main administration function
 */
function articles_admin_main()
{

// Security Check
    if (!xarSecurityCheck('EditArticles')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0){
        $welcome = '';
        // TODO: make language-dependent and/or cfr. RFC 19
        // <Dracos>  moved the contents of this file into the template, wrapped in mlstring tags
        // @include('modules/articles/xarlang/eng/admindoc.php');

        // Return the template variables defined in this function
        return array('welcome' => $welcome);
    } else {
        xarResponseRedirect(xarModURL('articles', 'admin', 'view'));
    }
    // success
    return true;

}

?>
