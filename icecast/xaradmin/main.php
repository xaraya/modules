<?php
/**
 * File: $Id:
 * 
 * icecast main administration function
 * 
 * @copyright (C) 2004 by Johnny Robeson
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link 
 *
 * @subpackage icecast
 * @author Johnny Robeson 
 */
 function icecast_admin_main()
{ 
    if (!xarSecurityCheck('AdminIcecast')) return; 
    
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        $data = xarModAPIFunc('icecast', 'admin', 'menu'); 
        $data['welcome'] = xarML('Welcome to the administration part of this icecast module...'); 
        return $data;  
    } else {
        xarResponseRedirect(xarModURL('icecast', 'admin', 'view'));
    }
     
    return true;
} 
?>
