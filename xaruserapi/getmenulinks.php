<?php
/**
 * EAV Module
 *
 * @package modules
 * @subpackage eav
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2013 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Return the options for the user menu
 *
 */

function eav_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurity::check('ViewEAV', 0)) {
        $menulinks[] = array('url'   => xarController::URL(
            'eav',
            'user',
            'main'
        ),
                              'title' => xarML(''),
                              'label' => xarML(''));
    }

    return $menulinks;
}
