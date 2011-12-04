<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * Initialise block
 *
 * Original Author of file:Camille Perinel
 * Mostly taken from the topitems.php block of the articles module.(See credits)
 * @return bool true on success
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class Keywords_KeywordsarticlesBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'keywordsarticles';
    protected $module           = 'keywords'; // module block type belongs to, if any
    protected $text_type        = 'Keywords Articles';  // Block type display name
    protected $text_type_long   = 'Show articles related by keywords'; // Block type description
    // Additional info, supplied by developer, optional
    protected $type_category    = 'block'; // options [(block)|group]
    protected $author           = '';
    protected $contact          = '';
    protected $credits          = '';
    protected $license          = '';

    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    protected $show_help    = false; // let the subsystem know if this block type has a help() method

    public $ptid = '';
    public $cid = '';
    public $status = '2,3';
    public $refreshtime = 1440;

    public function display()
    {
        $vars = $this->getContent();
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
        //Get the keywords related articles
        if (xarVarIsCached('Blocks.articles', 'aid')) {
            $vars['itemid'] = xarVarGetCached('Blocks.articles','aid');
            $itemtype = xarVarGetCached('Blocks.articles', 'ptid');
            if (!empty($itemtype) && is_numeric($itemtype)) {
                $vars['itemtype'] = $itemtype;
            } else {
                $article = xarModAPIFunc('articles','user','get',
                                       array('aid' => $vars['itemid']));
                $vars['itemtype'] = $article['pubtypeid'];
            }
            $vars['modid'] = xarModGetIDFromName('articles');
            $keywords = xarModAPIFunc('keywords','user','getwords',
                                   array('itemid' => $vars['itemid'],
                                            'itemtype' => $vars['itemtype'],
                                            'modid' => $vars['modid']));
             if (empty($keywords) || !is_array($keywords) || count($keywords) == 0) return '';
            //for each keyword in keywords[]
            $items = array();
            $vars['items'] = array();
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
                    if ($vars['itemid'] != $item['itemid'] || $vars['modid'] != $item['moduleid']
                    || $vars['itemtype'] != $item['itemtype']) {
                    if ( $articles = xarModAPIFunc('articles','user','get',
                                       array('aid' => $item['itemid']))) {
                         //TODO : display config
                        //'aid','title','summary','authorid', 'pubdate','pubtypeid','notes','status','body'
                        //if the related article already exist do not add it
                        if (stristr($vars['status'], $articles['status'])) {
                            $vars['items'][] = array(
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
    return $vars;
    }

}
?>
