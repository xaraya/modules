<?php
function helpdesk_userapi_cats($args){
    extract($args);
    if(!is_array($cids)) { $cids = array($cids); }
    $cats = xarModAPIFunc('categories', 'user', 'getchildren', 
                          array('cids' => $cids)
                         );
    if(!$cats) { return; }   
    //$l = array();
    $l = "";                      
    if(!empty($cats) && is_array($cats)){
        foreach($cats as $cat){
            if($cat['cid'] == $tcid){ return $cat['name']; }
            $temp = helpdesk_userapi_cats(array('cids' => $cat['cid'], 
                                                'tcid' => $tcid));
            if($temp){ $l = $cat['name'] . "->" . $temp; }                                                
        }
    }
    return $l;
}
?>
