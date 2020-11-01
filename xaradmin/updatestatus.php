<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * update item from publications_admin_modify
 */
function publications_admin_updatestate()
{
    if (!xarSecurity::check('EditPublications')) {
        return;
    }

    // Get parameters
    if (!xarVar::fetch('ids', 'isset', $ids, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('state', 'isset', $state, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('catid', 'isset', $catid, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('ptid', 'isset', $ptid, null, xarVar::DONT_SET)) {
        return;
    }


    // Confirm authorisation code
    if (!xarSec::confirmAuthKey()) {
        return;
    }

    if (!isset($ids) || count($ids) == 0) {
        $msg = xarML('No publications selected');
        throw new DataNotFoundException(null, $msg);
    }
    $states = xarMod::apiFunc('publications', 'user', 'getstates');
    if (!isset($state) || !is_numeric($state) || $state < -1 || ($state != -1 && !isset($states[$state]))) {
        $msg = xarML('Invalid state');
        throw new BadParameterException(null, $msg);
    }

    $pubtypes = xarMod::apiFunc('publications', 'user', 'get_pubtypes');
    if (!empty($ptid)) {
        $descr = $pubtypes[$ptid]['description'];
    } else {
        $descr = xarML('Publications');
        $ptid = null;
    }

    // We need to tell some hooks that we are coming from the update state screen
    // and not the update the actual article screen.  Right now, the keywords vanish
    // into thin air.  Bug 1960 and 3161
    xarCoreCache::setCached('Hooks.all', 'noupdate', 1);

    foreach ($ids as $id => $val) {
        if ($val != 1) {
            continue;
        }
        // Get original article information
        $article = xarMod::apiFunc(
            'publications',
            'user',
            'get',
            array('id' => $id,
                                       'withcids' => 1)
        );
        if (!isset($article) || !is_array($article)) {
            $msg = xarML(
                'Unable to find #(1) item #(2)',
                $descr,
                xarVar::prepForDisplay($id)
            );
            throw new BadParameterException(null, $msg);
        }
        $article['ptid'] = $article['pubtype_id'];
        // Security check
        $input = array();
        $input['article'] = $article;
        if ($state < 0) {
            $input['mask'] = 'ManagePublications';
        } else {
            $input['mask'] = 'EditPublications';
        }
        if (!xarMod::apiFunc('publications', 'user', 'checksecurity', $input)) {
            $msg = xarML(
                'You have no permission to modify #(1) item #(2)',
                $descr,
                xarVar::prepForDisplay($id)
            );
            throw new ForbiddenOperationException(null, $msg);
        }

        if ($state < 0) {
            // Pass to API
            if (!xarMod::apiFunc('publications', 'admin', 'delete', $article)) {
                return; // throw back
            }
        } else {
            // Update the state now
            $article['state'] = $state;

            // Pass to API
            if (!xarMod::apiFunc('publications', 'admin', 'update', $article)) {
                return; // throw back
            }
        }
    }
    unset($article);

    // Return to the original admin view
    $lastview = xarSession::getVar('Publications.LastView');
    if (isset($lastview)) {
        $lastviewarray = unserialize($lastview);
        if (!empty($lastviewarray['ptid']) && $lastviewarray['ptid'] == $ptid) {
            extract($lastviewarray);
            xarController::redirect(xarController::URL(
                'publications',
                'admin',
                'view',
                array('ptid' => $ptid,
                                                'catid' => $catid,
                                                'state' => $state,
                                                'startnum' => $startnum)
            ));
            return true;
        }
    }

    if (empty($catid)) {
        $catid = null;
    }
    xarController::redirect(xarController::URL(
        'publications',
        'admin',
        'view',
        array('ptid' => $ptid, 'catid' => $catid)
    ));

    return true;
}
