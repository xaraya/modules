<?php
/**
 * Obfuscation function
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Obfuscation for email - another method
 *
 * @author Jo Dalle Nogare
 * @param $email email address to be encoded
 * @$param $text optional text string to be displayed in email link
 * @return array $maildata with values of
 *    $$maildata['encoded'] the encoded email
 *    $$maildata['text'] the text displayed, defaults to 'email us' if null passed in
 *    $$maildata['link'] full link with displayed text if required
 */
function sitecontact_userapi_obfuemail($args)
{
    extract($args);

    if (!isset($email) || empty($email)) {return;}

    /* Initialise the array that will hold the returned data*/
    $maildata = array();
    $encoded = bin2hex($email);
    $encoded = chunk_split($encoded, 2, '%');
    $encoded = '%' . substr($encoded, 0, strlen($encoded) - 1);
    $maildata['encoded']=$encoded;
    if (!isset($text) || empty($text)) {
        $maildata['text']=xarML('email us');
    }else{
        $maildata['text']=$text;
    }
    $maildata['link']= "<a href=\"mailto:" . $maildata['encoded'] . "\">" . $maildata['text'] . "</a>";
    return $maildata;
}
?>