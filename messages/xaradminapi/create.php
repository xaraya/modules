<?php

function messages_adminapi_create( $args ) 
{

    if (!xarSecurityCheck( 'AddMessages')) return;

    list ( $itemtype ) = xarVarCleanFromInput( 'itemtype' );
    extract( $args );

    // Retrieve the object via the dynamicdata module api.
    $object = xarModAPIFunc(
        'dynamicdata'
        ,'user'
        ,'getobject'
        ,array(
            'module'     => 'messages'
            ,'itemtype'  => 1
        ));
    if ( empty($object) ) return;

    // check the input values for this object
    $isvalid = $object->checkInput();

    if ( $isvalid ) {

        /*
         * The object is valid . Create it
         */
        $itemid = $object->createItem();
        if (empty( $itemid) ) return; // throw back


        /*
         * call the hook 'item:create:API'
         */
        $args = array(
            'module'        =>  'messages'
            ,'itemid'       =>  $itemid
            ,'itemtype'     =>  '1' );
        $hooks = xarModCallHooks(
            'item'
            ,'create'
            ,$itemid
            ,$args
            ,'messages' );


        $item_title = xarModAPIFunc(
            'messages'
            ,'user'
            ,'gettitle'
            ,array(
                'object'    =>  $object
                ,'itemtype' =>  $itemtype ));

        xarSessionSetVar(
            'messages_statusmsg'
            ,'Created Messages ' . $itemid .' -> '.  $item_title .'.' );

        // This function generated no output, and so now it is complete we redirect
        // the user to an appropriate page for them to carry on their work
        xarResponseRedirect(
            xarModURL(
                'messages'
                ,'admin'
                ,'new'
                ,array(
                    'itemtype' => 1 )));

    } else {

        // Back to new
        return messages_adminapi_new( $args );

    }


}
?>