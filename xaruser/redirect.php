<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * redirect to a site based on some URL field of the item
 */
function publications_user_redirect($args)
{
    // Get parameters from user
    if(!xarVarFetch('id', 'id', $id, NULL, XARVAR_NOT_REQUIRED)) {return;}

    // Override if needed from argument array
    extract($args);

    if (!isset($id) || !is_numeric($id) || $id < 1) {
        return xarML('Invalid article ID');
    }

    // Load API
    if (!xarModAPILoad('publications', 'user')) return;

    // Get article
    $article = xarModAPIFunc('publications',
                            'user',
                            'get',
                            array('id' => $id));

    if (!is_array($article)) {
        $msg = xarML('Failed to retrieve article in #(3)_#(1)_#(2).php', 'user', 'get', 'publications');
        throw new DataNotFoundException(null, $msg);
    }

    $ptid = $article['pubtype_id'];

    // Get publication types
    $pubtypes = xarModAPIFunc('publications','user','get_pubtypes');

// TODO: improve this e.g. when multiple URL fields are present
    // Find an URL field based on the pubtype configuration
    foreach ($pubtypes[$ptid]['config'] as $field => $value) {
        if (empty($value['label'])) {
            continue;
        }
        if ($value['format'] == 'url' && !empty($article[$field]) && $article[$field] != 'http://') {
// TODO: add some verifications here !
            $hooks = xarModCallHooks('item', 'display', $id,
                                     array('module'    => 'publications',
                                           'itemtype'  => $ptid,
                                          ),
                                     'publications'
                                    );
            xarResponseRedirect($article[$field]);
            return true;
        } elseif ($value['format'] == 'urltitle' && !empty($article[$field]) && substr($article[$field],0,2) == 'a:') {
            $array = unserialize($article[$field]);
            if (!empty($array['link']) && $array['link'] != 'http://') {
                $hooks = xarModCallHooks('item', 'display', $id,
                                         array('module'    => 'publications',
                                               'itemtype'  => $ptid,
                                              ),
                                         'publications'
                                        );
                xarResponseRedirect($array['link']);
                return true;
            }
        }
    }

    return xarML('Unable to find valid redirect field');
}

?>
