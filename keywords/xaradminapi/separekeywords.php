<?php
/*
 *
 * Keywords Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @author janez(Alberto Cazzaniga)
*/

/**
 * 
 */

 
function keywords_adminapi_separekeywords($args)
{
extract($args);
 
 if (!xarSecurityCheck('AdminKeywords')) return;

$delimiters = xarModGetVar('keywords','delimiters');
 
 //get first delimiter
 $first = substr("$delimiters", 0, 1);                

 //create an array whit all delimiters
 $arr_delimiters = preg_split('//', $delimiters, -1, PREG_SPLIT_NO_EMPTY);

 //replace all delimiters whit the first one 
 $keywords = clean_delimiters($arr_delimiters, $first, $keywords);
 
 //new array with single keywords
 
 $words = explode($first,$keywords);   
   
   //if nothing has been separated, just plop the whole string (possibly only one keyword) into words.
    if (!isset($words)) {
        $words = array();
        $words[] = $keywords;
    }
    
    return $words;
    }


 function clean_delimiters($search_dels,$del,$string)
{
   foreach($search_dels as $search_del)
   {            
       $string = preg_replace("($search_del)",$del, $string);
   }
   return $string;
}
?>