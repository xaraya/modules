<?php

/**
 * Add Subscriber
 */
function pubsub_admin_addsubscriber()
{ 
    // Get parameters
    xarVarFetch('sub_module','isset',$sub_module,'', XARVAR_DONT_SET);
    xarVarFetch('sub_itemtype','isset',$sub_itemtype,'', XARVAR_DONT_SET);
    xarVarFetch('sub_category','isset',$sub_category,'', XARVAR_DONT_SET);
    xarVarFetch('sub_email','isset',$sub_email,'', XARVAR_DONT_SET);


    // Confirm authorisation code
//    if (!xarSecConfirmAuthKey()) return; 
    // Security Check
    if (!xarSecurityCheck('AdminPubSub')) return; 

    $sub_args = array();
    $sub_args['modid']    = $sub_module;
    $sub_args['cid']      = $sub_category;
    $sub_args['itemtype'] = $sub_itemtype;
	
	if( strstr($sub_email,"\n") )
	{
		$emails = explode("\n",$sub_email);
		foreach( $emails as $email )
		{
			$sub_args['email']    = $email;
			xarModAPIFunc('pubsub','user','subscribe', $sub_args);
		}
	} else {
	    $sub_args['email']    = $sub_email;
	    xarModAPIFunc('pubsub','user','subscribe', $sub_args);
	}
	

    xarResponseRedirect(xarModURL('pubsub', 'admin', 'viewall'));

    return true;
}

?>
