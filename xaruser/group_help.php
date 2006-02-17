<?php
/**
 * Surveys show help page
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team
 */
/*
 * Show help page
 *
 * Provide a help page, listing help details for the
 * specified group name. Help pages are coming from articles and
 * can be specific for a language
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param id    $gid group ID
 *
 * @return array
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 * @deprecated Deprecated [release version here]             [AS REQUIRED]
 */

function surveys_user_group_help()
{
    xarVarFetch('gid', 'id', $gid, 0, XARVAR_NOT_REQUIRED);

    $locale = xarLocaleGetInfo(xarMLSGetCurrentLocale());
    $lang = $locale['lang'];
    $lang_suffix = '_' . $lang;
    $catid_lang = xarModAPIfunc('categories', 'user', 'name2cid', array('name' => $lang));

    $children = array();
    $group_name = '';

    if (is_numeric($gid)) {
        $groups = xarModAPIfunc('surveys', 'user', 'getgroups', array('gid' => $gid, 'lang_suffix' => $lang_suffix));

        if (!empty($groups)) {
            $group_name = $groups['items'][$gid]['group_name'];
            //var_dump($groups['items'][$gid]);

            // If there are children, then provide links to them too.
            if (isset($groups['children'][$gid])) {
                foreach($groups['children'][$gid] as $child_gid) {
                    $children[] = $groups['items'][$child_gid];
                }
            }

            $catid_name = xarModAPIfunc('categories', 'user', 'name2cid', array('name' => $group_name));
        }
    } else {
        $catid_name = xarModAPIfunc('categories', 'user', 'name2cid', array('name' => $gid));
    }

    // English is the default language.
    // TODO: use the language from the default locale for the site instead of English.
    $catid_def = xarModAPIfunc('categories', 'user', 'name2cid', array('name' => 'en'));

    $articles = array();

    // Try the language-specific version.
    if (empty($articles) && !empty($catid_name) && !empty($catid_lang)) {
        $articles = xarModAPIfunc(
            'articles', 'user', 'getall',
            array('cids' => array($catid_name, $catid_lang),'andcids' => true)
        );
    }

    // Try the english version.
    if (empty($articles) && !empty($catid_name) && !empty($catid_def)) {
        $articles = xarModAPIfunc(
            'articles', 'user', 'getall',
            array('cids' => array($catid_name, $catid_def),'andcids' => true)
        );
    }

    // Try the non-language specific version.
    if (empty($articles) && !empty($catid_name)) {
        $articles = xarModAPIfunc(
            'articles', 'user', 'getall',
            array('cids' => array($catid_name),'andcids' => true)
        );
    }

    return array(
        'group_name' => $group_name,
        'lang' => $lang,
        'catid_lang' => $catid_lang,
        'catid_def' => $catid_def,
        'articles' => $articles,
        'children' => $children
    );
}

?>