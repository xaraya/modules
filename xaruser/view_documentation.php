<?php
/*
 * View all release documentation
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
 */
function release_user_view_documentation()
{
    if (!xarSecurity::check('EditRelease')) {
        return;
    }

    // Get the object to be listed
    $data['object'] = DataObjectMaster::getObjectList(array('name' => 'release_docs'));

    return $data;
}
