<?php
/**
 * Request mapper + default cache settings for the articles module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * Request mapper + default cache settings for the articles module - warning: this is still subject to change !
 *
 * @return array with the request mapper for articles
 */
function articles_xarmapper()
{
    $module = 'articles';

    // Initialize array
    $xarmapper = array();

/* Cfr. xarcachemanager module caching config (and future request mapper ?)

    // Hint: generate this dynamically first, and save the output in your xarmapper.php

// CHECKME: do we want/need the api functions too someday ?
    //$functiontypes = array('user','admin','userapi','adminapi');
// CHECKME: do we want/need the admin gui functions too someday ?
    $functiontypes = array('user','admin');

    foreach ($functiontypes as $type) {
        // find the $type functions
        $typedir = realpath(sys::code() . 'modules/' . $module . '/xar' . $type . '/');
        if ($typedir && $dh = @opendir($typedir)) {
            while(($filename = @readdir($dh)) !== false) {
                // Got a file or directory.
                $thisfile = $typedir . '/' . $filename;
                if (is_file($thisfile) && preg_match('/^(.+)\.php$/', $filename, $matches)) {
                    if ($type == 'user' || $type == 'admin') {
                        // try to find all xarVarFetch() parameters
                        $params = array();
                        $content = implode('',file($thisfile));
                        if (preg_match_all("/xarVarFetch\('([^']+)'/", $content, $params)) {
                            $paramslist = implode(',', $params[1]);
                        } else {
                            $paramslist = '';
                        }
                        // admin functions are not cacheable by default
                        if ($type == 'admin') {
                            $nocache = 1;
                            $usershared = 0;
                        } else {
                            $nocache = 0;
                            $usershared = 1;
                        }
                    } else {
                        // api and other functions are NOT cacheable by default (currently unused)
                        $paramslist = '';
                        $nocache = 1;
                        $usershared = 0;
                    }
                    $xarmapper[$type][$matches[1]] = array('params' => $paramslist, 'nocache' => $nocache, 'usershared' => $usershared, 'cacheexpire' => '');
                }
            }
            closedir($dh);
        }
    }
    // Output the generated xarmapper for now (to be copied to your xarmapper.php)
    $output = str_replace("\n","\n    ", str_replace('  ', '    ', var_export($xarmapper, true)));
    echo '<textarea rows="20" cols="80">
    // Please verify those function parameters
    $xarmapper = ' . $output . ';</textarea>';
*/

    // Please verify those function parameters
    $xarmapper = array (
        'user' => 
        array (
            'archive' => 
            array (
                'params' => 'ptid,sort,month,cids,catid',
                'nocache' => 0,
                'usershared' => 1,
                'cacheexpire' => '',
            ),
            'display' => 
            array (
                'params' => 'aid,page,ptid,q',
                'nocache' => 0,
                'usershared' => 1,
                'cacheexpire' => '',
            ),
            'main' => 
            array (
                'params' => '',
                'nocache' => 0,
                'usershared' => 1,
                'cacheexpire' => '',
            ),
            'redirect' => 
            array (
                'params' => 'aid',
                'nocache' => 0,
                'usershared' => 1,
                'cacheexpire' => '',
            ),
            'search' => 
            array (
                'params' => 'startnum,cids,andcids,catid,ptid,ptids,articles_startdate,articles_enddate,start,end,search,status,q,author,by,sort,bool,articles_fields,searchtype,fields',
                'nocache' => 0,
                'usershared' => 1,
                'cacheexpire' => '',
            ),
            'view' => 
            array (
                'params' => 'startnum,cids,andcids,catid,ptid,itemtype,q,sort,numcols,authorid,pubdate,startdate,enddate,where,letter',
                'nocache' => 0,
                'usershared' => 1,
                'cacheexpire' => '',
            ),
            'viewmap' => 
            array (
                'params' => 'ptid,by,go,catid,cids',
                'nocache' => 0,
                'usershared' => 1,
                'cacheexpire' => '',
            ),
        ),
        'admin' => 
        array (
            'create' => 
            array (
                'params' => 'ptid,new_cids,preview,save,view,return_url',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'delete' => 
            array (
                'params' => 'aid,confirm,return_url',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'exportpubtype' => 
            array (
                'params' => 'ptid',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'importpages' => 
            array (
                'params' => 'basedir,filelist,refresh,ptid,content,title,cids,filterhead,filtertail,findtitle,numrules,search,replace,test,import',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'importpictures' => 
            array (
                'params' => 'basedir,baseurl,thumbnail,filelist,refresh,ptid,title,summary,content,usefilemtime,cids,test,import',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'importpubtype' => 
            array (
                'params' => 'import,xml',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'main' => 
            array (
                'params' => '',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'modify' => 
            array (
                'params' => 'aid,return_url',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'modifyconfig' => 
            array (
                'params' => 'ptid',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'new' => 
            array (
                'params' => 'ptid,catid,itemtype,return_url',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'overview' => 
            array (
                'params' => '',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'privileges' => 
            array (
                'params' => 'ptid,cid,uid,author,aid,apply,extpid,extname,extrealm,extmodule,extcomponent,extinstance,extlevel,pparentid',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'pubtypes' => 
            array (
                'params' => 'ptid,action,name,descr,label,format,input',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'showpropval' => 
            array (
                'params' => 'ptid,field,preview,confirm,return_url',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'stats' => 
            array (
                'params' => 'group',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'update' => 
            array (
                'params' => 'aid,ptid,modify_cids,preview,save,return_url',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'updateconfig' => 
            array (
                'params' => 'itemsperpage,adminitemsperpage,number_of_columns,shorturls,usetitleforurl,defaultpubtype,sortpubtypes,ptypenamechange,defaultview,showcategories,showkeywords,showcatcount,showprevnext,showcomments,showhitcounts,showratings,showarchives,showmap,showpublinks,showpubcount,prevnextart,page_template,defaultstatus,defaultsort,usealias,ptid,dotransform,titletransform,checkpubdate,fulltext',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'updatestatus' => 
            array (
                'params' => 'aids,status,catid,ptid',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'view' => 
            array (
                'params' => 'startnum,ptid,status,itemtype,catid,sort,authorid,lang,pubdate',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
            'waitingcontent' => 
            array (
                'params' => '',
                'nocache' => 1,
                'usershared' => 0,
                'cacheexpire' => '',
            ),
        ),
    );

    // Return mapper information
    return $xarmapper;
}

?>
