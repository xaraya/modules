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
    return true;
}

/**
 * get information on block
 */
function articles_randomblock_info()
{
    // Values
    return array('text_type' => 'Random article',
                 'module' => 'articles',
                 'text_type_long' => 'Show a single random article',
                 'allow_multiple' => true,
                 'form_content' => false,
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
    if(!xarSecurityCheck('ReadArticlesBlock',1,'Block',$blockinfo['title'])) return;

    // Get variables from content block
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    $vars = _articles_randomblock_checkdefaults($vars);

    // Allow refresh by setting refreshrandom variable
    if (xarVarCleanFromInput('refreshrandom') == 1) {
        $vars['refreshtime'] = 0;
    }

    // Check cache 
    $refresh = (time() - ($vars['refreshtime'] * 60));
    $varDir = xarCoreGetVarDirPath();
    $cacheKey = md5($blockinfo['bid']);
    $cachedFileName = $varDir . '/cache/templates/' . $cacheKey;
    if ((file_exists($cachedFileName)) && 
           (filemtime($cachedFileName) > $refresh)) {
        $fp = @fopen($cachedFileName, 'r');

        // Read From Our Cache
        $vars = unserialize(fread($fp, filesize($cachedFileName)));
        fclose($fp);
    } else {

	// count number of articles and pick a random number
	$count = xarModAPIFunc('articles', 'user', 'countitems', array(
	     'ptid' => $vars['ptid'],
	     'cid' => $vars['cid'],
	     'status' => explode(",", $vars['status'])));
	mt_srand((double) microtime() * 1000000);

	// don't show template if there are no articles
	if ($count == 0) return;
	$random =  mt_rand(0, $count) - 1;

	// Database information
	xarModDBInfoLoad('articles');
	$dbconn =& xarDBGetConn();
	$xartable =& xarDBGetTables();
	$articlestable = $xartable['articles'];

	// Create WHERE field from non empty parameters
	$andlist = array();
        $orlist = array();
	foreach (explode(",", $vars['status']) as $status) {
	   $orlist[] = "xar_status='".xarVarPrepForStore($status)."'";
	}
	if (!empty($vars['ptid'])) {
	   $andlist[] = "xar_pubtypeid='".xarVarPrepForStore($vars['ptid'])."'";
	}
    /* TODO fix to check categories
	if (!empty($vars['cid'])) {
	   $andlist[] = "cid='".xarVarPrepForStore($vars['cid'])."'";
	}*/

    // create sql where string from AND and OR lists
	if (count($andlist) > 0) {
	    $wherestring = "WHERE " . join(' AND ', $andlist);
            if (count($orlist) > 0) {
                $wherestring .= ' AND (' . join(' OR ', $orlist) . ")";
            }
	} else {
            if (count($orlist) > 0) {
                $wherestring = 'WHERE ' . join(' OR ', $orlist);
            }
	}

	// Create SQL query
	$query = "SELECT xar_aid,
			 xar_title,
			 xar_summary,
			 xar_pubdate,
			 xar_authorid
			 FROM $articlestable
			 $wherestring";
        $msg = xarML("Getting random article #(1) of #(2) with: ", 
            $random, $count) . $query;
        xarLogMessage($msg, XARLOG_LEVEL_DEBUG);

	// Execute
	$result =& $dbconn->SelectLimit($query, 1, $random);
	if (!$result) {
	    return;
	}

	// Populate block info and pass to theme
	if (!$result->EOF) {
	    list($vars['aid'], 
		 $vars['title'],
		 $vars['summary'], 
		 $vars['pubdate'], 
		 $vars['authorid']) = $result->fields;
            $vars['pubdate'] = strftime('%d %b %y', $vars['pubdate']);
	    if ($vars['showauthor']) {
		$author = xarModAPIFunc('roles', 'user', 'get', array(
		     'uid' => $vars['authorid']));
		$vars['authorname'] = $author['name'];
	    }
	}

        // write to cache
        $fp = fopen("$cachedFileName", "wt");
        fwrite($fp, serialize($vars));
        fclose($fp);

    } /* check cache */

    // Pass to template
    if (!empty($vars['aid'])) {
        if (empty($blockinfo['template'])) {
            $template = 'random';
        } else {
            $template = $blockinfo['template'];
        }
        $blockinfo['content'] = xarTplBlock('articles', $template, $vars);

        return $blockinfo;
    } else {
        return;
    }
}


/**
 * modify block settings
 */
function articles_randomblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    $vars = _articles_randomblock_checkdefaults($vars);

    $vars['pubtypes'] = xarModAPIFunc('articles','user','getpubtypes');
    $vars['categorylist'] = xarModAPIFunc('categories','user','getcat');
    $vars['statusoptions'] = array(array('id' => '3,2',
                                         'name' => xarML('All Published')),
                                   array('id' => '3',
                                         'name' => xarML('Frontpage')),
                                   array('id' => '2',
                                         'name' => xarML('Approved'))
                                  );

    $vars['blockid'] = $blockinfo['bid'];

    // Return output
    return xarTplBlock('articles','randomAdmin', $vars);
}

/**
 * update block settings
 */
function articles_randomblock_update($blockinfo)
{
    //MikeC: Make sure we retrieve the new pubtype from the configuration form.
    list($vars['ptid'],
         $vars['cid'],
         $vars['status'],
         $vars['refreshtime'],
         $vars['showtitle'],
         $vars['showpubdate'],
         $vars['showauthor'],
         $vars['showsubmit'],
         $vars['showsummary']
                           ) = xarVarCleanFromInput('ptid',
                                                    'cid',
                                                    'status',
                                                    'refreshtime',
                                                    'showtitle',
                                                    'showpubdate',
                                                    'showsubmit',
                                                    'showauthor',
                                                    'showsummary'
                                                   );

    /* unchecked variables may not be sent by the browser */
    if (empty($vars['showpubdate'])) {
        $vars['showpubdate'] = 0;
    }

    if (empty($vars['showauthor'])) {
        $vars['showauthor'] = 0;
    }

    if (empty($vars['showtitle'])) {
        $vars['showtitle'] = 0;
    }

    if (empty($vars['showsummary'])) {
        $vars['showsummary'] = 0;
    }

    if (empty($vars['showsubmit'])) {
        $vars['showsubmit'] = 0;
    }

    $vars = _articles_randomblock_checkdefaults($vars);
    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}

/**
 * Makes sure all the required variables are set to display or modify the block
 */
function _articles_randomblock_checkdefaults($vars)
{
    if (empty($vars['ptid'])) {
        $vars['ptid'] = '';
    }

    if (empty($vars['cid'])) {
        $vars['cid'] = '';
    }

    if (empty($vars['status'])) {
        $vars['status'] = '3,2';
    }

    if (empty($vars['showpubdate'])) {
        $vars['showpubdate'] = 0;
    }

    if (empty($vars['showauthor'])) {
        $vars['showauthor'] = 0;
    }

    if (empty($vars['showsubmit'])) {
        $vars['showsubmit'] = 0;
    }

    /* don't use empty() because 0 is a valid value */
    if (!array_key_exists('refreshtime', $vars) ||
        !isset($vars['refreshtime'])) {
        $vars['refreshtime'] = 1440; // one day
    }

    if (!array_key_exists('showsummary', $vars) ||
        !isset($vars['showsummary'])) {
        $vars['showsummary'] = 1;
    }

    if (!array_key_exists('showtitle', $vars) ||
        !isset($vars['showtitle'])) {
        $vars['showtitle'] = 1;
    }

    return $vars;
}

?>
