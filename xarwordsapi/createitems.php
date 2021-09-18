<?php

function keywords_wordsapi_createitems(array $args=[])
{
    extract($args);

    if (empty($index_id) || !is_numeric($index_id)) {
        $invalid[] = 'index_id';
    }

    if (isset($keyword)) {
        if (is_string($keyword)) {
            $keyword = (strpos($keyword, ',') !== false) ?
                array_map('trim', explode(',', $keyword)) : [trim($keyword)];
        }
        if (is_array($keyword)) {
            $keyword = array_unique(array_filter($keyword));
            foreach ($keyword as $dt) {
                if (!is_string($dt)) {
                    $invalid[] = 'keyword';
                    break;
                }
            }
        } else {
            $invalid[] = 'keyword';
        }
    } else {
        $invalid[] = 'keyword';
    }

    if (!empty($invalid)) {
        $msg = 'Invalid #(1) for #(2) module #(3) function #(4)()';
        $vars = [implode(', ', $invalid), 'keywords', 'wordsapi', 'createitems'];
        throw new BadParameterException($vars, $msg);
    }

    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $wordstable = $tables['keywords'];

    $values = [];
    $bindvars = [];

    foreach ($keyword as $word) {
        $values[] = '(?,?)';
        $bindvars[] = $index_id;
        $bindvars[] = $word;
    }

    // Insert items
    try {
        $dbconn->begin();
        $insert = "INSERT INTO $wordstable (index_id, keyword)";
        $insert .= " VALUES " . implode(',', $values);
        $stmt = $dbconn->prepareStatement($insert);
        $result = $stmt->executeUpdate($bindvars);
        $dbconn->commit();
    } catch (SQLException $e) {
        $dbconn->rollback();
        throw $e;
    }
    return true;
}
