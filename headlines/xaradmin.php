<?php
/**
 * File: $Id$
 * 
 * Xaraya Headlines
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Headlines Module
 * @author John Cox
*/

function headlines_admin_main()
{
    // Security Check
	if(!xarSecurityCheck('EditHeadlines')) return;

    if (xarModGetVar('adminpanels', 'overview') == 0){
        // Return the output
        return array();
    } else {
        xarResponseRedirect(xarModURL('headlines', 'admin', 'view'));
    }
    // success
    return true;
}


/**
 * add new item
 */
function headlines_admin_new()
{
    
    // Security Check
	if(!xarSecurityCheck('AddHeadlines')) return;
    
    $data['authid'] = xarSecGenAuthKey();

    // Return the output
    return $data;
}

/**
 * This is a standard function that is called with the results of the
 * form supplied by headlines_admin_new() to create a new item
 * @param 'url' the url of the link to be created
 */
function headlines_admin_create($args)
{
    // Get parameters from whatever input we need
    $url = xarVarCleanFromInput('url');

    extract($args);

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // Check arguments
    if (empty($url)) {
        $msg = xarML('No Address for Feed Provided');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

      if (!ereg("^http://|https://|ftp://", $url)) {
        $msg = xarML('Invalid Address for Feed');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
      }

    // The API function is called
    $hid = xarModAPIFunc('headlines',
                         'admin',
                         'create',
                         array('url' => $url));

    if ($hid == false) return;   

    // Lets Create the Cache Right now to save processing later.

    // Require the xmlParser class
    require_once('modules/base/xarclass/xmlParser.php');

    // Require the feedParser class
    require_once('modules/base/xarclass/feedParser.php');

    $feedfile = $url;
    // Create Cache File
    $refresh = (time() - 3600);
    $varDir = xarCoreGetVarDirPath();
    $cacheKey = md5($feedfile);
    $cachedFileName = $varDir . '/cache/rss/' . $cacheKey . '.xml';
    if ((file_exists($cachedFileName)) && (filemtime($cachedFileName) > $refresh)) {
        $fp = fopen($cachedFileName, 'r');
        // Create a need feedParser object
        $p = new feedParser();
        // Read From Our Cache
        $feeddata = fread($fp, filesize($cachedFileName));
        // Tell feedParser to parse the data
        $info = $p->parseFeed($feeddata);
    } else {
        xarLogMessage("Creating RSS feed cache file : $cachedFileName");
        // Create a need feedParser object
        $p = new feedParser();
        
        // Read in our sample feed file
        // FIXME: can we omit the @?
        $feeddata = @implode("",file($feedfile));
        
        // Tell feedParser to parse the data
        $info = $p->parseFeed($feeddata);
        $fp = fopen("$cachedFileName", "wt");
        fwrite($fp, $feeddata);
        fclose($fp);    
    }

    xarResponseRedirect(xarModURL('headlines', 'admin', 'view'));

    // Return
    return true;
}

/**
 * modify an item
 * @param 'hid' the id of the headline to be modified
 */
function headlines_admin_modify($args)
{
    // Get parameters from whatever input we need
    list($hid,
         $obid)= xarVarCleanFromInput('hid',
                                      'obid');

    extract($args);

    if (!empty($obid)) {
        $hid = $obid;
    }

    // Security Check
	if(!xarSecurityCheck('EditHeadlines')) return;

    $link = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));

    if ($link == false) return;

    $link['authid'] = xarSecGenAuthKey();
    return $link;
    
}

/**
 * This is a standard function that is called with the results of the
 * form supplied by headlines_admin_modify() to update a current item
 * @param 'hid' the id of the link to be updated
 * @param 'url' the url of the link to be updated
 */
function headlines_admin_update($args)
{
    // Get parameters from whatever input we need
    list($hid,
         $obid,
         $title,
         $desc,
         $order,
         $url) = xarVarCleanFromInput('hid',
                                      'obid',
                                      'title',
                                      'desc',
                                      'order',
                                      'url');

    extract($args);

    if (!empty($obid)) {
        $hid = $onid;
    }

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    if(!xarModAPIFunc('headlines',
                      'admin',
                      'update',
                      array('hid'   => $hid,
                            'title' => $title,
                            'desc'  => $desc,
                            'url'   => $url,
                            'order' => $order))) return;

    xarResponseRedirect(xarModURL('headlines', 'admin', 'view'));

    // Return
    return true;
}

/**
 * delete item
 * @param 'hid' the id of the item to be deleted
 * @param 'confirmation' confirmation that this item can be deleted
 */
function headlines_admin_delete($args)
{
    // Get parameters from whatever input we need
    list($hid,
         $obid,
         $confirmation) = xarVarCleanFromInput('hid',
                                               'obid',
                                               'confirmation');
    extract($args);

     if (!empty($obid)) {
         $hid = $obid;
     }

    // Security Check
	if(!xarSecurityCheck('DeleteHeadlines')) return;

    // The user API function is called
    $link = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));

    if ($link == false) return; 

    // Check for confirmation.
    if (empty($confirmation)) {
    $link['submitlabel'] = xarML('Submit');
    $link['authid'] = xarSecGenAuthKey();

    return $link;

    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    // The API function is called
    if (!xarModAPIFunc('headlines',
                       'admin',
                       'delete',
                       array('hid' => $hid))) return; 

    xarResponseRedirect(xarModURL('headlines', 'admin', 'view'));

    // Return
    return true;
}

/**
 * view items
 */
