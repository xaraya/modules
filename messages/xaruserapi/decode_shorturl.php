<?php

function mymessages_userapi_decode_shorturl( $params ) {


    if ( $params[0] != 'messages' )
        return;

    /*
     * Check for the itemtype
     */
    if ( empty( $params[1] ) )
        return array( 'main', array() );

    switch ( $params[1] ) {

        case 'messages':
            $itemtype = 1;
            break;


        default:
            return array( 'main', array() );
    }

    if ( !isset( $params[2] ) )
        return array(
            'view'
            ,array(
                'itemtype' => $itemtype ));

    return array(
        'display'
        ,array(
            'itemid'    => $params[2]
            ,'itemtype' => $itemtype ));

}
?>