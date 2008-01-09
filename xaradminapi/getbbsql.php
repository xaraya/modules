<?php
function netquery_adminapi_getbbsql($args)
{
    extract($args);
    if ((!isset($startnum)) || (!is_numeric($startnum))) $startnum = 1;
    if ((!isset($numitems)) || (!is_numeric($numitems))) $numitems = -1;
    if ($where=="LIKE") $search = "%".$search."%";
    if (!is_numeric($search)) $search = "'".$search."'";
    $entries = array();
    if (!xarSecurityCheck('ReadNetquery')) return $entries;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $SpamblockerTable = $xartable['netquery_spamblocker'];
    $query = "SELECT * FROM $SpamblockerTable WHERE $field $where $search ORDER BY id DESC";
    $result =& $dbconn->SelectLimit($query, (int)$numitems, (int)$startnum-1);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext())
    {
        list($id, $ip, $date, $request_method, $request_uri, $server_protocol, $user_agent, $http_headers, $request_entity, $key) = $result->fields;
        $response = xarModAPIFunc('netquery', 'admin', 'bb2_response', (array('key' => $key)));
        $entries[] = array('id'              => $id,
                           'ip'              => $ip,
                           'date'            => $date,
                           'request_method'  => $request_method,
                           'request_uri'     => $request_uri,
                           'server_protocol' => $server_protocol,
                           'user_agent'      => $user_agent,
                           'http_headers'    => $http_headers,
                           'request_entity'  => $request_entity,
                           'key'             => $key,
                           'response'        => $response['response'],
                           'explanation'     => $response['explanation'],
                           'log'             => $response['log']);
    }
    $result->Close();
    return $entries;
}
?>