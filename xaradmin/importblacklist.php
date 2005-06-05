<?php
/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function comments_admin_importblacklist()
{
    if (!xarSecurityCheck('Comments-Admin')) return;
    if (!xarModAPIFunc('comments', 'admin', 'import_blacklist')) return;
    return array();
}
?>