<?php 
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------

/**
 * the main administration function
 */
function pubsub_admin_main()
{
    // Security check
    if (!xarSecAuthAction(0, 'Pubsub::', '::', ACCESS_EDIT)) {
        $msg = xarML('Not authorized to access to #(1)',
                    'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    $data = pubsub_admin_menu();
    
    // Specify some other variables used in the blocklayout template
    $data['welcome'] = xarML('Welcome to the administration part of this Pubsub module.');

    // Return the template variables defined in this function
    return $data;
}
function pubsub_admin_view()
{
    $data = pubsub_admin_menu();
    $data['items'] = array();
    $data['namelabel'] = xarVarPrepForDisplay(xarMLByKey('PUBSUBNAME'));
    $data['pager'] = '';

    if (!xarSecAuthAction(0, 'Pubsub::', '::', ACCESS_EDIT)) {
        $msg = xarML('Not authorized to access to #(1)',
                    'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }
    
}

?>
