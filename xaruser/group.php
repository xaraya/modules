<?php
/**
 * Show a group of articles from a newsgroup
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsgroups
 * @link http://xaraya.com/index.php/release/802.html
 * @author John Cox
 */
/**
 * Retrieve several articles from a newsgroup
 *
 * @param string group     newsgroup
 * @param int    startnum  message number the display starts
 * @param string sortby    Either 'thread' or 'article' for sort order of articles
 * @return array
 */


function newsgroups_user_group()
{
    if(!xarSecurityCheck('ReadNewsGroups')) return;

    xarVarFetch('group', 'str:1', $group, NULL, XARVAR_DONT_REUSE, XARVAR_PREP_FOR_DISPLAY);
    xarVarFetch('startnum', 'id', $startnum, NULL, XARVAR_NOT_REQUIRED);
    xarVarFetch('sortby', 'enum:thread:article', $sortby, '', XARVAR_NOT_REQUIRED);
    if (empty($sortby)) {
        $sortby = xarModGetVar('newsgroups','sortby');
    }

    xarTplSetPageTitle($group);

    $numitems = xarModGetVar('newsgroups', 'numitems');

    $data = xarModAPIFunc('newsgroups','user','getoverview',
                          array('group'    => $group,
                                'startnum' => $startnum,
                                'numitems' => $numitems,
                                'sortby'   => $sortby));
    if (!isset($data)) return;

    // Add fromname without email address and quotes
    foreach ($data['items'] as $key => $item) {
        $data['items'][$key]['fromname'] = preg_replace('/(^"|" (?=<)|<.*)/', '', $item['From']);
    }

    if (empty($startnum)){
        $startnum = $data['counts']['last'];
    }

    // Call the xarTPL helper function to produce a pager.
    // $data['counts']['count'] may be to small because of deleted articles
    $articlespan = $data['counts']['last'] - $data['counts']['first'] + 1;

    // Newsgroups are listed backwards. To let the last page show as much
    // articles as wanted in $numitems we have to correct the firstitem
    $firstitem = $data['counts']['last'] - floor($articlespan / $numitems) * $numitems;

    $data['pager'] = xarTplGetPager($startnum,
                                    $articlespan,
                                    xarModURL('newsgroups', 'user', 'group',
                                              array('group' => $group,
                                                    'startnum' => '%%')),
                                    $numitems, // articles per page
                                    array('firstitem' => $firstitem,
                                          'blocksize' => 5)
                                    );

    return $data;
}

?>
