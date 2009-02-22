<?php
/**
 * Twitter Module 
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */
/**
 * Timeline Block  - standard Initialization function
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @return array
 */
function twitter_timelineblock_init()
{
    return array(
        'username'        => xarModGetVar('twitter', 'site_screen_name'),
        'password'        => xarModGetVar('twitter', 'site_screen_pass'),
        'numitems'        => 3,
        'truncate'        => 0,
        'showimages'      => false,
        'showmyimage'     => false,
        'showsource'      => true,
        'showmodule'      => true,
        'showfollow'      => true,
        'timeline'        => xarModGetVar('twitter', 'deftimeline'),
        'nocache'         => 0, /* cache by default (if block caching is enabled) */
        'pageshared'      => 1, /* share across pages */
        'usershared'      => 1, /* share across group members */
        'cacheexpire'     => null
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

    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    $data = array();
    $defaults = twitter_timelineblock_init();

    $vars['username'] = !isset($vars['username']) ? $defaults['username'] : $vars['username'];
    $vars['password'] = !isset($vars['password']) ? $defaults['password'] : $vars['password'];
    $vars['numitems'] = !isset($vars['numitems']) ? $defaults['numitems'] : $vars['numitems'];
    $vars['truncate'] = !isset($vars['truncate']) ? $defaults['truncate'] : $vars['truncate'];
    $vars['showimages'] = !isset($vars['showimages']) ? $defaults['showimages'] : $vars['showimages']; 
    $vars['showmyimage'] = !isset($vars['showmyimage']) ? $defaults['showmyimage'] : $vars['showmyimage'];
    $vars['showsource'] = !isset($vars['showsource']) ? $defaults['showsource'] : $vars['showsource'];
    $vars['showmodule'] = !isset($vars['showmodule']) ? $defaults['showmodule'] : $vars['showmodule']; 
    $vars['showfollow'] = !isset($vars['showfollow']) ? $defaults['showfollow'] : $vars['showfollow']; 
    $vars['timeline'] = !isset($vars['timeline']) ? $defaults['timeline'] : $vars['timeline'];

    $items = xarModAPIFunc('twitter', 'user', 'rest_methods',
      array(
        'area' => 'statuses',
        'method' => $vars['timeline'].'_timeline',
        'username' => $vars['username'],
        'password' => $vars['password'],
        'count' => $vars['numitems'],
        'truncate' => $vars['truncate'],
        'cached' => true,
        'refresh' => 300,
        'superrors' => true
      ));

    $data['status_elements'] = !$items ? array() : $items;
    $data['showimages'] = $vars['showimages'];
    $data['showmyimage'] = $vars['showmyimage'];
    $data['showsource'] = $vars['showsource'];
    $data['showmodule'] = $vars['showmodule'];
    $data['showfollow'] = $vars['showfollow'];
    $data['timeline'] = $vars['timeline'];
    $data['username'] = xarVarPrepForDisplay($vars['username']);
    $userinfo = array();
    if (!empty($vars['username']) && !empty($vars['password'])) {
      $userinfo = xarModAPIFunc('twitter', 'user', 'rest_methods', 
        array(
          'area' => 'account',
          'method' => 'verify_credentials',
          'username' => $vars['username'], 
          'password' => $vars['password'],
          'cache' => true,
          'refresh' => 3600,
          'superrors' => true
        ));
    }
    $data['userinfo'] = $userinfo;
    
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

    $vars['username'] = !isset($vars['username']) ? $defaults['username'] : $vars['username'];
    $vars['password'] = !isset($vars['password']) ? $defaults['password'] : $vars['password'];
    $vars['numitems'] = !isset($vars['numitems']) ? $defaults['numitems'] : $vars['numitems'];
    $vars['truncate'] = !isset($vars['truncate']) ? $defaults['truncate'] : $vars['truncate'];
    $vars['showimages'] = !isset($vars['showimages']) ? $defaults['showimages'] : $vars['showimages']; 
    $vars['showmyimage'] = !isset($vars['showmyimage']) ? $defaults['showmyimage'] : $vars['showmyimage'];
    $vars['showsource'] = !isset($vars['showsource']) ? $defaults['showsource'] : $vars['showsource'];
    $vars['showmodule'] = !isset($vars['showmodule']) ? $defaults['showmodule'] : $vars['showmodule']; 
    $vars['showfollow'] = !isset($vars['showfollow']) ? $defaults['showfollow'] : $vars['showfollow']; 
    $vars['timeline'] = !isset($vars['timeline']) ? $defaults['timeline'] : $vars['timeline'];

    $timelines = array();
    $timelines[] = array('id' => 'public', 'name' => 'Public');
    $timelines[] = array('id' => 'user', 'name' => 'User');
    $vars['timelines'] = $timelines;

    if (!empty($vars['username']) && !empty($vars['password'])) {
      $userinfo = xarModAPIFunc('twitter', 'user', 'rest_methods', 
        array(
          'area' => 'account',
          'method' => 'verify_credentials',
          'username' => $vars['username'], 
          'password' => $vars['password'],
          'cache' => true,
          'refresh' => 3600,
          'superrors' => true
        ));
      if (!$userinfo) {
        $vars['username'] = $defaults['username'];
        $vars['password'] = $defaults['password'];
      } else {
        $vars['userinfo'] = $userinfo;
      }
    }
    $vars['blockid'] = $blockinfo['bid'];
 
    // Just return the template variables.
    return $vars;
}

/**
 * Update block
 * @param array $blockinfo The array with all information this block needs
 * @return array $blockinfo
 */
function twitter_timelineblock_update($blockinfo)
{
    // Keep all the default values in one place.
    $defaults = twitter_timelineblock_init();
    $vars = array();
    if (!xarVarFetch('username', 'isset', $vars['username'], $defaults['username'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('password', 'isset', $vars['password'], $defaults['password'], XARVAR_NOT_REQUIRED)) return;
    
    if (!empty($vars['username']) && !empty($vars['password'])) {
      $isvalid = xarModAPIFunc('twitter', 'user', 'rest_methods', 
        array(
          'area' => 'account',
          'method' => 'verify_credentials',
          'username' => $vars['username'], 
          'password' => $vars['password'],
          'cache' => true,
          'refresh' => 3600,
          'superrors' => true
        ));
      if (!$isvalid) {
        $vars['username'] = $defaults['username'];
        $vars['password'] = $defaults['password'];
      }
    }
    if (!xarVarFetch('numitems', 'int', $vars['numitems'], $defaults['numitems'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('truncate', 'int', $vars['truncate'], $defaults['truncate'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showimages', 'checkbox', $vars['showimages'], $defaults['showimages'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showmyimage', 'checkbox', $vars['showmyimage'], $defaults['showmyimage'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showsource', 'checkbox', $vars['showsource'], $defaults['showsource'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showmodule', 'checkbox', $vars['showmodule'], $defaults['showmodule'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showfollow', 'checkbox', $vars['showfollow'], $defaults['showfollow'], XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('timeline', 'enum:public:user:friends', $vars['timeline'], $defaults['timeline'], XARVAR_NOT_REQUIRED)) return;

    $blockinfo['content'] = $vars;
    return $blockinfo;
}


?>