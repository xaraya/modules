<?php
/**
 * Reminders Module
 *
 * @package modules
 * @subpackage reminders
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Return the options for the user menu
 *
 */

function reminders_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('ViewReminders', 0)) {
        $menulinks[] = array('url'   => xarModURL(
            'reminders',
            'user',
            'main'
        ),
                              'title' => xarML(''),
                              'label' => xarML(''));
    }

    return $menulinks;
}
