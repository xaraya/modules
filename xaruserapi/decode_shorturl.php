<?php
/**
 * Chat Module - Port of PJIRC for Xaraya
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Chat Module
 * @link http://xaraya.com/index.php/release/158.html
 * @author John Cox
 */
/**
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 * @param $params array containing the elements of PATH_INFO
 * @returns array
 * @return array containing func the function to be called and args the query
 *         string arguments, or empty if it failed
 */
function chat_userapi_decode_shorturl($params)
{
    $args = array();
    $module = 'chat';
    return array('main', $args);
    // default : return nothing -> no short URL
    // (e.g. for multiple category selections)
}
?>