<?php
/**
 * Obfuscation function
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */

/**
 * Obfuscation for email - another method
 *
 * @author Jo Dalle Nogare
 * Takes an email address, optional text  for the link text
 * If no text supplied then the email address is used for the link text and partial obfuscated for the display
 * @$param text $email email address to be encoded
 * @$param text $text optional text string to be displayed in email link
 * @$param boolean $image optional flag to display email image, false by default
 * @return array $maildata with values of
 *    $maildata['encoded'] the encoded email
 *    $maildata['text'] the text displayed, defaults to slight obfuscated replaced email address
 *    $maildata['link'] full link with displayed text if required
 */
function sitecontact_userapi_obfuemail($args)
{
extract($args);

    if (!isset($email) || empty($email)) {return;}

    $newemail = $email;
    $maildata = array();
    $encoded = bin2hex($newemail);
    $encoded = chunk_split($encoded, 2, '%');
    $encoded = '%' . substr($encoded, 0, strlen($encoded) - 1);
    $maildata['encoded']=$encoded;

    if (isset($text) && !empty($text)) {
        $maildata['text']=$text;
    }else{
        $newaddress = '';
        for($intCounter = 0; $intCounter < strlen($email); $intCounter++){
            $newaddress .= "&#" . ord(substr($email,$intCounter,1)) . ";";
        }
        $newtext=explode("&#064;", $newaddress);
        $at = xarML(' at ');
        $dot = xarML(' dot ');
        $maildata['text'] = $newtext[0].$at. str_replace("&#046;",$dot,$newtext[1]);
    }
    if (isset($image) && TRUE==$image) {
       $img = xarTplGetImage('email.gif','sitecontact');
       $maildata['link']= "<a href=\"mailto:{$maildata['encoded']}\"><img src=\"{$img}\" alt=\"Email\" />&#160;".$maildata['text'] . "</a>";
    }else {
        $maildata['link']= "<a href=\"mailto:{$maildata['encoded']}\">" .$maildata['text'] . "</a>";
    }

    return $maildata;
}
?>