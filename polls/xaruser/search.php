<?php

function polls_user_search()
{
   list($startnum,
         $polls_title,
         $polls_options,
         $q,
         $bool,
         $sort) = xarVarCleanFromInput('startnum',
                                       'polls_title',
                                       'polls_options',
                                       'q',
                                       'bool',
                                       'sort');
    // Default parameters
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = xarModGetVar('polls', 'itemsperpage');
    }
    $data = array();
    if($q == ''){
        return $data;
    }
    if ($polls_title != 1){
        $polls_title = 0;
    }
    if ($polls_options != 1){
        $polls_options = 0;
    }
    // Get user information
    $data['polls'] = xarModAPIFunc('polls',
                          'user',
                          'search',
                           array('title' => $polls_title,
                                 'options' => $polls_options,
                                 'q' => $q));

    if (empty($data['polls'])){
        $data['status'] = xarML('No Polls Found Matching Search');
    }

    return $data;

}

?>
