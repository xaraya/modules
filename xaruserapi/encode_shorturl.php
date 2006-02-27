<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */
function messages_userapi_encode_shorturl( $args )
{

    $func       = NULL;
    $module     = NULL;
    $mid        = NULL;
    $rest       = array();
    //print_r($args);
    //exit();
    foreach( $args as $name => $value ) {

        switch( $name ) {

            case 'module':
                $module = $value;
                break;
            case 'mid':
                $mid = $value;
                break;
            case 'func':
                $func = $value;
                break;
            default:
                $rest[$name] = $value;

       }
    }

    // kind of a assertion :-))
    if( isset( $module ) && $module != 'messages' ) {
        return;
    }

    /*
     * LETS GO. We start with the module.
     */
    $path = '/messages';

    if ( empty( $func ) )
        return;

    switch ($func) {
        case 'send':
            $path .= '/Outbox';
            break;
        case 'delete':
            $path .= '/Trash';
            break;
        case 'display':
        case 'view':
        case 'main':
        default:
            $path .= '/Inbox';
            if (isset($mid)) {
                $path .= '/' . $mid;
                unset($mid);
            }
            break;
    }

    if (isset($mid)) {
        $rest['mid'] = $mid;
    }

    $add = array();
    foreach ( $rest as $key => $value ) {
        if (isset($rest[$key])) {
            $add[] =  $key . '=' . $value;
        }
    }

    if ( count( $add ) > 0 ) {
        $path = $path . '?' . implode( '&', $add );
    }

    return $path;

}

?>
