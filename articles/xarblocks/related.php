<?php
// File: $Id: s.related.php 1.10 03/01/14 22:12:32+00:00 mikespub@sasquatch.pulpcontent.com $
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file: Articles Block
// ----------------------------------------------------------------------

/**
 * initialise block
 */
function articles_relatedblock_init()
{
    return array(
        'numitems' => 5
    );
}

/**
 * get information on block
 */
function articles_relatedblock_info()
{
    // Values
    return array(
        'text_type' => 'Related',
        'module' => 'articles',
        'text_type_long' => 'Show related categories and author links',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * display block
 */
function articles_relatedblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ReadArticlesBlock', 1, 'Block', $blockinfo['title'])) {return;}

    // Get variables from content block
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }

    // Trick : work with cached variables here (set by the module function)

    // Check if we've been through articles display
    if (!xarVarIsCached('Blocks.articles','aid')) {
        return;
    }

    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

    $links = 0;
    // Show publication type (for now)
    if (xarVarIsCached('Blocks.articles','ptid')) {
        $ptid = xarVarGetCached('Blocks.articles','ptid');
        if (!empty($ptid) && isset($pubtypes[$ptid]['descr'])) {
            $vars['pubtypelink'] = xarModURL('articles','user','view',
                                             array('ptid' => $ptid));
            $vars['pubtypename'] = $pubtypes[$ptid]['descr'];
            $links++;
        }
    }
    // Show categories (for now)
    if (xarVarIsCached('Blocks.articles','cids')) {
        $cids = xarVarGetCached('Blocks.articles','cids');
        // TODO: add related links
    }
    // Show author (for now)
    if (xarVarIsCached('Blocks.articles','authorid') &&
        xarVarIsCached('Blocks.articles','author')) {
        $authorid = xarVarGetCached('Blocks.articles','authorid');
        $author = xarVarGetCached('Blocks.articles','author');
        if (!empty($authorid) && !empty($author)) {
            $vars['authorlink'] = xarModURL('articles','user','view',
                                            array('authorid' => $authorid));
            $vars['authorname'] = $author;
            $links++;
        }
    }

    $vars['blockid'] = $blockinfo['bid'];

    // Populate block info and pass to theme
    if ($links > 0) {
        $blockinfo['content'] = $vars;
        return $blockinfo;
    }

    return;
}


/**
 * modify block settings
 */
function articles_relatedblock_modify($blockinfo)
{
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }

    $vars['bid'] = $blockinfo['bid'];

    // Return output
    return $vars;
}

/**
 * update block settings
 */
function articles_relatedblock_update($blockinfo)
{
    $vars = array();
    if (!xarVarFetch('numitems', 'int:1', $numitems, 5, XARVAR_NOT_REQUIRED)) {return;}

    $vars['numitems'] = $numitems;

    $blockinfo['content'] = $vars;

    return $blockinfo;
}

/**
 * built-in block help/information system.
 */
function articles_relatedblock_help()
{
    return '';
}

/*
Reminder of what this block used to do :-)

// ----------------------------------------------------------------------
// Original Author of file: Patrick Kellum
// Purpose of file: Display releated stories.  Only displayed when reading articles.
// ----------------------------------------------------------------------

$blocks_modules['related'] = array(
    'func_display' => 'blocks_related_block',
    'text_type' => 'Related',
    'text_type_long' => 'Story Related Links',
    'allow_multiple' => false,
    'form_content' => false,
    'form_refresh' => false,
    'show_preview' => false
);

function blocks_related_block($row)
{
    global $sid,
           $story // set by advarticle.php
           ;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    if(!xarSecurityCheck('ReadArticlesBlock',1,'Block',$row['title'])) return;

    if($story['topic']) {
        $row['content'] = '<span class="xar-normal">';
        $column = &$xartable['stories_column'];
        $sql = "SELECT $column[sid] as sid, $column[title] as title FROM $xartable[stories] WHERE $column[topic]=".xarVarPrepForStore($story['topic'])." ORDER BY $column[counter] DESC";
        $result = $dbconn->SelectLimit($sql,1);
        $mrow = $result->GetRowAssoc(false);
        $result->MoveNext();
        $column = &$xartable['related_column'];
        $result = $dbconn->Execute("SELECT $column[name] as name, $column[url] as url FROM $xartable[related] WHERE $column[tid]=".xarVarPrepForStore($story['topic'])."");
        while(!$result->EOF) {
            $lrow = $result->GetRowAssoc(false);
            $result->MoveNext();
            $row['content'] .= "&bull;&nbsp;<a href=\"$lrow[url]\" target=\"_blank\">".xarVarPrepForDisplay($lrow['name'])."</a><br />\n";
        }
        $row['content'] .= "&bull;&nbsp;<a href=\"advtopics.php?topic=$story[topic]\">"._MOREABOUT." ".xarVarPrepForDisplay($story['topicname'])."</a><br />\n"
            ."&bull;&nbsp;<a class=\"xar-normal\" href=\"modules.php?op=modload&amp;name=Search&amp;file=index&amp;action=search&amp;overview=1&amp;active_stories=1&amp;stories_author=$story[aid]\">"._NEWSBY." ".xarVarPrepForDisplay($story['aid'])."</a><br />\n"
            .'</span><br /><hr noshade="noshade" width="95%" size="1"><b>'._MOSTREAD." ".xarVarPrepForDisplay($story['topicname']).":</b><br />\n"
            ."<div align=\"center\"><a href=\"advarticle.php?sid=$mrow[sid]\">".xarVarPrepForDisplay($mrow['title'])."</a></div><br /><br />\n"
            .'<div align="right">'
            ."<a href=\"print.php?sid=$mrow[sid]\"><img src=\"images/global/print.gif\" border=\"0\" alt=\""._PRINTER."\" /></a>&nbsp;&nbsp;"
            ."<a class=\"xar-normal\" href=\"modules.php?op=modload&amp;name=Recommend_Us&amp;file=index&amp;req=FriendSend&amp;sid=$sid\"><img src=\"images/global/friend.gif\" border=\"0\" Alt=\""._FRIEND."\" /></a>\n"
            .'</div>'
        ;
        return themesideblock($row);
    }
}
*/

?>
