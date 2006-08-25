<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * Update configuration
 */
function articles_admin_updateconfig()
{
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;
    // Get parameters
    //A lot of these probably are bools, still might there be a need to change the template to return
    //'true' and 'false' to use those...
    if(!xarVarFetch('itemsperpage',      'int',   $itemsperpage,      20, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('adminitemsperpage', 'int',   $adminitemsperpage, 20, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('number_of_columns', 'int',   $number_of_columns, 0, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('shorturls',         'isset', $shorturls,         0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('usetitleforurl',    'isset', $usetitleforurl,    0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('defaultpubtype',    'isset', $defaultpubtype,    1,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('sortpubtypes',      'isset', $sortpubtypes,   'id',  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('defaultview',       'isset', $defaultview,       1,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('showcategories',    'isset', $showcategories,    0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('showkeywords',      'isset', $showkeywords,      0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('showcatcount',      'isset', $showcatcount,      0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('showprevnext',      'isset', $showprevnext,      0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('showcomments',      'isset', $showcomments,      0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('showhitcounts',     'isset', $showhitcounts,     0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('showratings',       'isset', $showratings,       0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('showarchives',      'isset', $showarchives,      0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('showmap',           'isset', $showmap,           0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('showpublinks',      'isset', $showpublinks,      0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('showpubcount',      'isset', $showpubcount,      0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('prevnextart',       'isset', $prevnextart,       0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('page_template',     'isset', $page_template,     '', XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('defaultstatus',     'isset', $defaultstatus,     0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('defaultsort',       'isset', $defaultsort,  'date',  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('usealias',          'isset', $usealias,          0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('ptid',              'isset', $ptid,              0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('dotransform',       'isset', $dotransform,       0,  XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('titletransform',    'isset', $titletransform,    0,  XARVAR_NOT_REQUIRED)) {return;}

    if (empty($ptid)) {
        $ptid = '';
        if (!xarSecurityCheck('AdminArticles')) return;
    } else {
        if (!xarSecurityCheck('AdminArticles',1,'Article',"$ptid:All:All:All")) return;
    }

    if (empty($ptid)) {
        xarModSetVar('articles', 'SupportShortURLs', $shorturls);
        xarModSetVar('articles', 'defaultpubtype', $defaultpubtype);
        xarModSetVar('articles', 'sortpubtypes', $sortpubtypes);
        if (xarDBGetType() == 'mysql') {
            if (!xarVarFetch('fulltext', 'isset', $fulltext, '', XARVAR_NOT_REQUIRED)) {return;}
            $oldval = xarModGetVar('articles', 'fulltextsearch');
            $index = 'i_' . xarDBGetSiteTablePrefix() . '_articles_fulltext';
            if (empty($fulltext) && !empty($oldval)) {
                // Get database setup
                $dbconn =& xarDBGetConn();
                $xartable =& xarDBGetTables();
                $articlestable = $xartable['articles'];
                // Drop fulltext index on xar_articles table
                $query = "ALTER TABLE $articlestable DROP INDEX $index";
                $result =& $dbconn->Execute($query);
                if (!$result) return;
                xarModSetVar('articles', 'fulltextsearch', '');
            } elseif (!empty($fulltext) && empty($oldval)) {
                //$searchfields = array('title','summary','body','notes');
                $searchfields = explode(',',$fulltext);
                // Get database setup
                $dbconn =& xarDBGetConn();
                $xartable =& xarDBGetTables();
                $articlestable = $xartable['articles'];
                // Add fulltext index on xar_articles table
                $query = "ALTER TABLE $articlestable ADD FULLTEXT $index (xar_" . join(', xar_', $searchfields) . ")";
                $result =& $dbconn->Execute($query);
                if (!$result) return;
                xarModSetVar('articles', 'fulltextsearch', join(',',$searchfields));
            }
        }
    }

    $settings = array();
    $settings['itemsperpage']       = $itemsperpage;
    $settings['adminitemsperpage']  = $adminitemsperpage;
    $settings['number_of_columns']  = $number_of_columns;
    $settings['defaultview']        = $defaultview;
    $settings['showcategories']     = $showcategories;
    $settings['showkeywords']       = $showkeywords;
    $settings['showcatcount']       = $showcatcount;
    $settings['showprevnext']       = $showprevnext;
    $settings['showcomments']       = $showcomments;
    $settings['showhitcounts']      = $showhitcounts;
    $settings['showratings']        = $showratings;
    $settings['showarchives']       = $showarchives;
    $settings['dotransform']        = $dotransform;
    $settings['titletransform']     = $titletransform;
    $settings['showmap']            = $showmap;
    $settings['showpublinks']       = $showpublinks;
    $settings['showpubcount']       = $showpubcount;
    $settings['dotransform']        = $dotransform;
    $settings['prevnextart']        = $prevnextart;
    $settings['page_template']      = $page_template;
    $settings['defaultstatus']      = $defaultstatus;
    $settings['defaultsort']        = $defaultsort;
    $settings['usetitleforurl']     = $usetitleforurl;

    if (!empty($ptid)) {
        xarModSetVar('articles', 'settings.'.$ptid, serialize($settings));

        $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
        if ($usealias) {
            xarModSetAlias($pubtypes[$ptid]['name'],'articles');
        } else {
            xarModDelAlias($pubtypes[$ptid]['name'],'articles');
        }

        // Call updateconfig hooks with module + itemtype
        xarModCallHooks('module','updateconfig','articles',
                        array('module'   => 'articles',
                              'itemtype' => $ptid));
    } else {
        xarModSetVar('articles', 'settings', serialize($settings));

        if ($usealias) {
            xarModSetAlias('frontpage','articles');
        } else {
            xarModDelAlias('frontpage','articles');
        }

        // Call updateconfig hooks with module + no itemtype (= default 0)
        xarModCallHooks('module','updateconfig','articles',
                        array('module' => 'articles'));
    }
    if (empty($ptid)) {
        $ptid = null;
    }
    xarResponseRedirect(xarModURL('articles', 'admin', 'modifyconfig',
                                  array('ptid' => $ptid)));
    return true;
}
?>
