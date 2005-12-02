<?php
/**
* Support for Short URLs (user functions)
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 *
 * @author the eBulletin module development team
 * @param  $params array containing the different elements of the virtual path
 * @returns array
 * @return array containing func the function to be called and args the query
 *          string arguments, or empty if it failed
 */
function ebulletin_userapi_decode_shorturl($params)
{
    $args = array();

    if (empty($params[1])) {
        return array('main', $args);
    } elseif (!empty($params[1])) {
        return array($params[1], array_slice($params, 2));
    }

}

?>
