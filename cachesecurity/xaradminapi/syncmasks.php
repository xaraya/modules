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

function cachesecurity_adminapi_syncmasks()
{
    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();

    $security_masks = $tables['security_masks'];
    $security_cache_masks = $tables['security_cache_masks'];

    $old_fetchmode = $dbconn->fetchMode;
    $dbconn->setFetchMode(ADODB_FETCH_ASSOC);

    if (!xarModAPIFunc(
        'cachesecurity','admin','unsetsynchronized', array('part'=>'masks')
    )) return;

    $query = "DELETE FROM $security_cache_masks";
    $result = & $dbconn->execute($query);
    if (!$result) return;

    $query = "SELECT * FROM $security_masks";
    $result = & $dbconn->execute($query);
    if (!$result) return;

    $masks = array();
    while (!$result->EOF) {
        $masks[] = $result->fields;
        $result->MoveNext();
    }

    $result->Close();
    $dbconn->setFetchMode($old_fetchmode);

    $instancesNumber = xarModAPIFunc('cachesecurity','admin','getinstancesnumber');

    $size = count($masks);
    for ($i=0;$i<$size;$i++) {
        //Descriptions are useless for the cache
        //IDs too, but i am keeping them to allow to debug errors in the cache system
        unset($masks[$i]['xar_description']);

        //New name for the id column.. Will make it easier with all the joins in the cache system
        $masks[$i]['xar_mask_id'] = $masks[$i]['xar_sid'];
        unset($masks[$i]['xar_sid']);

        $instances = explode(':', $masks[$i]['xar_instance']);
        unset($masks[$i]['xar_instance']);
        for ($j=0;$j<$instancesNumber;$j++) {
            if (empty($instances[$j])) {
                $masks[$i]['xar_instance'.($j+1)] ='All';
            } else {
                $masks[$i]['xar_instance'.($j+1)] = $instances[$j];
            }
        }
    }

    $columns = array_keys($masks[0]);

    $query = "INSERT INTO $security_cache_masks (".implode(', ', $columns).") ".
                     "VALUES (?".str_repeat(',?', count($columns)-1).")";

    foreach ($masks as $mask) {
        $result = & $dbconn->execute($query, $mask);
        if (!$result) return;
    } 

    if (!xarModAPIFunc(
        'cachesecurity','admin','setsynchronized', array('part'=>'masks')
    )) return;

    return true;
}

?>
