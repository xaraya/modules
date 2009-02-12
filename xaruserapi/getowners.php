<?php
 
function dossier_userapi_getowners($args)
{
    // Database information
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    
    $contacts_table = $xarTables['dossier_contacts'];
    $roles_table = $xarTables['roles'];
    
    if (!xarModAPILoad('dossier', 'user')) return;
    
    $query = 'SELECT DISTINCT ' . $contacts_table.'.userid, ' . $roles_table.'.xar_name';
    $query .= ' FROM ' . $contacts_table.', ' . $roles_table;
    $query .= ' WHERE ' . $roles_table . '.xar_uid = ' . $contacts_table.'.userid';
/*
    if (!isset($args['cids'])) {
        $args['cids'] = array();
    }
    if (!isset($args['andcids'])) {
        $args['andcids'] = false;
    }
    if (count($args['cids']) > 0) {
        // Load API
        if (!xarModAPILoad('categories', 'user')) return;

        // Get the LEFT JOIN ... ON ...  and WHERE (!) parts from categories
        $args['modid'] = xarModGetIDFromName('articles');
        if (isset($args['ptid']) && !isset($args['itemtype'])) {
            $args['itemtype'] = $args['ptid'];
        }
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',$args);

        $query .= ' LEFT JOIN ' . $categoriesdef['table'];
        $query .= ' ON ' . $categoriesdef['field'] . ' = '
                . $articlesdef['aid'];
        $query .= $categoriesdef['more'];
        $docid = 1;
    }

    // Create the WHERE part
    $where = array();
    // we rely on leftjoin() to create the necessary articles clauses now
    if (!empty($articlesdef['where'])) {
        $where[] = $articlesdef['where'];
    }
    if (!empty($docid)) {
        // we rely on leftjoin() to create the necessary categories clauses
        $where[] = $categoriesdef['where'];
    }
    if (count($where) > 0) {
        $query .= ' WHERE ' . join(' AND ', $where);
    }
*/

    // Order by author name
    $query .= ' ORDER BY ' . $roles_table.'.xar_name ASC';

    // Run the query - finally :-)
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $owners = array();
    while (!$result->EOF) {
        list($uid, $name) = $result->fields;
        $owners[$uid] = $name;
        $result->MoveNext();
    }

    $result->Close();

    return $owners;
}

?>
