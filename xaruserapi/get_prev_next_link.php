<?php
/**
 * Psspl Added the API function for 
 * getting the prev and next links.
 * @param unknown_type $args
 */

function messages_userapi_get_prev_next_link( $args )
{
    extract($args);
    switch($folder)
    {
        case 'inbox' :  
            $total_record = xarModAPIFunc('messages','user','get_count',array('recipient'=>xarUserGetVar('id')));     
            break;
        case 'sent'  :  
            $total_record = xarModAPIFunc('messages','user','get_count',array('author'=>xarUserGetVar('id')));
            break;
        case 'drafts':  
              $total_record  =  xarModAPIFunc('messages','user','get_count',array('author'=>xarUserGetVar('id'),'drafts'=>true));    
              break;        
    }
    
    $itemsperpage = xarModVars::get('messages', 'itemsperpage');   
    $record_to_disply = $startnum + $itemsperpage;
    
    if($record_to_disply <= $total_record){
        $next_record = $startnum + $itemsperpage;
        $link['next_link'] = xarModURL('messages','user','view',
                                               array('folder' => $folder,
                                                     'startnum'    => $next_record)); 
    }
    if($itemsperpage < $startnum){
        $prev_record = $startnum - $itemsperpage;
        $link['prev_link'] = xarModURL('messages','user','view',
                                               array('folder' => $folder,
                                                     'startnum'    => $prev_record));
     }
     
     $link['startnum'] = $record_to_disply;
     $link['itemsperpage'] = $itemsperpage;
          
     return $link;
}
?>
