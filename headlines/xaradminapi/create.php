<?
/**
 * create a new headline
 * @param $args['url'] url of the item
 * @returns int
 * @return headline ID on success, false on failure
 */
function headlines_adminapi_create($args)
{

    // Get arguments from argument array
    extract($args);


    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if (!isset($url)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'create', 'Headlines');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security Check
	if(!xarSecurityCheck('AddHeadlines')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $headlinestable = $xartable['headlines'];

    // Get next ID in table
    $nextId = $dbconn->GenId($headlinestable);

    // Add item
    $query = "INSERT INTO $headlinestable (
              xar_hid,
              xar_url,
              xar_order)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($url) . "',
              $nextId)";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $hid = $dbconn->PO_Insert_ID($headlinestable, 'xar_hid');

    // Let any hooks know that we have created a new link
    xarModCallHooks('item', 'create', $hid, 'hid');

    // Return the id of the newly created link to the calling process
    return $hid;
}
?>