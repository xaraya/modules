<?php

/**
 * Get the name of all templates
 *
 * @returns array
 * @return array of templates ids and names
 */
function pubsub_adminapi_getalltemplates($args)
{
    $templates = array();
    if (!xarSecurityCheck('AdminPubSub')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubtemplatestable = $xartable['pubsub_templates'];

    $query = "SELECT xar_templateid,
                     xar_name
                FROM $pubsubtemplatestable";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($templateid, $name) = $result->fields;
        $templates[$templateid] = $name;
    }

    $result->Close();

    return $templates;
}

?>
