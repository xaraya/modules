<?php
/**
 * File: $Id:
 * 
 * xarcpshop  grab a page
 *
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 *
 */
/**
 * @author original file author Ed Grosvener
 * @author jojodee - adapted for Xaraya
 * the get a page
 */
 function xarcpshop_userapi_getpage($args)
{
    extract($args);

    if (!isset($doc))   {
        $doc = $cp;
    }
    
    if(!isset($site))   {
        $site = 'www.cafepress.com';
    }
    
    if(!isset($start_string))   {
        $start_string = '<!-- Start Main Content -->';
        }
    if(!isset($end_string))   {
        $end_string = '<!-- End Content, Start Footer Include -->';
        }
        
    $debugger = $site . "/" . $doc;
    
    if(isset($querystring))   {
        $doc = $doc . $querystring;
    }
    $page='';

    //Connect to specified server at Cafe Press...

     $reqheader = "GET /$doc HTTP/1.0\r\nHost: $site\r\nUser-Agent: MS Internet Explorer\r\n\r\n";

     $socket =fsockopen("$site", 80, $errno, $errstr);
     if ($socket)
     {
         fputs($socket, $reqheader);
         while (!feof($socket))
         {
             $page .= fgets($socket, 1024);
        }
     }
     else   {
        return false;
        }

     fclose($socket);


     $null = eregi("$start_string(.*)$end_string", $page, $content);

     if(isset($getstring))   {
          $items = array();
         $page_array = split("\n" , $content);
         foreach ($page_array as $line)   {
            if (eregi($getstring , $line))   {
                array_push($items , $line);
            }
         }
         return($items);
     }

     else   {
        return($content);
     }

}
?>
