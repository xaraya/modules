<?php
/**
 * File: $Id:
 * 
 * xarcpshop  user menu
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
 * @author original author of file Ed Grosvener
 * @author adapted for use with Xaraya
 */
function xarcpshop_userapi_getcpparts($args)
{
    extract($args);

    if (!isset($cp))   {
        return false;
    }

    if(!isset($getstring))   {
        return false;
    }

    $cptest = split("/" , $cp);

    if (isset($cptest[1]) && ($cptest[1] == ''))  {
        $cp = $cptest[0];
    } else {
        $cp=$cp;
    }


    $debugger = "http://www.cafepress.com/" . $cp;


    //Connect to specified server at Cafe Press...
     $reqheader = "GET /$cp HTTP/1.0\r\nHost: www.cafepress.com\r\nUser-Agent: MS Internet Explorer\r\n\r\n";
     $page='';
     $socket = fsockopen('www.cafepress.com', 80, $errno, $errstr);
     if ($socket)
     {
         fputs($socket, $reqheader);
         while (!feof($socket))
         {
             $page .= fgets($socket, 1024);
        }
     }
     else   {
         echo "$errstr ($errno)<br>Attempted to connect to: $debugger\n";
        }

     fclose($socket);

     $items = array();
     $page_array = split("\n" , $page);

     foreach ($page_array as $line)   {
         if (eregi($getstring , $line))   {
            array_push($items , $line);
        }
    }
     return($items);
}
?>
