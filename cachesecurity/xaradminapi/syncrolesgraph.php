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

function cachesecurity_adminapi_syncrolesgraph()
{
    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();

    $rolemembers = $tables['rolemembers'];
    $security_cache_rolesgraph = $tables['security_cache_rolesgraph'];
    $roles = $tables['roles'];

    $old_fetchmode = $dbconn->fetchMode;
    $dbconn->setFetchMode(ADODB_FETCH_ASSOC);

    if (!xarModAPIFunc(
        'cachesecurity','admin','unsetsynchronized', array('part'=>'rolesgraph')
    )) return;

    $query = "DELETE FROM $security_cache_rolesgraph";
    $result = & $dbconn->execute($query);
    if (!$result) return;

    $query = "SELECT * FROM $rolemembers";
    $result = & $dbconn->execute($query);
    if (!$result) return;

    $links = array();
    while (!$result->EOF) {
        $links[] = $result->fields;
        $result->MoveNext();
    }
    $result->Close();
    
    $query = "SELECT xar_uid FROM $roles";
    $result = & $dbconn->execute($query);
    if (!$result) return;

    $roles = array();
    while (!$result->EOF) {
        $roles[] = $result->fields;
        $result->MoveNext();
    }
    $result->Close();
    
    $dbconn->setFetchMode($old_fetchmode);

    $new_links = array();

    foreach ($roles as $role) {
        //For each id, add a link 0 to itself. Economizes a join latter on. 
        $new_links[] = array(
            'xar_role_id' => (int) $role['xar_uid'], 
            'xar_role_sibbling_id' => (int) $role['xar_uid'], 
            'xar_role_distance' => 0
        );

        //key=>descendent, value=>depth
        $descendents = xarModAPIFunc(
                'cachesecurity','admin','getalldescendents', array(
                    'array' => $links, 'id' =>(int) $role['xar_uid'],
                    'id_column' => 'xar_uid', 'parent_column' => 'xar_parentid'));
        
        foreach ($descendents as $key => $value) {
            $new_links[] = array(
                'xar_role_id' => (int) $role['xar_uid'], 
                'xar_role_sibbling_id' => $key, 
                'xar_role_distance' => $value
            );
        }
    }

    $columns = array_keys($new_links[0]);

    $query = "INSERT INTO $security_cache_rolesgraph (".implode(', ', $columns).") ".
                     "VALUES (?".str_repeat(',?', count($columns)-1).")";

    foreach ($new_links as $new_link) {
        $result = & $dbconn->execute($query, $new_link);
        if (!$result) return;
    } 

    if (!xarModAPIFunc(
        'cachesecurity','admin','setsynchronized', array('part'=>'rolesgraph')
    )) return;

    return true;
}

?>
