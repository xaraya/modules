<?php

/**
 * the main user function
  */
function xarlinkme_user_main()
{ 
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing.
    if (!xarSecurityCheck('ViewxarLinkMe')) return;

    return xarModFunc('xarlinkme','user','view');

} 

?>
