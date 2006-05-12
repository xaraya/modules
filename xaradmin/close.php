<?php
/**
 * Polls module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Polls Module
 * @link http://xaraya.com/index.php/release/23.html
 * @author Jim McDonalds, dracos, mikespub et al.
 */
/**
 * close a poll
 */
function polls_admin_close()
{
    // Get parameters
    if (!xarVarFetch('pid', 'id', $pid)) return;
    if (!xarVarFetch('status', 'int:1:3', $status, 1, XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // Pass to API
    if (!xarModAPIFunc('polls',
                     'admin',
                     'close',
                     array('pid' => $pid))) return;

    xarResponseRedirect(xarModURL('polls',
                                  'admin',
                                  'list',
                                  array('status' => $status)));

    return true;
}

?>
