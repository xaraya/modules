<?php

/**
 * RSD xml constructor based on the available API's
 *
 * 
 */
function xmlrpcserver_user_rsd($args)
{
    $data = array();
    
    // We need to set the pagename template here to the module
    // xml template
    if(!xarTplSetPageTemplateName('module')) {
        // FIXME: What if module.xt does not exist?
    }
    
    // Determine the Xaraya version
    $data['xar_version'] = XARCORE_VERSION_NUM;
    
    // Determin which APIs we have and what stuff they need for the discovery
    $data['xmlrpc_apis'] = array();
    
    // BloggerAPI
    if(xarModIsAvailable('bloggerapi')) {
        $data['xmlrpc_apis']['Blogger'] = array();
        $data['xmlrpc_apis']['Blogger']['preferred'] = 'true'; // NOTE: string!
        $data['xmlrpc_apis']['Blogger']['link'] = xarServerGetBaseURL() .'ws.php?type=xmlrpc';
        // The blogId attribute is the blog to which the user can post, in xaraya this
        // means one or more root categories which are linked to the pubtype. 
        // The discovery only supports one category, so let's return the first one
        // encountered.
        $data['xmlrpc_apis']['Blogger']['others'] = '';
        $pubtype = xarModGetVar('bloggerapi','bloggerpubtype');
        if($pubtype && xarModIsAvailable('categories') && xarModIsAvailable('articles')) {
            // Get the root categories for this publication type
            $rootcats = xarModAPIFunc('articles','user','getrootcats',array('ptid'=>$pubtype));
            if (!empty($rootcats)) {
                xarLogVariable('rootcats', $rootcats);
                $data['xmlrpc_apis']['Blogger']['others'] = 'blogID = "'.$rootcats[0]['catid'].'" ';
            }
        }
        
    }
    return $data;
}

?>