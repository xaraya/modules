<?php
/**
 * Opentracker event API functions
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage opentracker
 * @author Chris "Alley" van de Steeg
 */

require_once(dirname(__FILE__) . '/xarOpenTracker.php');

function _opentracker_exit_urls($buffer)
{
  return preg_replace(
    "#<a href=(\"|')(http(s)*?://)([^\"']+)(\"|')#ime",
    '"<a href=\"".((substr_count(strtolower(\'\\2\\4\'), strtolower(xarServerGetBaseURL())) == 0) ? xarModUrl("opentracker", "user", "exit", array("proto"=>base64_encode(\'\\2\'), "url" => base64_encode(\'\\4\') ) ) : "\\2\\4" )."\""',
    $buffer
  );
}

function opentracker_eventapi_OnServerRequest()
{
  // track outgoing URLs
  $trackoutgoing = xarModGetVar('opentracker','trackoutgoing');
  if (!empty($trackoutgoing)) {
    // start output-buffering
    ob_start('_opentracker_exit_urls');
  }

  header('P3P: CP="NOI NID ADMa OUR IND UNI COM NAV"');

  // don't count admin hits
  $countadmin = xarModGetVar('opentracker','countadmin');
  if (empty($countadmin) && xarSecurityCheck('AdminOpentracker', 0)) {
    return true;
  }
  xarOpenTracker::log(
    array(
      'document' => $GLOBALS['xarTpl_pageTitle'],
      'client_id' => 1,
      'add_data' => array(
        'xar_uname' => xarUserGetVar('uname'),
        'xar_uid' => xarUserGetVar('uid')
        )
    )
  );
  return true;
}

?>
