<?php
/**
 * File: $Id$
 *
 * Displays an RSS Display.  
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage headlines Module
 * @author RevJim (revjim.net), John Cox
 * @todo Make the admin selectable number of headlines work.
 * @todo show search and image of rss site
*/

/**
 * Block init - holds security.
 */
function headlines_rssblock_init()
{
    return true;
}

/**
 * Block info array
 */
function headlines_rssblock_info()
{
    return array('text_type' => 'RSS',
		 'text_type_long' => 'RSS Newsfeed',
		 'module' => 'headlines',
		 'func_update' => 'headlines_rssblock_insert',
		 'allow_multiple' => true,
		 'form_content' => false,
		 'form_refresh' => false,
		 'show_preview' => true);
}

/**
 * Display func.
 * @param $blockinfo array containing title,content
 */
function headlines_rssblock_display($blockinfo)
{

    // Break out options from our content field
    $vars = unserialize($blockinfo['content']);
    $blockinfo['content'] = '';

    if (empty($blockinfo['bid'])){
        $blockinfo['bid'] = '';
    }

    // Check and see if a feed has been supplied to us.
    if(empty($vars['rssurl'])) {
        $blockinfo['title'] = xarML('Headlines');
        $blockinfo['content'] = xarML('No Feed Url Specified');
        return $blockinfo;
    } else {
        $feedfile = $vars['rssurl'];
    }

    if (!isset($vars['maxitems'])) {
        $vars['maxitems'] = 5;
    }

    // Sanitize the URL provided to us since
    // some people can be very mean.
    $feedfile = preg_replace("/\.\./","donthackthis",$feedfile);
    $feedfile = preg_replace("/^\//","ummmmno",$feedfile);

    // Require the xmlParser class
    require_once('modules/base/xarclass/xmlParser.php');

    // Require the feedParser class
    require_once('modules/base/xarclass/feedParser.php');

    // Get the feed file (from cache or from the remote site)
    $feeddata = xarModAPIFunc('base', 'user', 'getfile',
                              array('url' => $feedfile,
                                    'cached' => true,
                                    'cachedir' => 'cache/rss',
                                    'refresh' => 3600,
                                    'extension' => '.xml'));
    if (!$feeddata) {
        return; // throw back
    }

    // Create a need feedParser object
    $p = new feedParser();

    // Tell feedParser to parse the data
    $info = $p->parseFeed($feeddata);

    // DEBUG INFO  Shows array when uncommented.
    // print_r($info);
    // print_r($blockinfo['bid']);

    if(isset($info['warning'])) {
        $blockinfo['title'] = xarML('Headlines');
        $blockinfo['content'] = xarML('Problem with supplied feed');
        return $blockinfo;
    } else {
        foreach ($info as $content){
             $content = array_slice($content, 0, $vars['maxitems']);
             foreach ($content as $newline){
                    if(is_array($newline)) {
                        if ((isset($newline['description'])) && (!empty($vars['showdescriptions']))){
                            $description = $newline['description'];
                        } else {
                            $description = '';
                        }
                        if (isset($newline['title'])){
                            $title = $newline['title'];
                        } else {
                            $title = '';
                        }
                        if (isset($newline['link'])){
                            $link = $newline['link'];
                        } else {
                            $link = '';
                        }
                    $feedcontent[] = array('title' => $title, 'link' => $link, 'description' => $description);
                }
            }
        }
    }

    if (empty($blockinfo['title'])){
        $blockinfo['title'] = $info['channel']['title'];
    }

    $chantitle  =   $info['channel']['title'];
    $chanlink   =   $info['channel']['link'];
    $chandesc   =   $info['channel']['description'];

    if (empty($blockinfo['template'])) {
        $template = 'rss';
    } else {
        $template = $blockinfo['template'];
    }

    $feed = xarTplBlock('headlines',$template,  array('feedcontent'  => $feedcontent,
                                                      'blockid'      => $blockinfo['bid'],
                                                      'chantitle'    => $chantitle,
                                                      'chanlink'     => $chanlink,
                                                      'chandesc'     => $chandesc));

    $blockinfo['content'] = $feed;
    return $blockinfo;
}

/**
 * Updates the Block config from the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function headlines_rssblock_insert($blockinfo) 
{
    if (!xarVarFetch('rssurl', 'str:1:', $vars['rssurl'], '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxitems', 'int', $vars['maxitems'], 5, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showdescriptions', 'checkbox', $vars['showdescriptions'], false, XARVAR_NOT_REQUIRED)) return;

/*
    list($vars['rssurl'],
         $vars['maxitems'],
         $vars['showimage'],
         $vars['showsearch'],
         $vars['showdescriptions'],
         $vars['altstyle']) = xarVarCleanFromInput('rssurl',
                                                  'maxitems',
                                                  'showimage',
                                                  'showsearch',
                                                  'showdescriptions',
                                                  'altstyle');
*/

    // Define a default block title
    if (empty($blockinfo['title'])) {
        $blockinfo['title'] = xarML('Headlines');
    }
    $blockinfo['content']= serialize($vars);
    return $blockinfo;
}

/**
 * Modify Function to the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function headlines_rssblock_modify($blockinfo)
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Break out options from our content field
    $vars = unserialize($blockinfo['content']);

    // Migrate $row['rssurl'] to content if present
    if (!empty($vars['url'])) {
        $vars['rssurl'] = $vars['url'];
        unset($vars['url']);
    }

    // Get parameters from whatever input we need
    $vars['items'] = array();
    // The user API function is called
    $links = xarModAPIFunc('headlines',
                           'user',
                           'getall');
    $vars['items'] = $links;

    // Defaults
    if (!isset($vars['rssurl'])) {
        $vars['rssurl'] = '';
    }
    if (!ereg("^http://|https://|ftp://", $vars['rssurl'])) {
        $vars['rssurl'] = '';
    }
    if (!isset($vars['showdescriptions'])) {
        $vars['showdescriptions'] = 0;
    }
    if (!isset($vars['maxitems'])) {
        $vars['maxitems'] = 5;
    }

    $vars['blockid'] = $blockinfo['bid'];
    $content = xarTplBlock('headlines','rssAdmin', $vars);

    return $content;
}
?>