function headlines_admin_view()
{

    // Get parameters from whatever input we need
    $startnum = xarVarCleanFromInput('startnum');
    $data['items'] = array();

    // Specify some labels for display
    $data['urllabel'] = xarVarPrepForDisplay(xarML('URL'));
    $data['orderlabel'] = xarVarPrepForDisplay(xarML('Order'));
    $data['warninglabel'] = xarVarPrepForDisplay(xarML('Status'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Options'));
    $data['authid'] = xarSecGenAuthKey();
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('headlines', 'user', 'countitems'),
                                    xarModURL('headlines', 'admin', 'view', array('startnum' => '%%')),
                                    xarModGetVar('headlines', 'itemsperpage'));

    
    // Security Check
	if(!xarSecurityCheck('EditHeadlines')) return;

    // The user API function is called
    $links = xarModAPIFunc('headlines',
                          'user',
                          'getall',
                          array('startnum' => $startnum,
                                'numitems' => xarModGetVar('headlines',
                                                          'itemsperpage')));

    if (empty($links)) {
        $msg = xarML('No Headlines in database, please add a site.', 'headlines');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        if (xarSecurityCheck('EditHeadlines',0)) {
            $links[$i]['editurl'] = xarModURL('headlines',
                                              'admin',
                                              'modify',
                                              array('hid' => $link['hid']));
        } else {
            $links[$i]['editurl'] = '';
        }
        $links[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteHeadlines',0)) {
            $links[$i]['deleteurl'] = xarModURL('headlines',
                                                'admin',
                                                'delete',
                                                array('hid' => $link['hid']));
        } else {
            $links[$i]['deleteurl'] = '';
        }
        $links[$i]['deletetitle'] = xarML('Delete');
    }

    // Add the array of items to the template variables
    $data['items'] = $links;

    // Return the template variables defined in this function
    return $data;
}

/**
 * modify configuration
 */
function headlines_admin_modifyconfig()
{
    // Security Check
	if(!xarSecurityCheck('AdminHeadlines')) return;

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

/**
 * update configuration
 */
function headlines_admin_updateconfig()
{
    $itemsperpage = xarVarCleanFromInput('itemsperpage');

    if (!xarSecConfirmAuthKey()) return;

    // Security Check
	if(!xarSecurityCheck('AdminHeadlines')) return;

    if (!isset($itemsperpage)) {
        $itemsperpage = 20;
    }

    xarModSetVar('headlines', 'itemsperpage', $itemsperpage);

    xarResponseRedirect(xarModURL('headlines', 'admin', 'modifyconfig'));

    return true;
}


function headlines_admin_import()
{
    // Security Check
    if(!xarSecurityCheck('EditHeadlines')) return;

    // Get parameters from whatever input we need
    $hid = xarVarCleanFromInput('hid');

    // Require the xmlParser class
    require_once('modules/base/xarclass/xmlParser.php');

    // Require the feedParser class
    require_once('modules/base/xarclass/feedParser.php');

    // The user API function is called
    $links = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));


    // Check and see if a feed has been supplied to us.
    if(isset($links['url'])) {
        $feedfile = $links['url'];
    } else {
        $feedfile = "";
    }

    // Sanitize the URL provided to us since
    // some people can be very mean.
    $feedfile = preg_replace("/\.\./","donthackthis",$feedfile);
    $feedfile = preg_replace("/^\//","ummmmno",$feedfile);

    // Create Cache File
    $refresh = (time() - 3600);
    $varDir = xarCoreGetVarDirPath();
    $cacheKey = md5($feedfile);
    $cachedFileName = $varDir . '/cache/rss/' . $cacheKey . '.xml';
    if ((file_exists($cachedFileName)) && (filemtime($cachedFileName) > $refresh)) {
        $fp = @fopen($cachedFileName, 'r');
        // Create a need feedParser object
        $p = new feedParser();
        // Read From Our Cache
        $feeddata = fread($fp, filesize($cachedFileName));
        // Tell feedParser to parse the data
        $info = $p->parseFeed($feeddata);
    } else {
        // Create a need feedParser object
        $p = new feedParser();

        // Read in our sample feed file
        $feeddata = @implode("",@file($feedfile));

        // Tell feedParser to parse the data
        $info = $p->parseFeed($feeddata);
        $fp = fopen("$cachedFileName", "wt");
        fwrite($fp, $feeddata);
        fclose($fp);    
    }

    if (empty($info['warning'])){
        foreach ($info as $content){
             foreach ($content as $newline){
                    if(is_array($newline)) {
                        if (isset($newline['description'])){
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

                        $imports[] = array('title' => $title, 'link' => $link, 'description' => $description);
                }
            }
        }

        if (!empty($links['title'])){
            $data['chantitle'] = $links['title'];
        } else {
            $data['chantitle']  =   $info['channel']['title'];
        }
        if (!empty($links['desc'])){
            $data['chandesc'] = $links['desc'];
        } else {
            $data['chandesc']   =   $info['channel']['description'];
        }
        $data['chanlink']   =   $info['channel']['link'];

    } else {
        $msg = xarML('There is a problem with a feed.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    foreach ($imports as $import){

        $article['title'] = $import['title'];
        $article['summary'] = $import['description'];
        $article['summary'] .= '<br /><br />';
        $article['summary'] .= xarML('Source');
        $article['summary'] .= ': <a href="';
        $article['summary'] .= $import['link'];
        $article['summary'] .= '">';
        $article['summary'] .= $info['channel']['title'];
        $article['summary'] .= '</a>';
        $article['aid'] = 0;
        xarModAPIFunc('articles', 'admin', 'create', $article);
    }
    
    xarResponseRedirect(xarModURL('articles', 'user', 'view'));
}
?>