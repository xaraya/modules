<?php
function netquery_adminapi_getbbid($args)
{
    extract($args);
    if ((!isset($id)) || (!is_numeric($id)))
    {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $SpamblockerTable = $xartable['netquery_spamblocker'];
    $query = "SELECT * FROM $SpamblockerTable WHERE id = ?";
    $bindvars = array($id);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($id, $ip, $date, $request_method, $request_uri, $server_protocol, $user_agent, $http_headers, $request_entity, $key) = $result->fields;
    $response = xarModAPIFunc('netquery', 'admin', 'bb2_response', (array('key' => $key)));
    $entry = array('id'              => $id,
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
    $result->Close();
    return $entry;
}
?>