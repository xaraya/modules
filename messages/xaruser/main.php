<?php
function messages_user_main() {

    if (!xarSecurityCheck('ViewMessages', 0)) {
        return $data['error'] = xarML('You are not permitted to view messages.');
    }

    xarTplSetPageTitle( 'Messages :: Splash Page' );

    // Initialize the statusmessage
    $statusmsg = xarSessionGetVar( 'messages_statusmsg' );
    if ( isset( $statusmsg ) ) {
        xarSessionDelVar( 'messages_statusmsg' );
        $common['statusmsg'] = $statusmsg;
    }

    $common['menu'] = array();
    $common['pagetitle'] = 'Splash Page';

    $data['common'] = $common;

    return $data;
}

?>
