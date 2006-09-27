<?php
/**
 * Construct an RSD xml snippet to allow client to discover what we have here
 *  
 * @package modules
 * @subpackage xmlrpcserver
 * @copyright The Digital Development Foundation, 2006
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @author Marcel van der Boom <mrb@hsdev.com>
**/
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
    
    // Determine which APIs we have and what stuff they need for the discovery
    $data['xmlrpc_apis'] = array();
    
    // BloggerAPI
    if(xarModIsAvailable('bloggerapi')) 
    {
        // Get some values we need throughout
        $entrypoint = xarServerGetBaseUrl() . 'ws.php?type=xmlrpc';
        $pubtype    = xarModGetVar('bloggerapi','bloggerpubtype');
        if($pubtype && xarModIsAvailable('categories') && xarModIsAvailable('articles'))
        {
            // Get the root categories for this publication type
            xarLogVariable('rootcats', $rootcats);
            $rootcats = xarModAPIFunc('articles','user','getrootcats',array('ptid'=>$pubtype));
        }
        // The blogId attribute is the blog to which the user can post, in xaraya this
        // means one or more root categories which are linked to the pubtype.
        // The discovery only supports one category, so let's return the first one
        // encountered.
        $data['xmlrpc_apis']['Blogger'] = array(
            'preferred' => 'true',  // NOTE: string!
            'link'      => $entrypoint,
            'others'    => !empty($rootcats) ? 'blogId = "'.$rootcats[0]['catid'].'" ' : ''
        );
    
        // MetaWebLogAPI depends on BloggerAPI so it should be inside the if
        if(xarModIsAvailable('metaweblogapi')) 
        {
            $data['xmlrpc_apis']['MetaWeblog'] = array(
                'preferred'     => 'true',  // NOTE: string!
                'link'          => $entrypoint,
                'others'        => !empty($rootcats) ? 'blogId = "'.$rootcats[0]['catid'].'" ' : ''
            );
            // Metaweblog is now preferred over blogger
            $data['xmlrpc_apis']['Blogger']['preferred'] = 'false';

            // MoveableType depends on MetaWeblogAPI so it should be inside the if
            if(xarModIsAvailable('moveabletype')) 
            {
                $data['xmlrpc_apis']['MoveableType'] = array(
                    'preferred'     =>  'true', // NOTE: string!
                    'link'          =>  $entrypoint,
                    'others'        =>  !empty($rootcats) ? 'blogId = "'.$rootcats[0]['catid'].'" ' : ''
                );
                // Make sure MoveableType ends up as the preferred one
                $data['xmlrpc_apis']['MetaWeblog']['preferred'] = 'false'; // NOTE: string!
                $data['xmlrpc_apis']['Blogger']['preferred']    = 'false'; // NOTE: string!
            } // MoveableType
        } // MetaWebLogApi
    }// BloggerApi
    return $data;
}

?>