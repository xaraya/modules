<?php

/**
 * create a new pubsub template
 * @param $args['name'] name of the template you want to create
 * @param $args['template'] the template text
 * @returns int
 * @return template ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_addtemplate($args)
{
    // Get arguments from argument array
    extract($args);
    $invalid = array();
    if (!isset($template) || !is_string($template)) {
        $invalid[] = 'template';
    }
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for function #(2)() in module #(3)',
                    join(', ',$invalid), 'addtemplate', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('AddPubSub')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubtemplatestable = $xartable['pubsub_templates'];

    // check this template isn't already in the DB
    $query = "SELECT xar_templateid
              FROM $pubsubtemplatestable
              WHERE xar_name = '" . xarVarPrepForStore($name) . "'";
    $result = $dbconn->Execute($query);
    if (!$result) return;

    if (!$result->EOF) {
        $msg = xarML('Item already exists in function #(1)() in module #(2)',
                    'addtemplate', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                      new SystemException($msg));
        return;
    }

    // compile the template now
    $compiled = xarTplCompileString($template);

    // Get next ID in table
    $nextId = $dbconn->GenId($pubsubtemplatestable);

    // Add item
    $query = "INSERT INTO $pubsubtemplatestable (
              xar_templateid,
              xar_name,
              xar_template,
              xar_compiled)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($name) . "',
              '" . xarvarPrepForStore($template) . "',
              '" . xarvarPrepForStore($compiled) . "')";
    $result = $dbconn->Execute($query);
    if (!$result) return;

    $nextId = $dbconn->PO_Insert_ID($pubsubtemplatestable, 'xar_templateid');

    // return eventID
    return $nextId;
}

?>
