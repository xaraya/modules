<?php
/**
 * display waiting content as a hook
 */
function release_admin_waitingcontent()
{
    
    // Get releasenotes
    unset($released);
    $released = xarModAPIFunc('release', 'user', 'getreleaselinks',
                          array('approved' => 1));

     $data['loop'] = $released;
     $data['counted']=$released['counted'];

     return $data;
}
?>
