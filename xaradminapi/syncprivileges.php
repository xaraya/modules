<?php 
/**
 * File: $Id$
 * 
 * Xaraya's CacheSecurity Module
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage CacheSecurity Module
 * @author Flavio Botelho <nuncanada@xaraya.com>
*/

function cachesecurity_adminapi_syncprivileges ()
{
    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();

    //**** DIFFERENT FROM MASKS *****/
    $privileges = $tables['privileges'];
    $security_cache_privileges = $tables['security_cache_privileges'];
    //**** DIFFERENT FROM MASKS *****/

    $old_fetchmode = $dbconn->fetchMode;
    $dbconn->setFetchMode(ADODB_FETCH_ASSOC);

    if (!xarModAPIFunc(
        'cachesecurity','admin','unsetsynchronized', array('part'=>'privileges')
    )) return;

    $query = "DELETE FROM $security_cache_privileges";
    $result = & $dbconn->execute($query);
    if (!$result) return;


    $query = "SELECT * FROM $privileges";
    $result = & $dbconn->execute($query);
    if (!$result) return;

    $privileges = array();
    while (!$result->EOF) {
        $privileges[] = $result->fields;
        $result->MoveNext();
    }

    $result->Close();
    $dbconn->setFetchMode($old_fetchmode);

    $instancesNumber = xarModAPIFunc('cachesecurity','admin','getinstancesnumber');

    $size = count($privileges);
    for ($i=0;$i<$size;$i++) {
        //Descriptions are useless for the cache
        //IDs too, but i am keeping them to allow to debug errors in the cache system
        unset($privileges[$i]['xar_description']);

        //**** DIFFERENT FROM MASKS *****/
        //Privileges names are useless for the cache
        unset($privileges[$i]['xar_name']);

        //New name for the id column.. Will make it easier with all the joins in the cache system
        $privileges[$i]['xar_priv_id'] = $privileges[$i]['xar_pid'];
        unset($privileges[$i]['xar_pid']);
        //**** DIFFERENT FROM MASKS *****/

        $instances = explode(':', $privileges[$i]['xar_instance']);
        unset($privileges[$i]['xar_instance']);
        for ($j=0;$j<$instancesNumber;$j++) {
            if (empty($instances[$j])) {
                $privileges[$i]['xar_instance'.($j+1)] ='All';
            } else {
                $privileges[$i]['xar_instance'.($j+1)] = $instances[$j];
            }
        }

        //Extra Hack. Sometimes modules appear with the first letter upper case
        //Sometimes all lowered.
        if ($privileges[$i]['xar_module'] != 'All') {
            $privileges[$i]['xar_module'] = strtolower($privileges[$i]['xar_module']);
        }
        if ($privileges[$i]['xar_component'] != 'All') {
            $privileges[$i]['xar_component'] = strtolower($privileges[$i]['xar_component']);
        }
    }

    $columns = array_keys($privileges[0]);

    $query = "INSERT INTO $security_cache_privileges (".implode(', ', $columns).") ".
                     "VALUES (?".str_repeat(',?', count($columns)-1).")";

    foreach ($privileges as $privilege) {
        $result = & $dbconn->execute($query, $privilege);
        if (!$result) return;
    } 

    if (!xarModAPIFunc(
        'cachesecurity','admin','setsynchronized', array('part'=>'privileges')
    )) return;

    return true;
}

?>
