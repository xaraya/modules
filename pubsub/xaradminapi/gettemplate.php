<?php

/**
 * get an existing pubsub template
 * @param $args['templateid'] the ID of the item
 * @returns array
 * @return array of template information
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_gettemplate($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($templateid) || !is_numeric($templateid)) {
        $invalid[] = 'templateid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for function #(2)() in module #(3)',
                    join(', ',$invalid), 'gettemplate', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('EditPubSub', 1, 'item', "All:All:All:$templateid")) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubtemplatestable = $xartable['pubsub_templates'];

    // Update the item
    $query = "SELECT xar_templateid,
                     xar_name,
                     xar_template,
                     xar_compiled
              FROM $pubsubtemplatestable
              WHERE xar_templateid = " . xarVarPrepForStore($templateid);
    $result = $dbconn->Execute($query);
    if (!$result) return;

    $info = array();
    if ($result->EOF) return $info;

    list($info['templateid'],$info['name'],$info['template'],$info['compiled']) = $result->fields;
    $result->Close();

    return $info;
}

?>
