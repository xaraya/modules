<?php
/**
 * File: $Id$
 *
 * Sniffer Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Sniffer Module
 * @author Frank Besler
 *
 * Using phpSniffer by Roger Raymond
 * Purpose of file: find out the browser and OS of the visitor
*/

/**
 * This function is called directly during installation
 * and is used in the event handler function below
 *
 * @return array of user agent id and client
 */
function sniffer_userapi_sniffbasic($args)
{
    // Extract args
    if(!empty($args)) { // jsb: args get passed in from sniff.php, seem to be empty.  anyone know what is supposed to be in the args?
        extract($args);
    }

	// sniff process
    include_once('modules/sniffer/class/xarSniff.php');
    $client = new xarSniff('',0);
    $client->init();

	// Get database setup
    $dbconn =& xarDBGetConn();
    $xartable     =& xarDBGetTables();

	// set some variables used in the database call
    $sniffertable  =  $xartable['sniffer'];
//	$uacolumn = &$xartable['user_agent_column'];

    $sql = "SELECT xar_ua_id
            FROM $sniffertable
            WHERE xar_ua_agent = '" . xarVarPrepForStore($client->get_property('ua')) . "'";
    $result =& $dbconn->Execute($sql);
    if (!$result) return;

    if (!$result->EOF) {
        $uaid = $result->fields[0];
    } else {
        $nextID = $dbconn->GenId($sniffertable);
        $insarr = array($nextID, xarVarPrepForStore($client->get_property('ua')),
				        $client->property('platform'), $client->property('os'),
                        $client->getname('browser'), $client->property('version'));

        $query = "INSERT INTO $sniffertable
                  VALUES ({$insarr[0]},  '{$insarr[1]}', '{$insarr[2]}',
                         '{$insarr[3]}', '{$insarr[4]}', '{$insarr[5]}', '', '')";
//      last 2 are reserved for caps and quirks, supported by the sniffers cvs-version
        $result =& $dbconn->Execute($query);
	    if (!$result) return;        
        $uaid = $dbconn->PO_Insert_ID($sniffertable, 'xar_ua_id');
    }
    
    return array('uaid' => $uaid, 'client' => $client);
}

?>
