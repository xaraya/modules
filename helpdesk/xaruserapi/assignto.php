<?php
/**
    Finds a Rep to assign this ticket to
    
    @author Brian McGilligan
    @param $args['cats'] - The Selected Cats.
    @return uid of rep ticket needs to be assigned to 
*/
function helpdesk_userapi_assignto($args)
{
    extract($args);
    
    // Get reps for further processing
    $reps = xarModAPIFunc('dynamicdata', 'user', 'getitems', 
                          array('module' => 'helpdesk',
                                'itemtype' => 10
                               )
                         );

    $cats = xarModAPIFunc('categories', 'user', 'getlinks',
                          array('modid'    => 910,
                                'itemtype' => 10,
                                'cids'     => $cids)
                         );
    /*
        We are giving software ids higher precedence than type ids
    */ 
    $rids = array();
    if(!empty($cats)){    
        foreach($cats as $cat){
            foreach($cat as $repid){
                $rids[] = $repid;
            }
        }    
    }
    
    if(empty($rids[0])){ return 0; }    
    
    mt_srand((double)microtime()*100);
    $index = mt_rand(0, (sizeof($rids) - 1));    
        
    foreach($reps as $rep){
        if($rep['id'] == $rids[$index])
            return $rep['name'];
    }
    return 0;
}
?>
