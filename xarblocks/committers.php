<?php

/**
 * File: $Id$
 *
 * Committers block for bkview module
 *
 * @package modules
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * Initialisation of the committers block
 *
 * The bkview committers block gives info on the 
 * committers in a registered repository.
 *
 * @author  Marcel van der Boom <marcel@xaraya.com>
 * @access  public
 * @return  boolean wether initialisation was succesfull or not
*/
function bkview_committersblock_init()
{
    return true;
}


/**
 * Get information on the committers block
 *
 * @access  public 
 * @return  array  Standardized array with information about the block
*/
function bkview_committersblock_info()
{
    // Return some information on the block
    return array('text_type' => xarML('Committers info'),
                 'module' => 'bkview',
                 'text_type_long' => xarML('Show information about committers in a repository'),
                 'allow_multiple' => true,
                 'form_content' => false,
                 'form_refresh' => true,
                 'show_preview' => true);
}


/**
 * Display block
 *
 */
function bkview_committersblock_display(&$blockinfo)
{
    // Take the block content in serialized form an turn it into something
    // usefull for the template 
    // By default the committers.xd template will be used for displaying.
    // $blockinfo['content'] can contain direct output (tplblock) or tpldata like stuff
    // we take the tpldata approach here
    $data = unserialize($blockinfo['content']);

    // Selectedrepositories contains the id's of the repositories for
    // which this block should display information.
    $repolist = array();
    if(!empty($data['selectedrepositories'])) {
        foreach($data['selectedrepositories'] as $repo_id => $state) {
            $repo = xarModAPIFunc('bkview','user','get',array('repoid' => $repo_id));
            // FIXME: this should be cached, as it can take a long time
            $stats  = xarModAPIFunc('bkview','user','getstats',array('repo' => $repo['repo']));
            $allcsets = array_count_values($stats);
            arsort($allcsets);
            $allcsets = array_slice($allcsets,0,$data['nrofitems']);
            $repolist[$repo_id] = $repo;
            $repolist[$repo_id]['stats'] = $allcsets;
        }
    }
    $data['repolist'] = $repolist;
    // Return data, not rendered content
    // FIXME: using the same 'square' to store the serialized content 
    //        and the tpldata feels wrong
    $blockinfo['content'] = $data;
    return $blockinfo;
}



/**
 * Modify the setting for the committers block
 *
 * @access  public
 * @param   array  standardised array with block information
 * @return  array  template data
*/
function bkview_committersblock_modify($blockinfo)
{
    // By default the modify-committers.xd template will be used 
    // to show the possible settings for the block
    $data = unserialize($blockinfo['content']);
    if(!$data || !array_key_exists('selectedrepositories',$data)) {
        $data['selectedrepositories'] = array();
    }
    if(!$data || !array_key_exists('nrofitems',$data)) {
        $data['nrofitems'] = 5;
    }

    // Get the registered repositories
    $repositories = xarModAPIFunc('bkview','user','getall');
    $data['repositories'] = $repositories;

    return $data;
}

/** 
 * Update the information on the block settings
 *
 * @access  public
 * @param   array standardised array with block information
 * @return  array updated array with block information
*/
function bkview_committersblock_update(&$blockinfo)
{
    $data = array();
    // Get the values for the settings
    if(!xarVarFetch('repositories','array',$repositories,array(),XARVAR_NOT_REQUIRED)) return;
    $data['selectedrepositories'] = $repositories;
    if(!xarVarFetch('nrofitems','int:1:',$nrofitems,5,XARVAR_NOT_REQUIRED)) return;
    $data['nrofitems'] = $nrofitems;

    // Update the block settings
    $blockinfo['content'] = serialize($data);
    return $blockinfo;
}

/**
 * Online help for a block
 */

function bkview_committersblock_help()
{
    // Nothing yet
    return '';
}

?>