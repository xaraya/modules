<?php

/**
 * Update configuration
 */
function pubsub_admin_updateconfig()
{ 
    // Get parameters
    xarVarFetch('settings','isset',$settings,'', XARVAR_DONT_SET);
    xarVarFetch('isalias','isset',$isalias,'', XARVAR_DONT_SET);
    xarVarFetch('numitems','isset',$numitems,20, XARVAR_DONT_SET);
    xarVarFetch('subjecttitle','isset',$subjecttitle,'', XARVAR_DONT_SET);
    xarVarFetch('includechildren','isset',$includechildren,'', XARVAR_DONT_SET);
    xarVarFetch('allindigest','isset',$allindigest,'', XARVAR_DONT_SET);
    xarVarFetch('wrapper','isset',$wrapper,'', XARVAR_DONT_SET);

    // Security Check
    if (!xarSecurityCheck('AdminPubSub')) return; 

    if (isset($settings) && is_array($settings)) {
        foreach ($settings as $name => $value) {
            xarModSetVar('pubsub', $name, $value);
        } 
    } 
    if (isset($wrapper)) {
        xarModSetVar('pubsub','wrapper',$wrapper);
    } else {
        xarModSetVar('pubsub','wrapper',0);
    }
    if (empty($isalias)) {
        xarModSetVar('pubsub','SupportShortURLs',0);
    } else {
        xarModSetVar('pubsub','SupportShortURLs',1);
    }
    if (empty($numitems) || !is_numeric($numitems)) {
        xarModSetVar('pubsub','itemsperpage',20);
    } else {
        xarModSetVar('pubsub','itemsperpage',$numitems);
    }
    if (empty($subjecttitle)) {
        xarModSetVar('pubsub','subjecttitle',0);
    } else {
        xarModSetVar('pubsub','subjecttitle',1);
    }
    if (empty($includechildren)) {
        xarModSetVar('pubsub','includechildren',0);
    } else {
        xarModSetVar('pubsub','includechildren',1);
    }
    if (empty($allindigest)) {
        xarModSetVar('pubsub','allindigest',0);
    } else {
        xarModSetVar('pubsub','allindigest',1);
    }


    if (xarModIsAvailable('scheduler')) {
        if (!xarVarFetch('interval', 'str:1', $interval, '', XARVAR_NOT_REQUIRED)) return;
        // see if we have a scheduler job running to process the pubsub queue
        $job = xarModAPIFunc('scheduler','user','get',
                             array('module' => 'pubsub',
                                   'type' => 'admin',
                                   'func' => 'processq'));
        if (empty($job) || empty($job['interval'])) {
            if (!empty($interval)) {
                // create a scheduler job
                xarModAPIFunc('scheduler','admin','create',
                              array('module' => 'pubsub',
                                    'type' => 'admin',
                                    'func' => 'processq',
                                    'interval' => $interval));
            }
        } elseif (empty($interval)) {
            // delete the scheduler job
            xarModAPIFunc('scheduler','admin','delete',
                          array('module' => 'pubsub',
                                'type' => 'admin',
                                'func' => 'processq'));
        } elseif ($interval != $job['interval']) {
            // update the scheduler job
            xarModAPIFunc('scheduler','admin','update',
                          array('module' => 'pubsub',
                                'type' => 'admin',
                                'func' => 'processq',
                                'interval' => $interval));
        }
    }

    xarResponseRedirect(xarModURL('pubsub', 'admin', 'modifyconfig'));

    return true;
}

?>
