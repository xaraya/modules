<?php

function messages_userapi_encode_shorturl( $args ) {

    $func       = NULL;
    $module     = NULL;
    $itemid     = NULL;
    $itemtype   = NULL;
    $rest       = array();

    foreach( $args as $name => $value ) {

        switch( $name ) {

            case 'module':
                $module = $value;
                break;

            case 'itemtype':
                $itemtype = $value;
                break;

            case 'objectid':
            case 'itemid':
                $itemid = $value;
                break;

            case 'func':
                $func = $value;
                break;

            default:
                $rest[] = $value;

       }
    }

    // kind of a assertion :-))
    if( isset( $module ) and $module != 'messages' ) {
        return;
    }

    /*
     * LETS GO. We start with the module.
     */
    $path = '/messages';

    if ( empty( $func ) )
        return;

    /*
     * We only provide support for display and view and main
     */
    if ( $func != 'display' and $func != 'view' and $func != 'main' )
        return;

    /*
     * Now add the itemtype if possible
     */
    if ( isset( $itemtype ) ) {

        switch ( $itemtype ) {

            case 1:
                $itemtype_name = 'messages';
                break;


        default:
            // Unknown itemtype?
            return;
        }

        $path = $path . '/' . $itemtype_name;

        /*
         * And last but not least the itemid
         */
        If ( isset( $itemid ) ) {
                $path = $path . '/' . $itemid;
        }
    }

    /*
     * ADD THE REST !!!! THIS HAS TO BE DONE EVERYTIME !!!!!
     */
    $add = array();
    foreach ( $rest as $argument ) {
        if ( isset( $rest['argument'] ) ) {
            $add[] =  $argument . '=' . $rest[$argument];
        }
    }

    if ( count( $add ) > 0 ) {
        $path = $path . '?' . implode( '&', $add );
    }

    return $path;

}

?>