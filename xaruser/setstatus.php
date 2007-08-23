<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Johnny Robeson <johnny@localmomentum.net>
 */


/**
 * Set a specified status for a comment and it's children (optional)
 *
 * @author Johnny Robeson (johnny@localmomentum.net)
 * @access public
 * @param  int     $cid     id of the comment to lookup
 * @param  int     $action  the status to see (@see xarincludes/defines.php)
 * @param  bool    $children whether status should be set on the comment children as well
 * @return bool        returns true on success, throws an exception and returns false otherwise
 * @todo   implement hidden/off comment status/support
 */
function comments_user_setstatus($args)
{
    if (!xarSecurityCheck('Comments-Moderator')) return;

    $header  = xarRequestGetVar('header');
    $receipt = xarRequestGetVar('receipt');

    if (empty($header['cid'])) {
        $msg = xarML("Missing or Invalid parameter header['cid']");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (empty($receipt['action'])) {
        $msg = xarML("Missing or Invalid parameter receipt['action']");
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if (!isset($children)) $children = true;

    switch ($receipt['action']) {
        case 'lock':
            $status = _COM_STATUS_LOCKED;
            break;
        case 'hide':
            $status = _COM_STATUS_OFF;
            break;
        case 'unlock':
        case 'show':
            $status = _COM_STATUS_ON;
            break;
    }

    xarModAPIFunc('comments','user','setstatus',
            array('cid' => $header['cid'],
                  'status' => $status,
                  'children' => $children));
    xarResponseRedirect($receipt['returnurl']['decoded']);
}
?>
