<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * return an array with coded states
 * @return array
 */
function publications_userapi_getstates()
{
    // Simplistic getstates function
    // Obviously needs to be smarter along with the other state functions
    return array(0 => xarML('Deleted'),
                 1 => xarML('Inactive'),
                 2 => xarML('Draft'),
                 3 => xarML('Active'),
                 4 => xarML('Frontpage'),
                 5 => xarML('Empty')
                 );
}
