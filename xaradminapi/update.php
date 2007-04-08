<?php
/**
 * Webshare Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage webshare Module
 * @link http://xaraya.com/index.php/release/883.html
 * @author Andrea Moro
 */
/**
 * utility function update website information
 *
 * @author Andrea Moro
 * @param array $args['active'] array of websites to set active
 * @return bool true on success
 */
function webshare_adminapi_update($args)
{
    // Security Check
    if (!xarSecurityCheck('AdminWebshare')) {
	    return false;
    }
	extract($args);
	if (isset($active)) {
        $bindvars = array();
        $dbconn =& xarDBGetConn();
 	    $xartable =& xarDBGetTables();
 	    $table = $xartable['webshare'];
        $bindvars = array();
 	    foreach ($active as $key=>$value) {
 	        if ($value) {$activate[]="xar_id=$key"; 
 		    } else {
 		        $deactivate[]="xar_id=$key";
            }
 	    }
 	    if ($activate) {
		    $whereactivate = implode($activate,' OR ');
	    } else {
		    $whereactivate = '0';
		}
 	    if ($deactivate) {
 	    $wheredeactivate = implode($deactivate,' OR ');
	    } else {
		    $wheredeactivate = '0';
		}

	    // Update the item
	    $query = "UPDATE $table
	              SET xar_active = TRUE
                  WHERE $whereactivate ";
        $result =& $dbconn->Execute($query);
        if (!$result) return false;
		
	    $query = "UPDATE $table
	              SET xar_active = FALSE 
                  WHERE $wheredeactivate ";
        $result =& $dbconn->Execute($query);
        if (!$result) return false;
	}

    return true;
}
?>
