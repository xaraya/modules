<?php

function ievents_user_main($args)
{
    // TODO: be a little more inteligent regarding this entry point - 
    // provide various redirections depending on some simple parameters.
    return xarModfunc('ievents', 'user', 'viewcals', array($args));
}

?>
