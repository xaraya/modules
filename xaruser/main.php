<?php
/**
 * The main user function
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * The main user function
 *
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments. As such it can be used for a number
 * of things, but most commonly it either just shows the module menu and
 * returns or calls whatever the module designer feels should be the default
 * function (often this is the view() function)
 *
 * @author the Example module development team
 * @return array $data An array with the data for the template
 */
include_once('modules/twitter/xarclass/twitterAPI.php');
function twitter_user_main()
{

    if (!xarSecurityCheck('ViewTwitter')) return;
    if (!xarVarFetch('timeline', 'str:1', $timeline, 'public', XARVAR_NOT_REQUIRED)) return;

    $data = array();
    $username = xarModGetVar('twitter', 'username');
    $password = xarModGetVar('twitter', 'password');
    $numitems = xarModGetVar('twitter', 'itemsperpage');

    if (empty($username) || empty($password)) $timeline = 'public';

    $t = new twitter();

    switch ($timeline) {
      case 'public':
      default:
        $res = $t->publicTimeline();
      break;
      case 'user':
        $t->username=$username;
        $t->password=$password;
        $res = $t->userTimeline(false, $numitems);
      break;
      case 'friends':
        $t->username=$username;
        $t->password=$password;
        $res = $t->friendsTimeline();
      break;
    }

    $items = array();
    if ($res) {
      $i = 0;
      foreach ($res->status as $tweet) {
        $thistext = $tweet->text;
        // urls
        $thistext = preg_replace("#(^|[\n ])([\w]+?://[^ \"\n\r\t<]*)#is", "\\1<a href=\"\\2\" rel=\"external\">\\2</a>", $thistext); 
        // hashtags
        $thistext = preg_replace("/(?:^|\W)\#([a-zA-Z0-9\-_\.+:=]+\w)(?:\W|$)/is", " <a href=\"http://hashtags.org/tag/\\1/messages\">#\\1</a> ", $thistext);
        // at tags
        $thistext = preg_replace("/(?:^|\W|#)@(\w+)/is", " <a href=\"http://twitter.com/\\1\">@\\1</a> ", $thistext);
        $items[$i] = array(
          'created_at' => strtotime($tweet->created_at),
          'screen_name' => $tweet->user->screen_name,          
          'name' => $tweet->user->name,
          'profile_image_url' => $tweet->user->profile_image_url,
          'text' => $thistext,
          'id' => $tweet->id,
          'source' => $tweet->source
        );
        $i++;
        if ($i == $numitems-1) break;
      }
    }
    //print_r($res);
    $data['items'] = $items;   
    $data['username'] = $username;
    $data['timeline'] = $timeline;

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Welcome')));
    /* Return the template variables defined in this function */
    return $data;

}
?>