<?php
/**
 * Regenerate the page output cache of URLs in sessionless list
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.cache_hooks');

/**
 * regenerate the page output cache of URLs in the session-less list
 * @author jsb
 *
 * @return void
 */
function xarcachemanager_adminapi_regenstatic($nolimit = null)
{
    return xarCache_Hooks::regenstatic($nolimit);
}
