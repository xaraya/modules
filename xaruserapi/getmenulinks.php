<?php
/**
 *
 * Function purpose to be added
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Foo Module
 * @author Marc Lutolf <mfl@netspan.ch>
 *
 * Purpose of file:  to be added
 *
 * @param to be added
 * @return to be added
 *
 */

function foo_userapi_getmenulinks()
{

    if (xarSecurityCheck('ViewFoo',0)) {
        $menulinks[] = array('url'   => xarModURL('foo',
                                                  'user',
                                                  'main'),
                              'title' => xarML(''),
                              'label' => xarML(''));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}

?>
