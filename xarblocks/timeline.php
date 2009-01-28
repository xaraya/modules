<?php
/**
 * Privilege Wizard - module privilege manager
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Privilege Wizard
 * @link http://xaraya.com/index.php/release/releaseidTBC.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */
/**
 * Privileges Block  - standard Initialization function
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @return array
 */
function twitter_timelineblock_init()
{
    return array(
        'username'    => '',
        'password'    => '',
        'numitems'    => 5,
        'truncate'    => 0,
        'showimages'  => false,
        'showmyimage' => false,
        'shownumsheep' => false,
        'showothersheep' => false,
        'showsource'  => false,
        'showtimes'   => 1,
        'transformat' => 1,
        'transformhash' => 1,
        'showfavourites' => false,
        'showtweetinput' => false,
        'showbadge' => false,
        'timeline' => 'friends',
        'nocache'     => 0, /* cache by default (if block caching is enabled) */
        'pageshared'  => 1, /* share across pages */
        'usershared'  => 1, /* share across group members */
        'cacheexpire' => null
    );
}

/**
 * Get information on block
 * @return array
 */
function twitter_timelineblock_info()
{
    /* Values */
    return array(
        'text_type' => 'Timeline',
        'module' => 'twitter',
        'text_type_long' => 'Show twitter timeline',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * Display block
 * @param array $blockinfo The array with all information this block needs
 * @return array $blockinfo
 */
function twitter_timelineblock_display($blockinfo)
{
    include_once('modules/twitter/xarclass/twitterAPI.php');

    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    $data = array();
    $defaults = twitter_timelineblock_init();

    $numitems = isset($vars['numitems']) && !empty($vars['numitems']) && is_numeric($vars['numitems']) && $vars['numitems'] <= 20 ? $vars['numitems'] : $defaults['numitems'];
    $username = isset($vars['username']) && !empty($vars['username']) && is_string($vars['username']) ? $vars['username'] : $defaults['username'];
    $password = !empty($username) && isset($vars['password']) && !empty($vars['password']) ? $vars['password'] : $defaults['password'];
    
    
    $t = new twitter();

    if (empty($username)) {
      $timeline = 'public';
    } elseif (empty($password)) {
      $timeline = 'user';
    } else {
      $timeline = 'friends';
    }
    
    switch ($timeline) {
      case 'public':
      default:
        $res = $t->publicTimeline();
      break;
      case 'user':
        $t->username=$username;
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
        $items[$i] = array(
          'created_at' => strtotime($tweet->created_at),
          'screen_name' => $tweet->user->screen_name,          
          'name' => $tweet->user->name,
          'text' => $tweet->text,
          'id' => $tweet->id,
          'source' => $tweet->source
        );
        $i++;
        if ($i == $numitems-1) break;
      }
    }

    $data['items'] = $items;        

    /* Now we need to send our output to the template.
     * Just return the template data.
     */
    $blockinfo['content'] = $data;

    return $blockinfo;
}
/**
 * Modify Function to the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function twitter_timelineblock_modify($blockinfo)
{
    // Break out options from our content field.
    // Prepare for when content is passed in as an array.
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }
    // Keep all the default values in one place.
    $defaults = twitter_timelineblock_init();

    $vars['numitems'] = isset($vars['numitems']) ? $vars['numitems'] : $defaults['numitems'];
 
    $vars['blockid'] = $blockinfo['bid'];
 
    // Just return the template variables.
    return $vars;
}

/**
 * Update block
 * @param array $blockinfo The array with all information this block needs
 * @return array $blockinfo
 */
function twitter_timelineblock_insert($blockinfo)
{
    // Keep all the default values in one place.
    $defaults = twitter_timelineblock_init();
    $vars = array();

    $blockinfo['content'] = $vars;
    return $blockinfo;
}


?>