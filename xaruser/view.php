<?php

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
    $data['sitename']  = xarModGetVar('themes','sitename');
    $data['slogan']    = xarModGetVar('themes','siteslogan');
    $data['alttxt']    = $data['sitename'].((empty($data['sitename'])) or (empty($data['slogan'])))."::".$data['slogan'];
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
           || $file == "SCCS" || $file == "Thumbs.db" ) {
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
