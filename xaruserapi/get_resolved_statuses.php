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
function helpdesk_userapi_get_resolved_statuses($args)
{
    extract($args);
    $statuses = unserialize(xarModGetVar('helpdesk', 'resolved_statuses'));

    return $statuses;
}
?>