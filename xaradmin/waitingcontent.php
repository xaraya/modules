<?php
/**
 * display waiting content as a hook
 */
function articles_admin_waitingcontent()
{
    
    // Get publication types
    unset($publinks);
    $publinks = xarModAPIFunc('articles', 'user', 'getpublinks',
                          array('status' => array(0),
                                'typemod' => 'admin'));

     $data['loop'] = $publinks;
     return $data;
}
?>