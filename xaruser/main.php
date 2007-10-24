<?php
/**
 * xarcpshop  user menu
 *
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
 /*@author jojodee
  *@comment rewritten for Xaraya
  *@original author of file Ed Grosvener
  *@TODO: lots <jojodee> need to redo this entire function,
  * move all the URL stuff to appropriate encode and decode functions
  * isolate the replacement strings section to own function
  * arrange the getpage so it is passed individual prameters rather than a string
  */
function xarcpshop_user_main()
{
    if (!xarSecurityCheck('ViewxarCPShop')) return;

    if (!xarVarFetch('zoom', 'bool', $zoom, 'yes',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('storeid', 'int:1:', $storeid,$storeid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('id', 'str:1:', $id, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('zoomquery', 'str:1:', $zoomquery, '',XARVAR_NOT_REQUIRED)) return;


    $data=array();

    $data = xarModAPIFunc('xarcpshop', 'user', 'menu');

    // Determine whether the the stores have been closed for maintenance
    // or whether we are aware the Cafe Press is down.
    $cpdown = xarModGetVar('xarcpshop','cpdown');
    $closed = xarModGetVar('xarcpshop','closed');
    $verbose = xarModGetVar('xarcpshop','verbose');
    $breadcrumb = xarModGetVar('xarcpshop', 'breadcrumb');
    $defaultstore = xarModGetVar('xarcpshop', 'defaultstore');
    $baseurl=xarServerGetBaseURL();

    if ((xarModGetVar('xarcpshop','SupportShortURLs')) &&
        (xarConfigGetVar('Site.Core.EnableShortURLsSupport'))){

        $shorturls=1;
    } else {
        $shorturls=0;
    }

    if ($zoom == 'yes')   {
        $querystring = '&zoom=yes#zoom';
    } elseif(eregi('.', $id))   {
        $querystring = '&zoom=yes';
    }else{
        $querystring='';
    }


    if (!isset($id) || empty($id)) {
        // No ID is set, so we'll set it to be equal to the default store...
        $id = xarModGetVar('xarcpshop','defaultstore');
    }

    // Make sure the APIs loads or we can't go much further...
    if (!xarModAPILoad('xarcpshop', 'user')) {
                      return; // throw back
    }

    // The store is closed for maintenance (this is basically an emergency
    // shut-off valve in case things go horribly wrong).

    if ($closed == 1)   {
        $data['message']= xarML("Our apologies, this Cafe Press shop is currently closed for maintenance.");

    }else{
     $closed=0;
    }
    $data['closed']=$closed;

    $itemdata=array();
    if (is_numeric($id))   {
        $items = xarModAPIFunc('xarcpshop',
                               'user',
                               'get',
                             array('storeid' => $id));
    }else{
        $cp = $id;
    }

    if (!isset($cp) && $items['toplevel']<>'')   {
        $cp = $items['name'] .'/' . $items['toplevel'];
    }
    if (!isset($cp)){
        $cp= $items['name'];
    }

    $content = xarModAPIFunc('xarcpshop','user','getpage',
                         array('cp' => $cp));
                               //'querystring' => $querystring));
     if ($content == false) {
        return;
    }

    $content = $content[0];
    $js = "<script language=\"JavaScript1.1\" src=\"http://www.cafepress.com/commonscripts.js\"></script>
           <script>\n<!--\n
            function e (z, h, w, b, g) 
            {\n
            document.write('<div style=\"width:'+w+';height:'+h+';background:white url(http://zoom.cafepress.com/'+(z%10)+'/'+z+'_zoom.jpg) no-repeat center center;\"><img border=\"'+b+'\" class=\"imageborder\" src=\"http://www.cafepress.com/cp/img/'+(g?'zoom':'spacer')+'.gif\" width=\"'+w+'\" height=\"'+h+'\"></div>')\n
            }\n-->\n
            </script>\n";
    $content = eregi_replace('"/cp/' , '"http://www.cafepress.com/cp/', $content);
    $content = eregi_replace('\'/cp/','\'http://www.cafepress.com/cp/',$content);
    $content = eregi_replace('\/content\/marketplace\/img\/','http://www.cafepress.com/content/marketplace/img/',$content);
    if ($shorturls ==1) {
        $content = eregi_replace('<a href="/',  '<a href="index.php/xarcpshop/', $content);
    } else {
        $content = eregi_replace('<a href="/',  '<a href="index.php?module=xarcpshop&id=', $content);
    }
    if ($breadcrumb == 0)   {
       //$content = eregi_replace('<p class="storesmallprint">','<p class=storesmallprint" style="visibility:hidden; font-size:0px;">', $content);
     }
    $content = eregi_replace('<form method="post" action="http://www.cafepress.com/cp/addtocart.aspx">', '<form method="post" name="cart" action="http://www.cafepress.com/cp/addtocart.aspx?keepshopping=javascript:self.close()" target="cartWin">', $content);
    $content = eregi_replace('<input type="submit"', "<input type=\"submit\" onclick=\"cartWin = window.open ('','cartWin','toolbar=yes,location=no,directories=no,status=yes,scrollbars=yes,resizable=yes,copyhistory=no,width=800,height=500'); cartWin.focus(); return true;\"", $content);
    if ($shorturls ==1) {
        $content = eregi_replace('<a href="#top">', '<a href="index.php/xarcpshop/'.$cp.'#top">', $content);
    } else {
        $content = eregi_replace('<a href="#top">', '<a href="index.php?module=xarcpshop&id='.$cp.'#top">', $content);
    }
    $content = eregi_replace('Â', '', $content);
    $content = eregi_replace('<img src="/', '<img src="http://www.cafepress.com/', $content);
    $content = eregi_replace("</head>" , '', $content);
    $content = eregi_replace("cellpadding=\"8\"", "cellpadding=\"4\" cellspacing=\"0\"", $content);
    $content = eregi_replace("<td align=\"center\" valign=\"top\">" , "<td align=\"center\" valign=\"top\" width=\"205\">", $content);
    //$content = eregi_replace("width=\"100%\"", '', $content);
$data['js']=$js;
$data['content']=$content;
return $data;
}

?>
