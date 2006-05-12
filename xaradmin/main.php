<?php
/**
 * Polls Module main administration function
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
 * Redirect to list function
 * @return bool true on success of redirect
 */
function polls_admin_main()
{
    // Security check
    if(!xarSecurityCheck('AdminPolls')) return;
    // redirect
    xarResponseRedirect(xarModURL('polls', 'admin', 'list'));
    // success
    return true;
}

?>
