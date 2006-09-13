<?php
/**
 * Release module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 * @author jojodee
 */
/**
 * return an array with coded states
 * @return array
 * @TODO move to DD once we have captured all instances in existing code
 */
function release_userapi_getexttypes($args)
{
    extract($args);

    $exttypes = array(0 => xarML('All'),
                      1 => xarML('Module'),
                      2 => xarML('Theme'),
                      3 => xarML('Property'),
                      4 => xarML('Block'),
                      5 => xarML('Function'),
                      6 => xarML('TemplatePack'),
                      7 => xarML('AddOn')
                 );
    return $exttypes;
}
?>