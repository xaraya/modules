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
 * redirect to a site based on some URL field of the item
 */
function publications_user_redirect($args)
{
    // Get parameters from user
    if (!xarVar::fetch('id', 'id', $id, null, xarVar::NOT_REQUIRED)) {
        return;
    }

    // Override if needed from argument array
    extract($args);

    if (!isset($id) || !is_numeric($id) || $id < 1) {
        return xarML('Invalid publication ID');
    }

    // Load API
    if (!xarMod::apiLoad('publications', 'user')) {
        return;
    }

    // Get publication
    $publication = xarMod::apiFunc(
        'publications',
        'user',
        'get',
        array('id' => $id)
    );

    if (!is_array($publication)) {
        $msg = xarML('Failed to retrieve publication in #(3)_#(1)_#(2).php', 'user', 'get', 'publications');
        throw new DataNotFoundException(null, $msg);
    }

    $ptid = $publication['pubtype_id'];

    // Get publication types
    $pubtypes = xarMod::apiFunc('publications', 'user', 'get_pubtypes');

    // TODO: improve this e.g. when multiple URL fields are present
    // Find an URL field based on the pubtype configuration
    foreach ($pubtypes[$ptid]['config'] as $field => $value) {
        if (empty($value['label'])) {
            continue;
        }
        if ($value['format'] == 'url' && !empty($publication[$field]) && $publication[$field] != 'http://') {
            // TODO: add some verifications here !
            $hooks = xarModHooks::call(
                'item',
                'display',
                $id,
                array('module'    => 'publications',
                                           'itemtype'  => $ptid,
                                          ),
                'publications'
            );
            xarController::redirect($article[$field]);
            return true;
        } elseif ($value['format'] == 'urltitle' && !empty($publication[$field]) && substr($publication[$field], 0, 2) == 'a:') {
            $array = unserialize($publication[$field]);
            if (!empty($array['link']) && $array['link'] != 'http://') {
                $hooks = xarModHooks::call(
                    'item',
                    'display',
                    $id,
                    array('module'    => 'publications',
                                               'itemtype'  => $ptid,
                                              ),
                    'publications'
                );
                xarController::redirect($array['link']);
                return true;
            }
        }
    }

    return xarML('Unable to find valid redirect field');
}
