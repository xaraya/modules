<?php

/**
 * File: $Id$
 *
 * Modify administrative configuration
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bloggerapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * modify bloggerapi configuration
 */
function bloggerapi_admin_modifyconfig()
{
    if(!xarSecurityCheck('AdminBloggerAPI')) return;
    
    $states = array();
    // Get the article publication types to link to the bloggerapi
    if (xarModIsAvailable('articles')) {
        $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
        foreach ($pubtypes as $key=>$pubtype) {
            $pubtypes[$key]['ptid']=$key;
        }
        // Get the states an articles can be be in.
        $states = xarModAPIFunc('articles','user','getstates');
        
    } 
    $pubtypes[0]['ptid']=0;
    $pubtypes[0]['name']=xarML('None');
    $pubtypes[0]['descr']=xarML('Not configured');
    $pubtypes[0]['config']='';

    $data['authid']= xarSecGenAuthKey();
    $data['pubtypes'] = $pubtypes;
    $data['states'] = $states;
    $data['metakeywords'] = htmlspecialchars(xarConfigGetVar('metakeywords'));
    $data['metadescription'] = htmlspecialchars(xarConfigGetVar('metadescription'));

    return $data;
}
?>