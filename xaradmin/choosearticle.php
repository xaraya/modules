<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Choose an article to use as a Newsletter story
 *
 * @public
 * @author Ashley Jones 
 * @param 'publicationId' the publication id of the story
 * @param 'issueId' the issue id of the story
 * @param 'pubtypeid'
 * @param 'catfilter'
 * @param 'status'
 * @param 'articleid'
 * @param 'id'
 * @param 'notnew'
 * @returns array
 * @return $data
 */
function newsletter_admin_choosearticle()
{
    // Security check
//    if(!xarSecurityCheck('AddNewsletter')) return;


    xarVarFetch('publicationId', 'int:0:', $data['publicationId'], NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('issueId', 'int:0:', $data['issueId'], NULL, XARVAR_NOT_REQUIRED);
    

    // get input parameters.  These allow the user
    // to use an article in place of a story.  The first three (pubtypeid, catfilter, status) reduce
    // the number of articles shown to the user to choose from.  ie article must be of this pubtype,
    // in this category and of this status.
    
    // we may not have gotten the article vars from the form, they may need to be pulled from the
    // database.  the previous xarModAPIFunc('newsletter','user','getstory',) call has what we need.  Do this 
    // in the fourth xarVarFetch argument which will make the db value default if the form is empty.
    xarVarFetch('pubtypeid', 'int:0:', $vars['pubtypeid'], NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('catfilter', 'int:0:', $vars['catfilter'], NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('status', 'int:0:', $vars['status'], NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('articleid', 'int', $vars['articleid'], NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('id', 'id', $data['id'], NULL, XARVAR_NOT_REQUIRED);
  
    
    xarVarFetch('notNew', 'string', $data['notNew'], NULL, XARVAR_NOT_REQUIRED);
      


    $data['canUseArticles']=true;



    // set the defaults of the variables for the first time this page is run.
    // this scenario covers when there was nothing pulled from the 
    // database and nothing pulled from the form
    if (empty($vars['pubtypeid'])) {$vars['pubtypeid'] = '0';}
    if (empty($vars['catfilter'])) {$vars['catfilter'] = '0';}
    if (empty($vars['status'])) {$vars['status'] = '0';}
    if (empty($vars['itemlimit'])) {$vars['itemlimit'] = 0;}
    if (empty($vars['toptype'])) {$vars['toptype'] = 'date';}
    if (empty($vars['articleid'])) {$vars['articleid'] = 0;}
    

    // choose which fields for each article we want to retrieve
    $vars['fields'] = array('aid', 'title');

    // set status array to be vars sub status, and check if it's an array.
    $statusarray = $vars['status'];
    if (!is_array($vars['status'])) {
        $statusarray = array($vars['status']);
        // if it's set to 0, then they want all the articles, set the article filter status array to be
        // equal to both front page and approved IDs
        if ($vars['status']==0){
            $statusarray=array(2,3);
        }
        
    } 
    
    // set cids array to be vars sub catfilter, and check if it's an empty.
    $cidsarray = array();
    if(!empty($vars['catfilter'])) {
        $cidsarray = array($vars['catfilter']);
    } 

    // Create array based on modifications
    $article_args = array();

    // Only include pubtype if a specific pubtype is selected
    if (!empty($vars['pubtypeid'])) {
        $article_args['ptid'] = $vars['pubtypeid'];
    }

    // If itemlimit is set to 0, then don't pass to getall
    if ($vars['itemlimit'] != 0 ) {
        $article_args['numitems'] = $vars['itemlimit'];
    }
    
    // Add the rest of the arguments into article_args so that when we call the modAPIFunc
    // below, we only get the articles the user wants
    $article_args['cids'] = $cidsarray;
    $article_args['enddate'] = time();
    $article_args['status'] = $statusarray;
    $article_args['fields'] = $vars['fields'];
    $article_args['sort'] = $vars['toptype'];
         
    // get all the articles based on the users filter set (article_args)
    $vars['filtereditems'] = xarModAPIFunc(
        'articles', 'user', 'getall', $article_args );

    // Check for exceptions from our API call
    if (!isset($vars['filtereditems']) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Try to keep the additional headlines select list width less than 50 characters
    for ($idx = 0; $idx < count($vars['filtereditems']); $idx++) {
        if (strlen($vars['filtereditems'][$idx]['title']) > 50) {
            $vars['filtereditems'][$idx]['title'] = substr($vars['filtereditems'][$idx]['title'], 0, 47) . '...';
        }
    }

    // get all the pubtypes, categories and status options so that we can have a drop
    // down for the user to choose from all the available articles filter options
    $vars['pubtypes'] = xarModAPIFunc('articles', 'user', 'getpubtypes');
    $vars['categorylist'] = xarModAPIFunc('categories', 'user', 'getcat');
    $vars['statusoptions'] = array(
        array('id' => '0', 'name' => xarML('All Published')),
        array('id' => '3', 'name' => xarML('Frontpage')),
        array('id' => '2', 'name' => xarML('Approved'))
    );
    
    // push all the variables we've just set into the teplate by 
    // populating the array
    $data['categorylist'] =$vars['categorylist'];
    $data['catfilter'] =$vars['catfilter'];
    $data['articleid'] =$vars['articleid'];
    $data['filtereditems'] =$vars['filtereditems'];
    $data['statusoptions'] =$vars['statusoptions'];
    $data['pubtypes'] = $vars['pubtypes'];
    $data['status'] =$vars['status'];
    $data['pubtypeid'] =$vars['pubtypeid'];


    
   

    // Generate a one-time authorisation code for this operation
//    $data['authid'] = xarSecGenAuthKey();

    echo xarTplModule('newsletter','admin','choosearticle', $data,NULL);
    exit();
    
    // Return the template variables defined in this function
//    return $data;
}

?>
