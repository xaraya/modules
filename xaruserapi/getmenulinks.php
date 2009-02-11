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
 * utility function pass individual menu items to the main menu
 *
 * @return array Array containing the menulinks for the main menu items.
 */
function publications_userapi_getmenulinks()
{
    $menulinks = array();

    // Security Check
    if (!xarSecurityCheck('ViewPublications',0)) {
        return $menulinks;
    }

// TODO: re-evaluate for browsing by category

    $menulinks[] = Array('url'   => xarModURL('publications',
                                              'user',
                                              'view'),
                         'title' => xarML('Highlighted Publications'),
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
    $publinks = xarModAPIFunc('publications','user','getpublinks',
                              //array('state' => array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED), 'ptid' => $ptid));
                              // we show all links here
                              array('state' => array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED)));
    foreach ($publinks as $pubitem) {
        $menulinks[] = Array('url'   => $pubitem['publink'],
                             'title' => xarML('Display #(1)',$pubitem['pubtitle']),
                             'label' => $pubitem['pubtitle']);
        if (isset($ptid) && $pubitem['pubid'] == $ptid) {
            if (xarSecurityCheck('SubmitPublications',0,'Publication',$ptid.':All:All:All')) {
                $menulinks[] = Array('url'   => xarModURL('publications',
                                                          'admin',
                                                          'new',
                                                          array('ptid' => $ptid)),
                                     'title' => xarML('Submit #(1)',$pubitem['pubtitle']),
                                     'label' => '&#160;' . xarML('Submit Now'));
            }

            $settings = unserialize(xarModVars::get('publications', 'settings.'.$ptid));
            if (!empty($settings['showarchives'])) {
                $menulinks[] = Array('url'   => xarModURL('publications',
                                                          'user',
                                                          'archive',
                                                          array('ptid' => $ptid)),
                                     'title' => xarML('View #(1) Archive',$pubitem['pubtitle']),
                                     'label' => '&#160;' . xarML('Archives'));
            }

/*
            $menulinks[] = Array('url'   => xarModURL('publications',
                                                      'user',
                                                      'viewmap',
                                                      array('ptid' => $ptid)),
                                 'title' => xarML('Displays a map of all published content'),
                                 'label' => '&#160;' . xarML('Publication Map'));
*/
        }
    }

/*
    if (empty($ptid)) {
*/
        $menulinks[] = Array('url'   => xarModURL('publications',
                                                  'user',
                                                  'viewmap',
                                                  array('ptid' => $ptid)),
                             'title' => xarML('Displays a map of all published content'),
                             'label' => xarML('Publication Map'));
/*
        $menulinks[] = Array('url'   => xarModURL('publications',
                                                  'user',
                                                  'archive'),
                             'title' => xarML('Displays an archive for all published content'),
                             'label' => xarML('Archives'));
    }
*/

    return $menulinks;
}

?>
