<?
/**
 * create a new headline
 * @param $args['url'] url of the item
 * @returns int
 * @return headline ID on success, false on failure
 */
function sitecloud_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);

    // Security Check
	if(!xarSecurityCheck('Addsitecloud')) return;

    // We need to grab the current url right now for the string and the date
    $filedata = xarModAPIFunc('base', 'user', 'getfile',
                              array('url'       =>  $url,
                                    'cached'    =>  false));

    $string     = md5($filedata);
    $date       = time();

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sitecloudtable = $xartable['sitecloud'];

    // Get next ID in table
    $nextId = $dbconn->GenId($sitecloudtable);

    // Add item
    $query = "INSERT INTO $sitecloudtable (
              xar_id,
              xar_title,
              xar_url,
              xar_string,
              xar_date)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($url) . "',
              '" . xarVarPrepForStore($title) . "',
              '" . xarVarPrepForStore($string) . "',
              '" . xarVarPrepForStore($date) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $id = $dbconn->PO_Insert_ID($sitecloudtable, 'xar_id');

    // Let any hooks know that we have created a new link
    xarModCallHooks('item', 'create', $id, 'id');

    // Return the id of the newly created link to the calling process
    return $id;
}
?>