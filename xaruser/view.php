<?php
/* 
 * Main User View function for site link banners
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarlinkme
 * @link http://xaraya.com/index.php/release/889.html
 * @author jojodee <jojodee@xaraya.com>
 */
/**
 * view a list of banners with link text
  */
function xarlinkme_user_view()
{ 
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
     // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing.
    if (!xarSecurityCheck('ViewxarLinkMe')) return; 

    $data['items'] = array(); 
    // Specify some other variables used in the blocklayout template

    $data['siteurl']   = xarServerGetBaseURL();
    $data['sitename']  = xarModGetVar('themes','SiteName');
    $data['slogan']    = xarModGetVar('themes','SiteSlogan');
    $data['alttxt']    = $data['sitename']."::".$data['slogan'];
    $data['imgpath']   = xarServerGetBaseURL().xarModGetVar('xarlinkme','imagedir');
    $data['linkme1']   = xarModGetVar('themes','sitename')."&nbsp;".xarML(xarModGetVar('xarlinkme','pagetitle'));
    $data['linkme2']   = xarML(xarModGetVar('xarlinkme','instructions'));
    $data['linkme3']   = xarML(xarModGetVar('xarlinkme','instructions2'));
    $data['textlink']  = xarML(xarModGetVar('xarlinkme','txtintro'));
    $data['go']        = xarML(xarModGetVar('xarlinkme','txtadlead'));
    $data['pager'] = ''; 
    $imgdir = xarModGetVar('xarlinkme','imagedir');
    $links[]= array();
    $handle=opendir($imgdir);
    while (($file = readdir($handle))) {
          $filelist[] = $file;
    }
    asort($filelist);
    foreach($filelist as $key => $file) {
       if ($file == "." || $file == ".." || $file == "index.htm"  || $file == "index.html" 
           || $file == "SCCS" || $file == "Thumbs.db"  || $file == "MT" ) {
       } else {
           $links[]=$filelist[$key];
         }

    }
    $data['items'] = $links;

    // We also may want to change the title of the page for a little
    // better search results from the spiders.  All we are doing below
    // Is telling Xaraya what the title of the page should be, and
    // Xaraya controls the rest.
    xarTplSetPageTitle(xarModGetVar('themes','sitename'."::".xarVarPrepForDisplay(xarML('Link to us!'))));
    // Return the template variables defined in this function
    return $data;
}
?>