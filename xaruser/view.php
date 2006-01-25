<?php
/**
* Display GUI for config modification
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * View publications
 */
function ebulletin_user_view()
{
    // security check
    if (!xarSecurityCheck('VieweBulletin')) return;

    // get publications
    $pubs = xarModAPIFunc('ebulletin', 'user', 'getall', array('public' => true));
    if (empty($pub) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // initialize template array
    $data = xarModAPIFunc('ebulletin', 'user', 'menu', array('tab' => 'archive'));

    // add template vars
    $data['pubs'] = $pubs;

    return $data;

}

?>
