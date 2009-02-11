<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * return an array with coded states
 * @return array
 */
function publications_userapi_getstates()
{
    // Simplistic getstates function
    // Obviously needs to be smarter along with the other state functions
    return array(0 => xarML('Submitted'),
                 1 => xarML('Rejected'),
                 2 => xarML('Approved'),
                 3 => xarML('Frontpage'),
           //    4 => xarML('Unknown')
                 );
}
?>
