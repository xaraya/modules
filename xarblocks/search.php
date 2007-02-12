<?php
/**
 * Search System - Present searches via hooks
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Search Module
 * @link http://xaraya.com/index.php/release/32.html
 * @author Search Module Development Team
 */
/**
 * initialise block
 *
 * @author Johnny Robeson
 * @access public
 * @param none $
 * @return nothing
 * @throws no exceptions
 * @todo nothing
 */
function search_searchblock_init()
{
    return array(
        'nocache' => 1, // don't cache by default
        'pageshared' => 1, // share across pages
        'usershared' => 1, // share for group members
        'cacheexpire' => null);
}

/**
 * get information on block
 *
 * @author Johnny Robeson
 * @access public
 * @param none $
 * @return data array
 * @throws no exceptions
 * @todo nothing
 */
function search_searchblock_info()
{
    // Values
    return array('text_type'        => 'Search',
        'module'                    => 'search',
        'text_type_long'            => 'Search Block',
        'allow_multiple'            => false,
        'form_content'              => false,
        'form_refresh'              => false,
        'show_preview'              => true);
}

/**
 * display search block
 *
 * @author Johnny Robeson
 * @access public
 * @param none $
 * @return data array on success or void on failure
 * @throws no exceptions
 * @todo implement centre menu position
 */
function search_searchblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ReadSearch', 0)) {return;}

    $blockinfo['content'] = array(
        'blockid' => $blockinfo['bid']
    );

    return $blockinfo;
}

?>
