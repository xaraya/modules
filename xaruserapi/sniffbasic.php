<?php
/**
 * Sniffer System
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sniffer Module
 * @link http://xaraya.com/index.php/release/775.html
 * @author Frank Besler using phpSniffer by Roger Raymond
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
//    $uacolumn = &$xartable['user_agent_column'];

    $sql = "SELECT xar_ua_id
            FROM $sniffertable
            WHERE xar_ua_agent = ?";
    $result =& $dbconn->Execute($sql, array((string) $client->get_property('ua')));
    if (!$result) return;

    if (!$result->EOF) {
        $uaid = $result->fields[0];
    } else {
        $nextID = $dbconn->GenId($sniffertable);
        $insarr = array($nextID, (string) $client->get_property('ua'),
                        (string) $client->property('platform'), (string) $client->property('os'),
                        (string) $client->getname('browser'), (string) $client->property('version'));

        $query = "INSERT INTO $sniffertable
                  VALUES (?, ?, ?, ?, ?, ?, '', '')";
//      last 2 are reserved for caps and quirks, supported by the sniffers cvs-version
        $result =& $dbconn->Execute($query, $insarr);
        if (!$result) return;
        $uaid = $dbconn->PO_Insert_ID($sniffertable, 'xar_ua_id');
    }

    return array('uaid' => $uaid, 'client' => $client);
}

?>
