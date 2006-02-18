<?php
/**
 * Keywords Module Categories Block
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * Original Author of file:Camille Perinel
 * Mostly taken from the topitems.php block of the articles module.(See credits)
 * @TODO : Add multi categories support with + - ...
 */
/**
 * initialise block
 */

function keywords_keywordscategoriesblock_init()
{
    return true;
}

/**
 * get information on block
 * @return array
 */

function keywords_keywordscategoriesblock_info()
{
    // Details of block.
    return array(
        'text_type'         => 'Keywords Categories',
        'module'            => 'keywords',
        'text_type_long'    => 'Show categories related by keywords',
        'allow_multiple'    => true,
        'form_content'      => false,
        'form_refresh'      => false,
        'show_preview'      => false
    );
}

/**
 * display block
 * @return array with block
 */

function keywords_keywordscategoriesblock_display(& $blockinfo)
{
    // Security check
    if(!xarSecurityCheck('ReadKeywords')) return;

    // Get variables from content block
    $vars = @unserialize($blockinfo['content']);
    // Defaults
    //$vars = _keywords_keywordscategoriesblock_checkdefaults($vars);

    // Allow refresh by setting refreshrandom variable
    if (!xarVarFetch('refreshrandom', 'int:1:1', $vars['refreshtime'], 0, XARVAR_DONT_SET)) return;

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
        //Get the keywords related categories
        if (xarVarIsCached('Blocks.articles', 'cids')) {
            $data['modid'] = xarModGetIDFromName('categories');
            $data['cids'] = xarVarGetCached('Blocks.articles','cids');
            if (empty($data['cids']) || !is_array($data['cids']) || count($data['cids']) == 0) return '';

            $keywords = array();
            foreach ($data['cids'] as $id => $cid) {
                // if we're viewing all items below a certain category, i.e. catid = _NN
                $cid = str_replace('_', '', $cid);
                $keywords = xarModAPIFunc('keywords','user','getwords',
                                   array('itemid' => $cid,
                                            'modid' => $data['modid']));
            }
            if (empty($keywords) || !is_array($keywords) || count($keywords) == 0) return '';
            //for each keyword in keywords[]
            $items = array();
            $data['items'] = array();
            foreach ($keywords as $id => $word) {
               // get the list of items to which this keyword is assigned
               //TODO Make itemtype / modid dependant
                $items = $items + xarModAPIFunc('keywords','user','getitems',
                                   array('keyword' => $word,
                                        'modid' => $data['modid']));
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
                 if (!in_array($item['itemid'], $data['cids'])) {
                        $categories = xarModAPIFunc('categories','user','getcatinfo',
                                       array('cid' => $item['itemid']));
                         //TODO : display config
                        //'aid','title','summary','authorid', 'pubdate','pubtypeid','notes','status','body'
                        //if the related article already exist do not add it
                       $data['items'][] = array(
                                'keyword' => $item['keyword'],
                                'modid' =>  $item['moduleid'],
                                'itemtype' => $item['itemtype'],
                                'itemid' => $item['itemid'],
                                'name' => $categories['name'],
                                'description' => $categories['description'],
                                'image' => $categories['image'],
                                'parent' => $categories['parent'],
                                'left' => $categories['left'],
                                'right' => $categories['right'],
                                'link' => xarModURL('articles','user','view',array('cids' => array(0 => $item['itemid'])))
                                );
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

function keywords_keywordscategoriesblock_help()
{
    // No information yet.
    return '';
}

?>
