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
 * Function to decode state
 */
function publications_userapi_getstatusname( $args )
{
    extract($args);
    $states = xarModAPIFunc('publications','user','getstates');
    if (isset($state) && isset($states[$state])) {
        return $states[$state];
    } else {
        return xarML('Unknown');
    }
}
?>
