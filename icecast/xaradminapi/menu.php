<?php
/**
 * Generate the common admin menu configuration
 * 
 * @copyright (C) 2004 by Johnny Robeson
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link 
 * @subpackage icecast
 * @author Johnny Robeson 
 */
function icecast_adminapi_menu()
{ 
    
    $data = array(); 
    
    $data['menutitle'] = xarML('Icecast Administration'); 
    $data['status'] = ''; 

    return $data;
} 

?>
