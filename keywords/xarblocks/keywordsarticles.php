<?php
// File: keywordsarticles.php
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file:Camille Perinel
// Mostly taken from the topitems.php block of the articles module.(See credits)
// Purpose of file: Keyword Articles Block
// ----------------------------------------------------------------------

/**
 * initialise block
 */

function keywords_keywordsarticlesblock_init()
{
    return true;
}

/**
 * get information on block
 */

function keywords_keywordsarticlesblock_info()
{
    // Details of block.
    return array(
        'text_type'         => 'Keywords Articles',
        'module'            => 'keywords',
        'text_type_long'    => 'Show articles related by keywords',
        'allow_multiple'    => true,
        'form_content'      => false,
        'form_refresh'      => false,
        'show_preview'      => false
    );
}

/**
 * display block
 */

function keywords_keywordsarticlesblock_display(& $blockinfo)
{
    // Security check
    if(!xarSecurityCheck('ReadKeywords')) return;

    // Get variables from content block
    $vars = @unserialize($blockinfo['content']);
    // Defaults
    //$vars = _keywords_keywordsarticlesblock_checkdefaults($vars);

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
        //Get the keywords related articles
        if (xarVarIsCached('Blocks.articles', 'aid')) {
            $data['itemid'] = xarVarGetCached('Blocks.articles','aid');
            $itemtype = xarVarGetCached('Blocks.articles', 'ptid');
            if (!empty($itemtype) && is_numeric($itemtype)) {
                $data['itemtype'] = $itemtype;
            } else {
                $article = xarModAPIFunc('articles','user','get',
                                       array('aid' => $data['itemid']));
                $data['itemtype'] = $article['pubtypeid'];
            }
            $data['modid'] = xarModGetIDFromName('articles');
            $keywords = xarModAPIFunc('keywords','user','getwords',
                                   array('itemid' => $data['itemid'],
                                            'itemtype' => $data['itemtype'],
                                            'modid' => $data['modid']));
             if (empty($keywords) || !is_array($keywords) || count($keywords) == 0) return '';
            //for each keyword in keywords[]
            $items = array();
            $data['items'] = array();
            foreach ($keywords as $id => $word) {
               //$item['id'] = $id;
               //$item['keyword'] = xarVarPrepForDisplay($word);
               // get the list of items to which this keyword is assigned
               //TODO Make itemtype / modid dependant
                $items = $items + xarModAPIFunc('keywords','user','getitems',
                                   array('keyword' => $word,
                                        'itemtype' => $vars['ptid']));
            }
            //make itemid unique (worst ever code)
            $tmp = array();
            $itemsB = array();
            foreach ($items as $id => $item) {
                if (!in_array($item['itemid'], $tmp) ) {
                    $tmp[] = $item['itemid'];
                    $itemsB[] = $item;
                }
            }
             foreach ($itemsB as $id => $item) {
                    if ($data['itemid'] != $item['itemid'] || $data['modid'] != $item['moduleid']
                    || $data['itemtype'] != $item['itemtype']) {
                    if ( $articles = xarModAPIFunc('articles','user','get',
                                       array('aid' => $item['itemid']))) {
                         //TODO : display config
                        //'aid','title','summary','authorid', 'pubdate','pubtypeid','notes','status','body'
                        //if the related article already exist do not add it
                        if (stristr($vars['status'], $articles['status'])) {
                            $data['items'][] = array(
                                        'keyword' => $item['keyword'],
                                        'modid' =>  $item['moduleid'],
                                        'itemtype' => $item['itemtype'],
                                        'itemid' => $item['itemid'],
                                        'title' => $articles['title'],
                                        'summary' => $articles['summary'],
                                        'authorid' => $articles['authorid'],
                                        'pubdate' => $articles['pubdate'],
                                        'pubtypeid' => $articles['pubtypeid'],
                                        'status' => $articles['status'],
                                        'link' => xarModURL('articles','user','display',array('aid' => $articles['aid'], 'ptid' => $articles['pubtypeid']))
                                        );
                        }
                      }
                    }
                }
        }
    }
    // Set the data to return.
    $blockinfo['content'] =& $data;
    return $blockinfo;
}

/**
 * built-in block help/information system.
 */

function keywords_keywordsarticlesblock_help()
{
    // No information yet.
    return '';
}

?>
