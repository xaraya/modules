<?php
/**
 * Get release links
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
 * Get release links
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 * @TODO
 */
function release_userapi_getreleaselinks($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($rnid)) {
        $rnid = null;
    }
    if (!isset($count)) {
        $count = true;
    }
    if (!$count) {
        $all = 1;
    }

    $releaseinfo = array();

    // Security Check
    if (!xarSecurity::check('OverviewRelease')) {
        return;
    }

    $releaseinfo = xarMod::apiFunc(
        'release',
        'user',
        'getallnotes',
        array('approved' => 1)
    );

    $totreleases = count($releaseinfo);

    $releaselinks = array();
    foreach ($releaseinfo as $release) {
        $item['release']['regname']=$release['regname'];
        $item['release']['link'] = xarController::URL('release', 'admin', 'modifynote', array('rnid'=>$release['rnid']));
        $releaselinks[] = $item['release'];
    }
    $releaselinks['counted']=$totreleases;
    return $releaselinks;
}
