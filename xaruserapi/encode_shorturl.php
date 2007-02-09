<?php
/**
* Encode Short URL's
*
* @package modules
* @copyright (C) 2002-2007 The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage highlight
* @link http://xaraya.com/index.php/release/559.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Encode Short URL's
*
* Take input from xarModURL() and create a filesystem-like path for it.
*
* @author Curtis Farnham <curtis@farnham.com>
* @access  public
* @param  $args the function and arguments passed to xarModURL
* @return string path to be added to index.php for a short URL, or empty if failed
*/
function highlight_userapi_encode_shorturl($args)
{
    extract($args);

    // set defaults
    $module = 'highlight';

    // main function is only function on user side!!!
    $path = "/$module/";

    return $path;
}

?>
