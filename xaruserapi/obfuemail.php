<?php
/**
 * Obfuscation function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
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
 * @param $email email address to be encoded
 * @$param $text optional text string to be displayed in email link
 * @$param $image optional boolean to display email image, false by default
 * @return array $maildata with values of
 *    $$maildata['encoded'] the encoded email
 *    $$maildata['text'] the text displayed, defaults to 'email us' if null passed in
 *    $$maildata['link'] full link with displayed text if required
 */
function sitecontact_userapi_obfuemail($args)
{
extract($args);

    if (!isset($email) || empty($email)) {return;}
    if (!isset($text) || empty($text)) {$text = $email;}
    if (!isset($usejs)) $usejs = FALSE;
    if ($usejs) {
       $maildata['link']=createMailto($email);
    }else {
        $maildata = array();
        $encoded = bin2hex($email);
        $encoded = chunk_split($encoded, 2, '%');
        $encoded = '%' . substr($encoded, 0, strlen($encoded) - 1);
        $maildata['encoded']=$encoded;
    }
    if (!isset($text) || empty($text)) {
        $maildata['text']=xarML('email us');
    }else{
        $maildata['text']=$text;
    }
    if (isset($image) && TRUE==$image) {
       $img = xarTplGetImage('email.gif','sitecontact');
       $maildata['link']= "<a href=\"mailto:{$maildata['encoded']}\"><img src=\"{$img}\" alt=\"Email\" />&#160;".$maildata['text'] . "</a>";
    }else {
        $maildata['link']= "<a href=\"mailto:{$maildata['encoded']}\">" .$imgsrc.$maildata['text'] . "</a>";
    }


    return $maildata;
}
?>