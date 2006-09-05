<?php
/**
 * User Photo Selection via block
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Flickring module
 */

/*
 * Skin Selection via block
 * @author Marco Canini
 * initialise block
 */
function flickring_flickrblock_init()
{
    return array(
        'nocache' => 1, // don't cache by default
        'pageshared' => 1, // share across pages
        'usershared' => 0, // don't share across users
        'cacheexpire' => null);
}

/**
 * get information on block
 */
function flickring_flickrblock_info()
{
    return array(
        'text_type' => 'Flickr',
        'module' => 'flickring',
        'text_type_long' => 'Find User Photos'
    );
}

/**
 * Display func.
 * @param $blockinfo array containing title,content
 */
function flickring_flickrblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ReadFlickring', 0, 'Block', "All:" . $blockinfo['title'] . ":" . $blockinfo['bid'])) {return;}

    $data['form_action'] = xarModURL('flickring', 'user', 'showuserphotos');
    $data['blockid'] = $blockinfo['bid'];
    $blockinfo['content'] = $data;

    return $blockinfo;
}

?>
