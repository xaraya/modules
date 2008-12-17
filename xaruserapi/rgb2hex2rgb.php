<?php

/**
 * RGB to HEx to RGB converter
 *
 * @package Xaraya modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage Formantibot
 * @copyright (C) 2008 2skies.com
 * @link http://xarigami.com/project/formantibot
 */
/*
 * @author original pradeep
 * @author Jo Dalle Nogare icedlava@2skies.com - working version for xaraya
 * http://www.go4expert.com
 */
 
function formantibot_userapi_rgb2hex2rgb($args)
 {
     extract($args);
     if(!$c) return false;
     $c = trim($c);
   
     $out = false;
 
     if(eregi("^[0-9ABCDEFabcdef\#]+$", $c))
     {
         $c = str_replace('#','', $c);
         $l = strlen($c);
         
         if ($l == 3)  {
         
             unset($out);
             $out[0] = $out['r'] = $out['red'] = hexdec(substr($c, 0,1));
             $out[1] = $out['g'] = $out['green'] = hexdec(substr($c, 1,1));
             $out[2] = $out['b'] = $out['blue'] = hexdec(substr($c, 2,1));
         
         } elseif ($l == 6)  {
         
             unset($out);
             $out[0] = $out['r'] = $out['red'] = hexdec(substr($c, 0,2));
             $out[1] = $out['g'] = $out['green'] = hexdec(substr($c, 2,2));
             $out[2] = $out['b'] = $out['blue'] = hexdec(substr($c, 4,2));
         } else {
             $out = false;
         } 
     } elseif (eregi("^[0-9]+(,| |.)+[0-9]+(,| |.)+[0-9]+$", $c))
     {
         if (eregi(",", $c)) {
            $e = explode(",",$c);
         } else if(eregi(" ", $c)) {
            $e = explode(" ",$c);
         } else if(eregi(".", $c)) {
            $e = explode(".",$c);
         } else {
            return false;
         }
          
         if(count($e) != 3)
             return false;
 
         $out = '#';
         for($i = 0; $i<3; $i++) 
             $e[$i] = dechex(($e[$i] <= 0)?0:(($e[$i] >= 255)?255:$e[$i]));
         
            
         for($i = 0; $i<3; $i++) 
            $out .= ((strlen($e[$i]) < 2)?'0':'').$e[$i];
         
         $out = strtoupper($out);
     }  else {
     
         $out = false;
     }
     return $out;
 }
 ?>