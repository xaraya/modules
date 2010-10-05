<?php
function fulltext_userapi_search($args)
{
    extract($args);
    
    if (!isset($q) && !is_string($q))
        $invalid[] = 'query';    
    
    // Get database information
    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $ftable = $tables['fulltext'];
    $where = array();
    $bindvars = array();
    $query = "
        SELECT id, module_id, itemtype, item_id, text,
            MATCH(text) AGAINST (?) AS score
        FROM $ftable
        WHERE MATCH(text) AGAINST(?)
    ";
    $bindvars[] = $q;
    $bindvars[] = $q;

    $stmt = $dbconn->prepareStatement($query);
    $result = $stmt->executeQuery($bindvars);   
    if (!$result) return;
    
    $results = array();
    while($result->next()) {
        list($id, $module_id, $itemtype, $itemid, $text, $score) = $result->fields;
        $results[] = array(
            'id' => $id,
            'module_id' => $module_id,
            'itemtype' => $itemtype,
            'itemid' => $itemid,
            'text' => $text,
            'score' => $score,
        );
    }
    $result->close();
    if (empty($results)) {
        $where = array();
        $bindvars = array();
        $query = "
            SELECT id, module_id, itemtype, item_id, text
            FROM $ftable
        ";
        if (strpos($q, ' ') !== false) {
            $words = explode(' ', $q);
        } else {
            $words = array($q);
        }
        foreach ($words as $word) {
            if (empty($word)) continue;
            $word = "%$word%";
            $where[] = "text LIKE ?";
            $bindvars[] = $word;
        }
        if (!empty($where)) 
            $query .= " WHERE " . join(" OR ", $where);
        //print_r($query);
        $stmt = $dbconn->prepareStatement($query);
        $result = $stmt->executeQuery($bindvars);   
        if (!$result) return;
        $results = array();
        while($result->next()) {
            list($id, $module_id, $itemtype, $itemid, $text) = $result->fields;
            $results[] = array(
                'id' => $id,
                'module_id' => $module_id,
                'itemtype' => $itemtype,
                'itemid' => $itemid,
                'text' => $text,
                'score' => null,
            );
        }
        $result->close();
    }
            
    return $results;
    
}
?>