<?php
 function messages_admin_reply( $args ) 
{

    list( $itemtype, $itemid, $cancel, $authid, $subject, $from_id, $preview ) =
        xarVarCleanFromInput('itemtype', 'itemid', 'cancel', 'authid', 'subject', 'from_id', 'preview' );
    extract( $args );

    /*
     * Return to the itemtype's view page if
     *  -> If the user decided to cancel the action
     *  -> There is no itemid to modify
     *  -> There is no itemtype ( will go to main view )
     */
    if ( !empty( $cancel ) or empty( $itemid ) or empty( $itemtype ) ) {

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        xarResponseRedirect(
            xarModURL(
                'messages'
                ,'admin'
                ,'view'
                ,array(
                    'itemtype' => $itemtype )));

    }

    // check if authid is set.
    if ( isset( $authid ) ) {

        // Confirm the authorization key
        if (!xarSecConfirmAuthKey()) return;

        // Check if a preview is wished
        if ( !isset( $preview ) ) {

            switch( $itemtype ) {

                case 1:
                    return xarModAPIFunc(
                        'messages'
                        ,'admin'
                        ,'update'
                        ,$args );

                default:
                    // TODO // Add statusmessage
                    xarResponseRedirect(
                        xarModURL(
                            'messages'
                            ,'admin'
                            ,'view' ));
            }
        }
    }

    switch( $itemtype ) {

        case 1:
            return xarModAPIFunc(
                'messages'
                ,'admin'
                ,'modify'
                ,$args );

        default:
            // TODO // Add statusmessage
            xarResponseRedirect(
                xarModURL(
                    'messages'
                    ,'admin'
                    ,'view' ));
    }

}

?>