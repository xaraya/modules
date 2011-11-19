<?php
/**
 * Foo Module
 *
 * @package modules
 * @subpackage foo module
 * @copyright (C) 2011 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Return the options for the user menu
 *
 */

function foo_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('ViewFoo',0)) {
        $menulinks[] = array('url'   => xarModURL('foo',
                                                  'user',
                                                  'main'),
                              'title' => xarML(''),
                              'label' => xarML(''));
    }

    return $menulinks;
}

?>
