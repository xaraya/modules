<?php

function autodoc_init($args=array())
{
    // Register search api function as hook
    xarModRegisterHook('item','search','API','autodoc','user','search');

    // Enable double whammy with search module
    if(xarModIsAvailable('search')) {
        xarModAPIFunc('modules','admin','enablehooks',
                      array('callerModName' => 'autodoc', 'hookModName' => 'search'));
        xarModAPIFunc('modules','admin','enablehooks',
                     array('callerModName' => 'search', 'hookModName' => 'autodoc'));
    }
    return true;
}
?>