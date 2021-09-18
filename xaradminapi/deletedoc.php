<?php
/**
 * Delete a doc
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Delete a doc
 *
 * @param $rdid ID
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_adminapi_deletedoc($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($rdid)) {
        throw new BadParameterException(null, xarML('Invalid Parameter Count'));
    }

    // The user API function is called
    $link = xarMod::apiFunc(
        'release',
        'user',
        'getdoc',
        ['rdid' => $rdid]
    );

    if ($link == false) {
        throw new EmptyParameterException(null, xarML('No Such Release Doc Present'));
    }

    // Security Check
    if (!xarSecurity::check('ManageRelease')) {
        return;
    }

    // Get datbase setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $releasetable = $xartable['release_docs'];

    // Delete the item
    $query = "DELETE FROM $releasetable
            WHERE xar_rnid = ?";
    $result =& $dbconn->Execute($query, [$rdid]);
    if (!$result) {
        return;
    }

    // Let any hooks know that we have deleted a link
    xarModHooks::call('item', 'delete', $rdid, '');

    // Let the calling process know that we have finished successfully
    return true;
}
