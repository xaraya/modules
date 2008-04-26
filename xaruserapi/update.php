<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author Chris Powis
 */
/**
 * update a headline
 * @param $args['hid'] the ID of the link - required
 * @param $args['date'] new hash date - optional defaults to time()
 * @param $args['string'] new hash - required
 */
function headlines_userapi_update($args)
{
    // Get arguments from argument array
    extract($args);

    $date = isset($date) && is_numeric($date) && !empty($date) ? $date : time();
    // Argument check
    if ((!isset($hid)) ||
        (!isset($string))) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // Security Check
    // TODO: seperate check will be needed for my user function
    // this only covers updating the hash and date right now
    if(!xarSecurityCheck('ReadHeadlines')) return;
    // TODO: eventually this check will need to be re-introduced
    // function needs updating to allow users to add/update their own feeds (cfr. my user function)
    /* The user API function is called
    $link = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));

    if ($link == false) return;
    */
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $headlinestable = $xartable['headlines'];

    // Update the link
    $query = "UPDATE $headlinestable
              SET xar_string   = ?,
                  xar_date     = ?
              WHERE xar_hid     = ?";
    $bindvars = array($string, $date, $hid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;     
    // TODO: Let the calling process know that we have finished successfully
    // bring this back for my user function, atm no hooks are affected by this call
    // xarModCallHooks('item', 'update', $hid, '');
    return true;
}
?>
