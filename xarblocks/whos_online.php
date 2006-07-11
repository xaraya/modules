<?php
/**
 * AuthInvision module - who's online block
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authinvision
 * @link http://xaraya.com/index.php/release/950.html
 * @author ladyofdragons
 */

/**
 * initialise block
 */
function authinvision_whos_onlineblock_init()
{
    return array();
}

/**
 * get information on block
 */
function authinvision_whos_onlineblock_info()
{
    return array('text_type' => 'Online',
                 'module' => 'roles',
                 'text_type_long' => 'Display who is online');
}

/**
 * Display func.
 * @param $blockinfo array containing title,content
 */
function authinvision_whos_onlineblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ViewRoles',0,'Block',"All:" . $blockinfo['title'] . ":All", 'All')) return;

    // Get variables from content block
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sessioninfotable = $xartable['session_info'];
    $activetime = time() - (xarConfigGetVar('Site.Session.Duration') * 60);
    $sql = "SELECT COUNT(1)
            FROM $sessioninfotable
            WHERE xar_lastused > ? AND xar_uid > 2
            GROUP BY xar_uid
            ";
    $bindvars=array($activetime);
    $result = $dbconn->Execute($sql,$bindvars);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }
    $args['numusers'] = $result->RecordCount();
    $result->Close();

   $query2 = "SELECT count( 1 )
             FROM $sessioninfotable
              WHERE xar_lastused > ? AND xar_uid = '2'
              GROUP BY xar_ipaddr
             ";
   $bindvars=array($activetime);
   $result2 = $dbconn->Execute($query2,$bindvars);
   $args['numguests'] = $result2->RecordCount();
   $result2->Close();

       // Pluralise

   if ($args['numguests'] == 1) {
       $args['guests'] = xarML('guest');
   } else {
       $args['guests'] = xarML('guests');
   }

   if ($args['numusers'] == 1) {
       $args['users'] = xarML('user');
   } else {
       $args['users'] = xarML('users');
   }
   $args['blockid'] = $blockinfo['bid'];
    // Block formatting
    if (empty($blockinfo['title'])) {
        $blockinfo['title'] = xarML('Online');
    }

  $lastuser = xarModGetVar('roles', 'lastuser');

    // Make sure we have a lastuser
    if (!empty($lastuser)) {
        $status = xarModAPIFunc('authinvision', 'user', 'getlast',
                                array('lastuser' => $lastuser));
   $args['lastuser'] = $status['name'];
//    var_dump($status);
        // Check return
        if ($status)
            $args['lastuser'] = $status;
    }

    $args['blockid'] = $blockinfo['bid'];

    $blockinfo['content'] = $args;
    return $blockinfo;
}

?>
