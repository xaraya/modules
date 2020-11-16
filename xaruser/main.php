<?php
/**
 * Wurfl Module
 *
 * @package modules
 * @subpackage wurfl module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Main user GUI function, entry point
 *
 */

    function wurfl_user_main()
    {
        // Security Check
        if (!xarSecurity::check('ReadWurfl')) {
            return;
        }

        // success
        return array(); //true;
    }
