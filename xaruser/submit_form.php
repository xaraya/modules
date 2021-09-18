<?php

sys::import('modules.dynamicdata.class.objects.master');
function pubsub_user_submit_form($args)
{
    extract($args);
    if (!xarVar::fetch('name', 'str', $name, 'pubsub_subscriptions', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('userid', 'int', $userid, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('event_id', 'int', $event_id, 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('email', 'email', $email, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('returnurl', 'str', $returnurl, false)) {
        return;
    }

    // Set some default values
    $default_values = [
        'event'   => $event_id,
        'user_id' => $userid,
        'email'   => $email,
    ];

    if (!empty($email)) {
        //check if email already available
        sys::import('xaraya.structures.query');
        $tables =& xarDB::getTables();
        $q = new Query();
        $q->addtable($tables['pubsub_subscriptions'], 'ps');
        $q->eq('ps.email', $default_values['email']);
        $q->eq('ps.state', 3);
        $q->run();
        $result  = $q->output();

        if (empty($result)) {
            $data['object'] = DataObjectMaster::getObject(['name' => $name]);
            $data['object']->setFieldValues($default_values, 1);

            // Good data: create the item
            $itemid = $data['object']->createItem();

            //send to notify_new_user
            xarMod::apiFunc('pubsub', 'user', 'notify_new_user', $default_values['email']);

            // If this is an AJAX call, end here
            xarController::$request->exitAjax();

            // Jump to the next page
            xarController::redirect(xarServer::getCurrentURL());
        } else {
            //xarML('This email is already registered!');
            //error_reporting(0);
            die('This email is already registered!');
        }
    }
    return true;
}
