<?php

/**
 * Update a publication type
 *
 * @param $args['ptid'] ID of the publication type
 * @param $args['name'] name of the publication type (not allowed here)
 * @param $args['descr'] description of the publication type
 * @param $args['config'] configuration of the publication type
 * @returns bool
 * @return true on success, false on failure
 */
function articles_adminapi_updatepubtype($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present
    // and in the right format, if not then set an appropriate error
    // message and return
    // Note : since we have several arguments we want to check here, we'll
    // report all those that are invalid at the same time...
    $invalid = array();
    if (!isset($ptid) || !is_numeric($ptid) || $ptid < 1) {
        $invalid[] = 'publication type ID';
    }
/*
    if (!isset($name) || !is_string($name) || empty($name)) {
        $invalid[] = 'name';
    }
*/
    if (!isset($descr) || !is_string($descr) || empty($descr)) {
        $invalid[] = 'description';
    }
    if (!isset($config) || !is_array($config) || count($config) == 0) {
        $invalid[] = 'configuration';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'updatepubtype','Articles');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // Security check - we require ADMIN rights here
    if (!xarSecurityCheck('AdminArticles',1,'Article',"$ptid:All:All:All")) return;

    // Load user API to obtain item information function
    if (!xarModAPILoad('articles', 'user')) return;

    // Get current publication types
    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
    if (!isset($pubtypes[$ptid])) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'publication type ID', 'admin', 'updatepubtype',
                    'Articles');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // Make sure we have all the configuration fields we need
    $pubfields = xarModAPIFunc('articles','user','getpubfields');
    foreach ($pubfields as $field => $value) {
        if (!isset($config[$field])) {
            $config[$field] = '';
        }
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubtypestable = $xartable['publication_types'];

    // Update the publication type (don't allow updates on name)
    $query = "UPDATE $pubtypestable
            SET xar_pubtypedescr = '" . xarVarPrepForStore($descr) . "',
                xar_pubtypeconfig = '" . xarVarPrepForStore(serialize($config)) . "'
            WHERE xar_pubtypeid = " . xarVarPrepForStore($ptid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    return true;
}

?>
