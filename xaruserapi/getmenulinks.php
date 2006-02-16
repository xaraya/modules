<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * utility function pass individual menu items to the main menu
 *
 * @return array containing the menulinks for the main menu items.
 */
function articles_userapi_getmenulinks()
{
    $menulinks = array();

    // Security Check
    if (!xarSecurityCheck('ViewArticles',0)) {
        return $menulinks;
    }

// TODO: re-evaluate for browsing by category

    $menulinks[] = Array('url'   => xarModURL('articles',
                                              'user',
                                              'view'),
                         'title' => xarML('Highlighted Articles'),
                         'label' => xarML('Front Page'));

    if(!xarVarFetch('ptid',     'isset', $ptid,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemtype', 'isset', $itemtype,  NULL, XARVAR_DONT_SET)) {return;}

    if (empty($ptid)) {
        if (!empty($itemtype)) {
            $ptid = $itemtype;
        } else {
            $ptid = null;
        }
    }
    $publinks = xarModAPIFunc('articles','user','getpublinks',
                              //array('status' => array(3,2), 'ptid' => $ptid));
                              // we show all links here
                              array('status' => array(3,2)));
    foreach ($publinks as $pubitem) {
        $menulinks[] = Array('url'   => $pubitem['publink'],
                             'title' => xarML('Display #(1)',$pubitem['pubtitle']),
                             'label' => $pubitem['pubtitle']);
        if (isset($ptid) && $pubitem['pubid'] == $ptid) {
            if (xarSecurityCheck('SubmitArticles',0,'Article',$ptid.':All:All:All')) {
                $menulinks[] = Array('url'   => xarModURL('articles',
                                                          'admin',
                                                          'new',
                                                          array('ptid' => $ptid)),
                                     'title' => xarML('Submit #(1)',$pubitem['pubtitle']),
                                     'label' => '&nbsp;' . xarML('Submit Now'));
            }

            $settings = unserialize(xarModGetVar('articles', 'settings.'.$ptid));
            if (!empty($settings['showarchives'])) {
                $menulinks[] = Array('url'   => xarModURL('articles',
                                                          'user',
                                                          'archive',
                                                          array('ptid' => $ptid)),
                                     'title' => xarML('View #(1) Archive',$pubitem['pubtitle']),
                                     'label' => '&nbsp;' . xarML('Archives'));
            }

/*
            $menulinks[] = Array('url'   => xarModURL('articles',
                                                      'user',
                                                      'viewmap',
                                                      array('ptid' => $ptid)),
                                 'title' => xarML('Displays a map of all published content'),
                                 'label' => '&nbsp;' . xarML('Article Map'));
*/
        }
    }

/*
    if (empty($ptid)) {
*/
        $menulinks[] = Array('url'   => xarModURL('articles',
                                                  'user',
                                                  'viewmap',
                                                  array('ptid' => $ptid)),
                             'title' => xarML('Displays a map of all published content'),
                             'label' => xarML('Article Map'));
/*
        $menulinks[] = Array('url'   => xarModURL('articles',
                                                  'user',
                                                  'archive'),
                             'title' => xarML('Displays an archive for all published content'),
                             'label' => xarML('Archives'));
    }
*/

    return $menulinks;
}

?>
