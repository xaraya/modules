<?php
/**
 * xarBB Forum Module Event Handler
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarBB Forum Module
 * @link http://xaraya.com/index.php/release/300.html
 * @author crisp <crisp@xaraya.com>
 */

/**
 * xarBB Module event handler for the system event ServerRequest
 *
 * This function is called when the system triggers the
 * event in index.php on each Server Request
 *
 * @author the xarBB module development team
 * @return bool
 */
function xarbb_eventapi_OnServerRequest()
{

  $now = time();

  // this function takes care of lastvisit for the module
  if (xarUserIsLoggedIn()) {
    $lastseen = xarModGetUserVar('xarbb', 'thisvisit');
    $lastvisit = xarModGetUserVar('xarbb', 'lastvisit');
    // how long since we last saw this user?
    $interval = $now-$lastseen;
    $wait = '900'; // in testing I found 15 minutes to be fine, change this if you like though.
    // if the interval is greater than the wait time
    if ($interval > $wait) {
      $lastvisit = $lastseen;
      // set the last visit to last seen time
      xarModSetUserVar('xarbb', 'lastvisit', $lastvisit);
      /*
      // cache current visit starttime?
      xarSessionSetVar('xarbb_visitstart', $now);
      */
    }
    /*
    // cache current visit starttime?
    if (!xarSessionGetVar('xarbb_visitstart')) {
      xarSessionSetVar('xarbb_visitstart', $now);
    }
    */
    // cache lastvisit for the user block and other functions to use
    xarVarSetCached('Blocks.xarbb', 'lastvisit', $lastvisit);
    // this gets logged every page view
    xarModSetUserVar('xarbb', 'thisvisit', $now);
  }
  return true;
}
?>