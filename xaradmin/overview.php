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
 * Overview function that displays the standard Overview page
 *
 * This function shows the overview template, currently admin-main.xd.
 * The template contains overview and help texts
 *
 * @author the HelpDesk module development team
 * @return array xarTplModule with $data containing template data
 */
function helpdesk_admin_overview($args)
{
    extract($args);

    if (!xarSecurityCheck('adminhelpdesk')) { return; }

    $data = array();

    return xarTplModule('helpdesk', 'admin', 'main', $data,'main');
}
?>