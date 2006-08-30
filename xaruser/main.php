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
    if( !Security::check(SECURITY_OVERVIEW, 'helpdesk') ){ return false; }

    $data = array();

    return xarTplModule('helpdesk', 'user', 'main', $data);
}
?>
