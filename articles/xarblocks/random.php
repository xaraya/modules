<?php
// File: random.php
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Author: Roger Keays <r.keays@ninthave.net>
//   based on featureitems.php
//
// Purpose of file: Random Articles Block
// ----------------------------------------------------------------------

/**
 * initialise block
 */
function articles_randomblock_init()
{
    // Default values to initialize the block.
    return array(
        'ptid' => '',
        'cid' => '',
        'status' => '3,2',
        'refreshtime' => 60*24,
        'showtitle' => true,
        'showpubdate' => false,
        'showauthor' => false,
        'showsubmit' => false,
        'showsummary' => true
    );
}

/**
 * get information on block
 */
function articles_randomblock_info()
{
    // Return details about this block.
    return array(
        'text_type' => 'Random article',
        'module' => 'articles',
        'text_type_long' => 'Show a single random article',
        'allow_multiple' => true,
        'form_content' => false, // Deprecated
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * display block
 */
function articles_randomblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ReadArticlesBlock', 1, 'Block', $blockinfo['title'])) {return;}

    // Get variables from block content.
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Allow refresh by setting refreshrandom variable
    xarVarFetch('refreshrandom', 'checkbox', $refreshrandom, false, XARVAR_NOT_REQUIRED);
    if ($refreshrandom) {
        $vars['refreshtime'] = 0;
    }

    // Check cache 
    $refresh = time() - ($vars['refreshtime'] * 60);
    $varDir = xarCoreGetVarDirPath();
    $cacheKey = md5('block-articles-random-' . $blockinfo['bid']);
    $cachedFileName = $varDir . '/cache/templates/' . $cacheKey;
    if ($vars['refreshtime'] > 0 && file_exists($cachedFileName) && filemtime($cachedFileName) > $refresh) {
        $fp = @fopen($cachedFileName, 'r');

        // Read From Our Cache
        $vars = unserialize(fread($fp, filesize($cachedFileName)));
        fclose($fp);
    } else {
        // count number of articles and pick a random number
        $count = xarModAPIFunc(
            'articles', 'user', 'countitems',
            array(
                'ptid' => $vars['ptid'],
                'cid' => $vars['cid'],
                'status' => explode(",", $vars['status'])
            )
        );

        mt_srand((double) microtime() * 1000000);

        // Don't show block if there are no articles.
        if ($count == 0) {return;}
        $random = mt_rand(0, $count) - 1;

        // Database information
        xarModDBInfoLoad('articles');

        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();
        $articlestable = $xartable['articles'];

        // Create WHERE field from non empty parameters
        $andlist = array();
        $orlist = array();
        foreach (explode(",", $vars['status']) as $status) {
            $orlist[] = "xar_status = " . $dbconn->qstr($status);
        }
        if (!empty($vars['ptid'])) {
            $andlist[] = "xar_pubtypeid = " . $dbconn->qstr($vars['ptid']);
        }

        /* TODO fix to check categories
        if (!empty($vars['cid'])) {
            $andlist[] = "cid='".xarVarPrepForStore($vars['cid'])."'";
        }*/

        // create sql where string from AND and OR lists
        if (count($andlist) > 0) {
            $wherestring = ' WHERE ' . join(' AND ', $andlist);
            if (count($orlist) > 0) {
                $wherestring .= ' AND (' . join(' OR ', $orlist) . ")";
            }
        } else {
            if (count($orlist) > 0) {
                $wherestring = ' WHERE ' . join(' OR ', $orlist);
            }
        }

        // Create SQL query
        $query = 'SELECT xar_aid, xar_title, xar_summary, xar_pubdate, xar_authorid'
            . ' FROM ' . $articlestable . $wherestring;
        $msg = xarML(
            'Getting random article #(1) of #(2) with query #(3) ',
            $random, $count, $query
        );
        xarLogMessage($msg, XARLOG_LEVEL_DEBUG);

        // Execute the query.
        $result =& $dbconn->SelectLimit($query, 1, $random);
        if (!$result) {
            return;
        }

        // Populate block info and pass to rendering engine.
        if (!$result->EOF) {
            list(
                $vars['aid'], 
                $vars['title'],
                $vars['summary'], 
                $vars['pubdate'], 
                $vars['authorid']) = $result->fields;
                $vars['pubdate'] = strftime('%d %b %y', $vars['pubdate']
            );
            if ($vars['showauthor']) {
                $author = xarModAPIFunc(
                    'roles', 'user', 'get',
                    array('uid' => $vars['authorid'])
                );
                $vars['authorname'] = $author['name'];
            }
        }

        // Write to cache
        // TODO: handle cache in core?
        if ($vars['refreshtime'] > 0) {
            $fp = fopen("$cachedFileName", "wt");
            fwrite($fp, serialize($vars));
            fclose($fp);
        }
    } /* check cache */

    // Pass details back for rendering.
    if (!empty($vars['aid'])) {
        $blockinfo['content'] = $vars;
        return $blockinfo;
    }

    // Nothing to render.
    return;
}


/**
 * modify block settings
 */
 // TODO: move this to a separate script.
function articles_randomblock_modify($blockinfo)
{
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    //$vars = _articles_randomblock_checkdefaults($vars);

    $vars['pubtypes'] = xarModAPIFunc('articles', 'user', 'getpubtypes');
    $vars['categorylist'] = xarModAPIFunc('categories', 'user', 'getcat');
    $vars['statusoptions'] = array(
        array('id' => '3,2', 'name' => xarML('All Published')),
        array('id' => '3', 'name' => xarML('Frontpage')),
        array('id' => '2', 'name' => xarML('Approved'))
    );

    // TODO: provide core alternative to doing this.
    $vars['blockid'] = $blockinfo['bid'];

    // Return output
    return $vars;
}

/**
 * update block settings
 */
function articles_randomblock_update($blockinfo)
{
    if (!xarVarFetch('ptid', 'id', $vars['ptid'], '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('cid', 'id', $vars['cid'], '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('status', 'strlist:,:int:1:3', $vars['status'], '3,2', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('refreshtime', 'int:0:', $vars['refreshtime'], 60*24, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('showtitle', 'checkbox', $vars['showtitle'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('showpubdate', 'checkbox', $vars['showpubdate'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('showauthor', 'checkbox', $vars['showauthor'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('showsubmit', 'checkbox', $vars['showsubmit'], false, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('showsummary', 'checkbox', $vars['showsummary'], false, XARVAR_NOT_REQUIRED)) {return;}

    $blockinfo['content'] = $vars;

    // Clear the cache.
    // TODO: centralise this facility for all blocks.
    $varDir = xarCoreGetVarDirPath();
    $cacheKey = md5('block-articles-random-' . $blockinfo['bid']);
    $cachedFileName = $varDir . '/cache/templates/' . $cacheKey;
    if ($vars['refreshtime'] > 0 && file_exists($cachedFileName)) {
        // Delete the file.
        @unlink($cachedFileName);
    }

    return $blockinfo;
}

?>