<?php
/**
 * Foo Module
 *
 * @package modules
 * @subpackage foo module
 * @copyright (C) 2010 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Main user GUI function, entry point
 *
 */

    function foo_user_main()
    {
        // Security Check
        if (!xarSecurityCheck('ReadFoo')) return;

//        xarResponse::redirect(xarModURL('foo', 'user', 'view'));
        // success
        return array(); //true;
    }

?>
