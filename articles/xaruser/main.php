<?php

/**
 * the main user function
 */
function articles_user_main($args)
{
    return xarModFunc('articles','user','view',$args);
// TODO: make this configurable someday ?
    // redirect to default view (with news articles)
    //xarResponseRedirect(xarModURL('articles', 'user', 'view'));
    //return;
}

?>
