<?php
/**
* Decode Short URL's
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage files
* @link http://xaraya.com/index.php/release/554.html
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
function files_userapi_decode_shorturl($params)
{
    // go to main function if only one param found
    if (count($params) == 1) {
        $args = array();
        return array('main', $args);
    }

    // get module and function
    $module = array_shift($params);
    $func = array_shift($params);

    // "list" is actually the main function
    if ($func == 'list') $func = 'main';

    // initialize args array
    $args = array();

    // handle the path
    if ($func == 'main') {
        if (empty($params)) {
            $params[] = '';
        }
        $args['path'] = '/' . join('/', $params);
    } elseif (!empty($params)) {
        $args['path'] = '/' . join('/', $params);
    }

    return array($func, $args);

}

?>
