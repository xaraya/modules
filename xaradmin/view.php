<?php
function messages_admin_view($args) 
{

    list( $itemtype ) = xarVarCleanFromInput('itemtype' );

    switch( $itemtype ) {

        case 1:
            return xarModAPIFunc(
                'messages'
                ,'admin'
                ,'view' );


        default:
            return messages_admin_common('Main Page'); }
}

?>