<?php
/**
 * Realms Module
 *
 * @package modules
 * @subpackage realms module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * Return the options for the user menu
 *
 */

function realms_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurity::check('ViewRealms', 0)) {
        $menulinks[] = array('url'   => xarController::URL(
            'realms',
            'user',
            'main'
        ),
                              'title' => xarML(''),
                              'label' => xarML(''));
    }

    return $menulinks;
}
