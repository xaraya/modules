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

function cachesecurity_adminapi_syncprivsgraph()
{
    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();

    $privmembers = $tables['privmembers'];
    $security_cache_privsgraph = $tables['security_cache_privsgraph'];
    $privileges = $tables['privileges'];

    $old_fetchmode = $dbconn->fetchMode;
    $dbconn->setFetchMode(ADODB_FETCH_ASSOC);

    if (!xarModAPIFunc(
        'cachesecurity','admin','unsetsynchronized', array('part'=>'privsgraph')
    )) return;

    $query = "DELETE FROM $security_cache_privsgraph";
    $result = & $dbconn->execute($query);
    if (!$result) return;

    $query = "SELECT * FROM $privmembers";
    $result = & $dbconn->execute($query);
    if (!$result) return;

    $links = array();
    while (!$result->EOF) {
        $links[] = $result->fields;
        $result->MoveNext();
    }
    $result->Close();
    
    $query = "SELECT xar_pid FROM $privileges";
    $result = & $dbconn->execute($query);
    if (!$result) return;

    $privileges = array();
    while (!$result->EOF) {
        $privileges[] = $result->fields;
        $result->MoveNext();
    }
    $result->Close();
    
    $dbconn->setFetchMode($old_fetchmode);

    $new_links = array();

    foreach ($privileges as $privilege) {
        //For each id, add a link 0 to itself. Economizes a join latter on. 
        $new_links[] = array(
            'xar_priv_id' => (int) $privilege['xar_pid'], 
            'xar_priv_sibbling_id' => (int) $privilege['xar_pid'], 
//            'xar_priv_distance' => 0
        );

        //key=>descendent, value=>depth
        $descendents = xarModAPIFunc(
                'cachesecurity','admin','getalldescendents', array(
                    'array' => $links, 'id' =>(int) $privilege['xar_pid'],
                    'id_column' => 'xar_pid', 'parent_column' => 'xar_parentid'));
        
        foreach ($descendents as $key => $value) {
            $new_links[] = array(
                'xar_priv_id' => (int) $privilege['xar_pid'], 
                'xar_priv_sibbling_id' => $key, 
//                'xar_priv_distance' => $value
            );
        }
    }

    $columns = array_keys($new_links[0]);

    $query = "INSERT INTO $security_cache_privsgraph (".implode(', ', $columns).") ".
                     "VALUES (?".str_repeat(',?', count($columns)-1).")";

    foreach ($new_links as $new_link) {
        $result = & $dbconn->execute($query, $new_link);
        if (!$result) return;
    } 

    if (!xarModAPIFunc(
        'cachesecurity','admin','setsynchronized', array('part'=>'privsgraph')
    )) return;

    return true;
}

?>
