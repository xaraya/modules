<?php

function xarbb_user_viewforumrss()
{
     
    $fid = xarVarCleanFromInput('fid');

    $data['items'] = array();

    // Security Check
    if(!xarSecurityCheck('ReadxarBB')) return;

    // The user API function is called
    $topics = xarModAPIFunc('xarbb',
                            'user',
                            'getalltopics',
                            array('fid' => $fid));

    // Add the array of items to the template variables
    $data['fid'] = $fid;
    $data['items'] = $topics;
  
    // Return the template variables defined in this function
    return $data;
}

?>