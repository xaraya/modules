<?php

/**
 * update an existing pubsub template
 * @param $args['templateid'] the ID of the item
 * @param $args['name'] the new name of the item
 * @param $args['template'] the new template text of the item
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_updatetemplate($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($templateid) || !is_numeric($templateid)) {
        $invalid[] = 'templateid';
    }
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($template) || !is_string($template)) {
        $invalid[] = 'template';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) function #(3)() in module #(4)',
                    join(', ',$invalid), 'updatetemplate', 'Pubsub');
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

    // compile the template now
    $compiled = xarTplCompileString($template);

    // Update the item
    $query = "UPDATE $pubsubtemplatestable
              SET xar_template = '" . xarVarPrepForStore($template) . "',
                  xar_compiled = '" . xarVarPrepForStore($compiled) . "',
                  xar_name = '" . xarVarPrepForStore($name) . "'
              WHERE xar_templateid = " . xarVarPrepForStore($templateid);
    $result = $dbconn->Execute($query);
    if (!$result) return;

    return true;
}

?>
