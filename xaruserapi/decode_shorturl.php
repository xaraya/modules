<?php
/**
* Decode Short URL's
*
* @package unassigned
* @copyright (C) 2002-2007 The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage highlight
* @link http://xaraya.com/index.php/release/559.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Decode Short URL's
*
* Extract function and arguments from short URLs for this module, and pass
* them back to xarGetRequestInfo().
*
* @author Curtis Farnham <curtis@farnham.com>
* @access  public
* @param   array $params The different elements of the virtual path
* @return  boolean
* @returns array containing func the function to be called and args the query
*          string arguments, or empty if it failed
*/
function highlight_userapi_decode_shorturl($params)
{
    // main function is only function on user side!
    return array('main', array());
}

?>
