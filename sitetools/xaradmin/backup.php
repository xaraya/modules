<?php
/*
 * File: $Id: $
 *
 * SiteTools Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by Jo Dalle Nogare
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage SiteTools module
 * @author jojodee <http://xaraya.athomeandabout.com >
*/

**
 * Backup tables in your database TO DO
 */
function sitetools_admin_backup()
{
/*    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    // Security check
    if (!xarSecurityCheck('AdminSiteTools')) return;
    
    // Check for confirmation.
    if (empty($confirm)) {
        // No confirmation yet - display a suitable form to obtain confirmation
        // of this action from the user
        $data['finished']=false;
         // Generate a one-time authorisation code for this operation
        $data['authid'] = xarSecGenAuthKey();
        
           list($dbconn) = xarDBGetConn();
        $dbname= xarDBGetName();
        $data['dbname'] =$dbname;
        echo "The database name is ".$dbname;

        $tables = array();
        $query = mysql_list_tables($dbname);
        $result      = @mysql_query($query);
        if ($result) {
             while ($row = mysql_fetch_row($result))
            {
                $tables[] = array("id" => $row[0], "name" => $row[0]);
            }
       }else {
            echo "No table query results";
       }

       $data[]=$tables;
       // Return the template variables defined in this function
        return $data;
    }
    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;


   return true; */
    }

?>
