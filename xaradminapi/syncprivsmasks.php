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

function cachesecurity_adminapi_syncprivsmasks()
{
    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();

    $security_masks = $tables['security_masks'];
    $security_cache_privsmasks = $tables['security_cache_privsmasks'];
    $privileges = $tables['privileges'];

    $old_fetchmode = $dbconn->fetchMode;
    $dbconn->setFetchMode(ADODB_FETCH_ASSOC);

    if (!xarModAPIFunc(
        'cachesecurity','admin','unsetsynchronized', array('part'=>'privsmasks')
    )) return;

    $query = "DELETE FROM $security_cache_privsmasks";
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

    foreach ($privileges as $privilege) {
        foreach ($masks as $mask) {
            if ((
                    strtolower($mask['xar_realm']) == 
                        strtolower($privilege['xar_realm']) OR
                    $privilege['xar_realm'] == 'All' OR
                    $mask['xar_realm'] == 'All' 
                ) AND (
                    strtolower($mask['xar_module']) == 
                        strtolower($privilege['xar_module']) OR
                    $privilege['xar_module'] == 'All' OR 
                    $mask['xar_module'] == 'All' 
                ) AND (
                    strtolower($mask['xar_component']) == 
                        strtolower($privilege['xar_component']) OR
                    $privilege['xar_component'] == 'All' OR 
                    $mask['xar_component'] == 'All' 
                )) 
            {
                $query = "INSERT INTO $security_cache_privsmasks (xar_priv_id, xar_mask_id) ".
                                 "VALUES (?, ?)";
                $result = & $dbconn->execute($query, array(
                    $privilege['xar_pid'], $mask['xar_sid']));
                if (!$result) return;
            }
        }
    }

    if (!xarModAPIFunc(
        'cachesecurity','admin','setsynchronized', array('part'=>'privsmasks')
    )) return;

    return true;
}

?>