<?php
/**
* Publish an issue
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
* @author Andrea Moro <andream@andreamoro.net>
*/
/**
 * publish issues for scheduler module
 *
 */
function ebulletin_schedulerapi_publishissues($args=array())
{
    extract($args);
    
    // get issues to be published
    $issues = xarModAPIFunc('ebulletin', 'user', 'getallissues', array('published' => 0));
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

	if (sizeof($issues) == 0) return;

    foreach ($issues as $issue) {
	   // get publication
	   $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $issue['pid']));
	   if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

       // call API function to do the publishing if publishable automatically
	   if ($pub['scheduler'] >= 2) {
          if (!xarModAPIFunc('ebulletin', 'admin', 'send_issue', array('id' => $issue['id']))) return;
	   }
    }

    // success
    return true;
}

?>
