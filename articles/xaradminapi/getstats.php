<?php

/**
 * count number of items depending on additional module criteria
 *
 * @returns array
 * @return number of items
 */
function articles_adminapi_getstats($args)
{
    extract($args);

    $allowedfields = array('pubtypeid', 'status', 'authorid', 'language');
    if (empty($group)) {
        $group = array();
    }
    $newfields = array();
    foreach ($group as $field) {
        if (empty($field) || !in_array($field,$allowedfields)) {
            continue;
        }
        $newfields[] = 'xar_' . $field;
    }
    if (empty($newfields) || count($newfields) < 1) {
        $newfields = array('xar_pubtypeid', 'xar_status', 'xar_authorid');
    }

    // Database information
    $dbconn =& xarDBGetConn();
    $xartables =& xarDBGetTables();

    $query = 'SELECT ' . join(', ', $newfields) . ', COUNT(*)
              FROM ' . $xartables['articles'] . '
              GROUP BY ' . join(', ', $newfields);

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $stats = array();
    while (!$result->EOF) {
        if (count($newfields) > 3) {
            list($field1,$field2,$field3,$field4,$count) = $result->fields;
            $stats[$field1][$field2][$field3][$field4] = $count;
        } elseif (count($newfields) == 3) {
            list($field1,$field2,$field3,$count) = $result->fields;
            $stats[$field1][$field2][$field3] = $count;
        } elseif (count($newfields) == 2) {
            list($field1,$field2,$count) = $result->fields;
            $stats[$field1][$field2] = $count;
        } elseif (count($newfields) == 1) {
            list($field1,$count) = $result->fields;
            $stats[$field1] = $count;
        }
        $result->MoveNext();
    }
    $result->Close();

    return $stats;
}

?>
