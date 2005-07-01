<?php
include_once('modules/comments/xarincludes/defines.php');
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function comments_admin_modifyconfig()
{
     $editstamp=xarModGetVar('comments','editstamp');
    $output['editstamp']       = !isset($editstamp) ? 1 :$editstamp;

    // Security Check
    if(!xarSecurityCheck('Comments-Admin'))
        return;
    $numstats       = xarModGetVar('comments','numstats');
    $rssnumitems    = xarModGetVar('comments','rssnumitems');
    if (empty($rssnumitems)) {
        xarModSetVar('comments', 'rssnumitems', 25);
    }
    if (empty($numstats)) {
        xarModSetVar('comments', 'numstats', 100);
    }
    $output['authid'] = xarSecGenAuthKey();
    $output['hooks'] = xarModCallHooks('module', 'modifyconfig', 'comments',
                                       array('module' => 'comments'));
    return $output;
}
?>
