<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Recommend
 */

/* Support for short URLs (user functions)
 *
 *  Return the path for a short URL to xarModURL for this module
 *
 * @author Jo Dalle Nogare
 * @param  $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function recommend_userapi_encode_shorturl($args)
{
    /* Get arguments from argument array */
    extract($args);
    /* Check if we have something to work with */
    if (!isset($func)) {
        return;
    }
    $path = '';
    /* if we want to add some common arguments as URL parameters below */
    $join = '?';

    /* we can't rely on xarModGetName() here -> you must specify the modname ! */
    $module = 'recommend';

    /* specify some short URLs relevant to your module */
    if ($func == 'sendtofriend') {
        if (isset($message)) {
                $path = '/' . $module . '/sendtofriend/1/'.$aid;
        }elseif (isset($aid) && is_numeric($aid)) {
            $path = '/' . $module . '/sendtofriend/'.$aid;
        }else{
           //hmmm..
        }
    }else if ($func == 'main') {
        $path = '/' . $module . '/';
        if (isset($message) && is_numeric($message)) {
            $path = '/' . $module . '/' . $message;
        }
    }else{
    //ooohhh
    }

    return $path;
}

?>
