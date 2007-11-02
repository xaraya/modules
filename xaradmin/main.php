<?php

function mag_admin_main($args)
{
    // TODO: be a little more inteligent regarding this entry point - 
    // provide various redirections depending on some simple parameters.

    return xarModfunc('mag', 'admin', 'view');
}

?>