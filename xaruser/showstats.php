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
    Displays stats for the Helpdesk
    TODO: This whole function needs to be programmed

    @author Brian McGilligan
    @return Template data
*/
function helpdesk_user_showstats()
{
    $data['menu']      = xarModFunc('helpdesk', 'user', 'menu');

    $data['summary']   = xarModFunc('helpdesk', 'user', 'summaryfooter');

    return xarTplModule('helpdesk', 'user', 'showstats', $data);
}
?>
