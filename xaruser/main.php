<?php

function ievents_user_main($args)
{
    // TODO: be a little more inteligent regarding this entry point - 
    // provide various redirections depending on some simple parameters.
    // Start with the listing view, so we can go straight in with URLs
    // such as /ievents?range=next12months or /ievents?cid=2

    return xarModfunc('ievents', 'user', 'view', array($args));
}

?>
