<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 */

sys::import('modules.messages.xarincludes.defines');

function messages_userapi_encode_shorturl( $args )
{

    $func       = NULL;
    $module     = NULL;
    $id        = NULL;
    $rest       = array();
    //print_r($args);
    //exit();
    foreach( $args as $name => $value ) {

        switch( $name ) {

            case 'module':
                $module = $value;
                break;
            case 'id':
                $id = $value;
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
        case 'view':
        case 'display':
        case 'main':
        default:
            $path .= '/Inbox';
            if (isset($id)) {
                $path .= '/' . $id;
                unset($id);
            }
            break;
    }

    if (isset($id)) {
        $rest['id'] = $id;
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
