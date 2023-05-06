<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */

/**
 * return the path for a short URL to xarController::URL for this module
 *
 * @author the Example module development team
 * @param  $args the function and arguments passed to xarController::URL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function keywords_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);
    // Check if we have something to work with
    if (!isset($func)) {
        return;
    }
    $path = array();
    $get = $args;

    $module = 'keywords';
    $path[] = $module;

    if ($func == 'main') {
        unset($get['func']);
        if (!empty($tab)) {
            $path[] = 'tab'.$tab;
            unset($get['tab']);
        } elseif (!empty($keyword)) {
            $path[] = $keyword;
             unset($get['keyword']);
            if (!empty($id)) {
                $path[] = $id;
                unset($getp['id']);
            }
        }
    } else {

        // anything else that you haven't defined a short URL equivalent for
        // -> don't create a path here
    }
    return array('path'=>$path,'get'=>$get);
}

?>
