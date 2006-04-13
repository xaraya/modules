<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
    The main user function
    @author Brian McGilligan
    @return Template data
*/
function helpdesk_user_main()
{
    // Security check
    if (!xarSecurityCheck('readhelpdesk')) return;

    if (!xarModGetVar('helpdesk', 'User can Submit') && !xarSecurityCheck('submithelpdesk')) {
        $data['error'] = xarML('Administration has disabled the user interface');
        return $data;
    }

    // Add menu to output
    $data['menu']      = xarModFunc('helpdesk', 'user', 'menu');

    $data['summary']   = xarModFunc('helpdesk', 'user', 'summaryfooter');

    return xarTplModule('helpdesk', 'user', 'main', $data);
}
?>
