<?php
/**
 * Psspl Added the API function for 
 * getting the prev and next links.
 * @param unknown_type $args
 */

include_once("./modules/commonutil.php");
function messages_userapi_get_prev_next_link( $args )
{
    extract($args);
	switch($folder)
	{
		case 'inbox' :	
			$total_record = xarModAPIFunc('messages','user','count_total');	    
			break;
		case 'sent'  :	
			$total_record = xarModAPIFunc('messages','user','count_sent');
			break;
		case 'drafts':  
			  $total_record  =  xarModAPIFunc('messages','user','count_drafts'); 	
			  break;	 	
	}
    
	$itemsperpage = xarModVars::get('messages', 'itemsperpage');   
	$record_to_disply = $startnum + $itemsperpage;
	
	if($record_to_disply <= $total_record){
     	$next_record = $startnum + $itemsperpage;
		$link['next_link'] = xarModURL('messages','user','display',
                                               array('folder' => $folder,
                                                     'startnum'    => $next_record)); 
	}
    if($itemsperpage < $startnum){
    	$prev_record = $startnum - $itemsperpage;
     	$link['prev_link'] = xarModURL('messages','user','display',
                                               array('folder' => $folder,
                                                     'startnum'    => $prev_record));
     }
     
     $link['startnum'] = $record_to_disply;
     $link['itemsperpage'] = $itemsperpage;
          
     return $link;
}
?>