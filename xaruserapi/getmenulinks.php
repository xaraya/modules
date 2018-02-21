<?php
/**
 * Cacher Module
 *
 * @package modules
 * @subpackage cacher
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2014 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Return the options for the user menu
 *
 */

function cacher_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('ViewCacher',0)) {
        $menulinks[] = array('url'   => xarModURL('cacher',
                                                  'user',
                                                  'main'),
                              'title' => xarML(''),
                              'label' => xarML(''));
    }

    return $menulinks;
}

?>
