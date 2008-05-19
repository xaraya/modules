<?php
/*
 * Process input and return user email for use with sitecontact
 *  @param username str passed in username
 *  @param scid  int the form id (optional)
 */
 
 /* In this example we capture a username passed in as 'custom' var and get the email address from it, 
  *  pass it to the customcontact variable . You can pass in whatever you need, use whatever function to return
  * an email for custom contact.
  * This is a very 'simplified' function and no error checking is done here. Exercise left for those needing this for a specific situation.
  *
  * In this example the user has clicked on a link in an article, or any html page with:
  * a link : xarModULR('sitecontact','user','main',array('custom'=>$username,'scid'=>2)) where 2 is w/e form id you want (optional)
  *
  * Add a hidden field to capture 'custom' field in your user-main-[overridetemplate].xt
  * The custom api func is loaded at each function so we need conditionals to only process at a given point
  */
 $page = xarRequestGetInfo();
 if ($page[2]=='main') { //only check this if we are coming in from the main user function
    //grab our custom field value  - we really only need this in the final api 
    if (!xarVarFetch('custom', 'str:0:', $custom,  '',    XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    //use it to get our email
    if (!empty($custom)) {
        xarSessionSetVar('sitecontactcustom',$custom);
        $customname= $custom;
  $data['custom'] = $custom;
    }
} elseif (isset($customname) && !empty($customname)) { 
   //if we are not in the main user function then just grab the name that has been set if one is set
   $customname = xarSessionGetVar('sitecontactcustom'); 
   $userinfo = xarModAPIFunc('roles','user','get',array('uname'=>$customname));
   //provide some variables so we carry our required field through to the api
   $customcontact =  isset($userinfo['email'])?$userinfo['email']:'';
}
?>