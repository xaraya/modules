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
 * Modify an Newsletter story
 *
 * @public
 * @author Richard Cave
 * @param 'id' the id of the story to be modified
 * @param 'publicationId' publication id of the issue the story is in
 * @param 'articleid' id of an article used in place of a story
 * @returns array
 * @return $templateVarArray
 */
function newsletter_admin_modifystory($args=array()) 
{
    // set a default value for form error messages
    // this will get overwritten w/ the extract call if formErrorMsg was passed in args
    $formErrorMsg=array();
    
    $story=array();
    
    // get the arguments passed to us
    extract($args);
    
    // Security check
    if(!xarSecurityCheck('EditNewsletter')) return;
    
    // Get input parameters
    if (!xarVarFetch('id', 'id', $id)) return;
    if (!xarVarFetch('publicationId', 'int:0:', $publicationId, 0)) return;
    
    if (empty($story)){
        // call the user API function to retrieve all the content
        // for the story with the matching id ($id)
        $story = xarModAPIFunc('newsletter',
                               'user',
                               'getstory',
                               array('id' => $id));      
    }
    else{
        // call the user API function to retrieve all the content
        // for the story with the matching id ($id)
        $_storyDB = xarModAPIFunc('newsletter',
                               'user',
                               'getstory',
                               array('id' => $id));   
                               
        // put the story we just got in the form into the proper story format
        // set the data from the form over the db pull
        $_storyDB['articleid']=$story['articleid'];
        $_storyDB['title']=$story['title'];
        $_storyDB['source']=$story['source'];
        $_storyDB['altDate']=$story['altDate'];
        $_storyDB['content']=$story['content'];
        $_storyDB['fullTextLink']=$story['fullTextLink'];
        $_storyDB['commentary']=$story['commentary'];
        $_storyDB['commentarySource']=$story['commentarySource'];
        
        // now make them the same for all the shared code below
        $story = $_storyDB;

    }
       
    // set what we'll return to be an array and populate it depending on 
    // if we use articles or stories
    $templateVarArray = array();
     
    // default is to not use articles
    $templateVarArray['canUseArticles']=false;
    
    
    // only do this step if they have the articles module loaded up
    if (xarModIsAvailable('articles')){
        // get input parameters.  These allow the user
        // to use an article in place of a story.  The first three (pubtypeid, catfilter, status) reduce
        // the number of articles shown to the user to choose from.  ie article must be of this pubtype,
        // in this category and of this status.
        
        // we may not have gotten the article vars from the form, they may need to be pulled from the
        // database.  the previous xarModAPIFunc('newsletter','user','getstory',) call has what we need.  Do this 
        // in the fourth xarVarFetch argument which will make the db value default if the form is empty.
        xarVarFetch('articleid', 'int:0:', $vars['articleid'], $story['articleid'], XARVAR_NOT_REQUIRED);
        
        $templateVarArray['canUseArticles']=true;
    }
    
    // get an array of all the publication from the database. we'll to put in the drop down
    // to choose a publication for the story to use.
    $story['publications'] = xarModAPIFunc('newsletter',
                                           'user',
                                           'get',
                                            array('phase' => 'publication',
                                                  'sortby' => 'title'));
    
    // Check for exceptions from the xarMadAPIFunc call
    if (!isset($story['publications']) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back
                           
                                                     
    // Check for exceptions
    if (!isset($story) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back


    // Find the publication based on story category
    if ($publicationId != 0) {
        // Get the chosen publication
        $pubItem = xarModAPIFunc('newsletter',
                                 'user',
                                 'getpublication',
                                 array('id' => $publicationId));

        // Check for exceptions
        if (!isset($pubItem) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
            return; // throw back
    } else {
        // Check if the story has a publication already
        if ($story['pid'] != 0) {
            $publicationId = $story['pid'];

            // Get the chosen publication
            $pubItem = xarModAPIFunc('newsletter',
                                     'user',
                                     'getpublication',
                                     array('id' => $publicationId));

            // Check for exceptions
            if (!isset($pubItem) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
                return; // throw back

        } elseif ($story['cid'] != 0) {
            // Get the parent category of the story
            $storyCategory =  xarM0dAPIFunc('categories',
                                            'user',
                                            'getcatinfo',
                                             Array('cid' => $story['cid']));

            // Get parent publication
            $pubItem =  xarModAPIFunc('categories',
                                      'user',
                                      'getcatinfo',
                                      Array('cid' => $storyCategory['parent']));

            // Set publication
            foreach ($story['publications'] as $storyPub) {
                if ($storyPub['cid'] == $pubItem['cid']) {
                    $publicationId = $storyPub['id'];
                    break;
                }
            }
        } else {
            // No category found so set to top level
            $pubItem['cid'] = xarModGetVar('newsletter', 'mastercid');
            $publicationId = 0;
        }
    }

    // Set some more variables
    $story['publicationId'] = $publicationId;
    $story['categories'] = array();
    $story['number_of_categories'] = xarModGetVar('newsletter', 'number_of_categories');
    if ($publicationId != 0) {

        // Only show categories for publication
        $categories = xarModAPIFunc('newsletter',
                                     'user',
                                     'getchildcategories',
                                     array('parentcid' => $pubItem['cid'],
                                           'numcats' => $story['number_of_categories']));
        
        if ($categories)
            $story['categories'] = $categories;
    
    } else {
        // Get the child categories below the master category
        $categories = xarModAPIFunc('newsletter',
                                     'user',
                                     'getchildcategories',
                                     array('parentcid' => $pubItem['cid'],
                                           'numcats' => $story['number_of_categories']));

        // Get the grandchild categories below the master category
        foreach ($categories as $category) {
            $grandchildren = xarModAPIFunc('newsletter',
                                     'user',
                                     'appendchildcategories',
                                     array('parentcid' => $category['cid'],
                                           'numcats' => $story['number_of_categories']));

            // Merge the category arrays
            $story['categories'] = array_merge($story['categories'], $grandchildren);
        }
    }

    // Make sure some categories were returned.  If there were no
    // categories created under this publication category, then the
    // stories will not display.
    if (empty($story['categories'])) {
        $msg = xarML('No story categories were found for this publication.  Please create these categories before continuing.');
        xarErrorSet(XAR_USER_EXCEPTION, 'FORBIDDEN_OPERATION', new DefaultUserException($msg));
        return;
    }

    // Get the list of owners
    $story['owners'] = xarModAPIFunc('newsletter',
                                       'user',
                                       'get',
                                       array('phase' => 'owner'));

    // Check for exceptions
    if (!isset($story['owners']) && xarCurrentErrorType() != XAR_NO_EXCEPTION) 
        return; // throw back

    // Get the list of commentary sources from module var
    $commentarySourceArray = xarModGetVar('newsletter', 'commentarysource');
    if (!empty($commentarySourceArray)) {
        if (!is_array($commentarySourceArray = @unserialize($commentarySourceArray))) {
            $commentarySourceArray = array();
        }
    } else {
        $commentarySourceArray = array();
    }

    // Check if publication is in commentary source array
    if (array_key_exists($publicationId, $commentarySourceArray)) {
        $story['commentarySourceArray'] = $commentarySourceArray[$publicationId];
    } else {
        $story['commentarySourceArray'] = array();
    }

    // Set hook variables
    $story['module'] = 'newsletter';
    $hooks = xarModCallHooks('story','modify',$id,$story);
    if (empty($hooks) || !is_string($hooks)) {
        $hooks = '';
    }

    // Get the admin menu
    $menu = xarModAPIFunc('newsletter', 'admin', 'menu');


    if (isset($vars['articleid']) && $vars['articleid']!=0){
        $templateVarArray['articleid']=$vars['articleid'];
        
        // get all the articles based on the users filter set (article_args)
        $_articlearray = xarModAPIFunc(
            'articles', 'user', 'get', array("aid"=>$templateVarArray['articleid'] ));
        // truncate the article title and put it back
        $templateVarArray['articletitle']=substr($_articlearray['title'],0,50);
    }

    // if we were passed an auth id from updatestory, use it instead
    $templateVarArray['authid'] = xarSecGenAuthKey();
    if  (!empty($authid)){
       $templateVarArray['authid']= $authid;
    }

    // Set template array with all story based info
    $templateVarArray['updatebutton'] = xarVarPrepForDisplay(xarML('Update Story'));
    $templateVarArray['menu'] = $menu;
    $templateVarArray['hooks'] = $hooks;
    $templateVarArray['story'] = $story;
    $templateVarArray['formErrorMsg'] = $formErrorMsg;

    // Return the template variables defined in this function
    return $templateVarArray;
}

?>
