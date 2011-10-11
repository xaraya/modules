<?php
/**
 * Random Block
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 *
 */
/**
 * initialise block
 * @author Roger Keays
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class Articles_RandomBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'random';
    protected $module           = 'articles'; // module block type belongs to, if any
    protected $text_type        = 'Random Article';  // Block type display name
    protected $text_type_long   = 'Show a random article'; // Block type description
    // Additional info, supplied by developer, optional 
    protected $type_category    = 'block'; // options [(block)|group] 
    protected $author           = '';
    protected $contact          = '';
    protected $credits          = '';
    protected $license          = '';
    
    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    protected $show_help    = false; // let the subsystem know if this block type has a help() method

    public $pubtypeid     = 0;
    public $catfilter     = 0;
    public $status        = '3,2';
    public $language      = '';
    public $numitems      = 1;
    public $alttitle      = '';
    public $altsummary    = '';
    public $showtitle     = true;
    public $showsummary   = true;
    public $showpubdate   = false;
    public $showsubmit    = false;
    public $showdynamic   = false;
    public $linkpubtype   = false;

/**
 * Display func.
 * @param $data array containing title,content
 */
    function display()
    {
        $vars = $this->getContent();

        // frontpage or approved status
        if (empty($vars['status'])) {
                $statusarray = array(2,3);
        } elseif (!is_array($vars['status'])) {
                $statusarray = split(',', $vars['status']);
        } else {
                $statusarray = $vars['status'];
        }

        if (empty($vars['language'])) {
            $lang = null;
        } elseif ($vars['language'] == 'current') {
            $lang = xarMLSGetCurrentLocale();
        } else {
            $lang = $vars['language'];
        }

        // get cids for security check in getall
        $fields = array('aid', 'title', 'body', 'notes', 'pubtypeid', 'cids', 'authorid');

        if (!empty($vars['showpubdate'])) {
            array_push($fields, 'pubdate');
        }
        if (!empty($vars['showsummary'])) {
            array_push($fields, 'summary');
        }
        if (!empty($vars['showauthor'])) {
            array_push($fields, 'author');
        }
        if (!empty($vars['alttitle'])) {
            $blockinfo['title'] = $vars['alttitle'];
        }
        if (empty($vars['pubtypeid'])) {
            $vars['pubtypeid'] = 0;
        }

        if (!empty($vars['catfilter'])) {
            // use admin defined category
            $cidsarray = array($vars['catfilter']);
            $cid = $vars['catfilter'];
        } else {
            $cid = 0;
            $cidsarray = array();
        }

        // check if dynamicdata is hooked for all pubtypes or the current one (= defaults to 0 anyway here)
        if (!empty($vars['showdynamic']) && xarModIsHooked('dynamicdata', 'articles', $vars['pubtypeid'])) {
            array_push($fields, 'dynamicdata');
        }

        if (empty($vars['numitems'])) $vars['numitems'] = 1;

        $articles = xarMod::apiFunc('articles','user','getrandom',
                                  array('ptid'     => $vars['pubtypeid'],
                                        'cids'     => $cidsarray,
                                        'andcids'  => false,
                                        'status'   => $statusarray,
                                        'language' => $lang,
                                        'numitems' => $vars['numitems'],
                                        'fields'   => $fields,
                                        'unique'   => true));

        if (!isset($articles) || !is_array($articles) || count($articles) == 0) {
            return;
        } else {
            foreach (array_keys($articles) as $key) {
                // for template compatibility :-(
                if (!empty($articles[$key]['author']) && !empty($vars['showauthor'])) {
                    $articles[$key]['authorname'] = $articles[$key]['author'];
                }
                $vars['items'][] = $articles[$key];
            }
        }

        // Pass details back for rendering.
        if (count($vars['items']) > 0) {
            return $vars;
        }

        // Nothing to render.
        return;
    }

/**
 * Modify Function to the Blocks Admin
 * @param $data array containing title,content
 * @TODO: Move this to block_admin after 2.1.0
 */
    public function modify()
    {
        $data = $this->getContent();

        $data['pubtypes'] = xarMod::apiFunc('articles', 'user', 'getpubtypes');
        $data['categorylist'] = xarMod::apiFunc('categories', 'user', 'getcat');
        $data['statusoptions'] = array(
            array('id' => '', 'name' => xarML('All Published')),
            array('id' => '3', 'name' => xarML('Frontpage')),
            array('id' => '2', 'name' => xarML('Approved'))
        );
        if(!empty($data['catfilter'])) {
            $cidsarray = array($data['catfilter']);
        } else {
            $cidsarray = array();
        }

        $data['locales'] = xarMLSListSiteLocales();
        asort($data['locales']);
        return $data;
    }

/**
 * Updates the Block config from the Blocks Admin
 * @param $data array containing title,content
 * @TODO: Move this to block_admin after 2.1.0
 */
    public function update()
    {
        $vars = array();

        xarVarFetch('pubtypeid', 'id', $vars['pubtypeid'], 0, XARVAR_NOT_REQUIRED);
        xarVarFetch('catfilter', 'id', $vars['catfilter'], 0, XARVAR_NOT_REQUIRED);
        xarVarFetch('status', 'int:0:4', $vars['status'], NULL, XARVAR_NOT_REQUIRED);
        xarVarFetch('language', 'str', $vars['language'], '', XARVAR_NOT_REQUIRED);
        xarVarFetch('alttitle', 'str', $vars['alttitle'], '', XARVAR_NOT_REQUIRED);
        xarVarFetch('altsummary', 'str', $vars['altsummary'], '', XARVAR_NOT_REQUIRED);
        if (!xarVarFetch('numitems', 'int:1:100', $vars['numitems'], 5, XARVAR_NOT_REQUIRED)) {return;}
        xarVarFetch('showtitle', 'checkbox', $vars['showtitle'], false, XARVAR_NOT_REQUIRED);
        xarVarFetch('showsummary', 'checkbox', $vars['showsummary'], false, XARVAR_NOT_REQUIRED);
        xarVarFetch('showpubdate', 'checkbox', $vars['showpubdate'], false, XARVAR_NOT_REQUIRED);
        xarVarFetch('showauthor', 'checkbox', $vars['showauthor'], false, XARVAR_NOT_REQUIRED);
        xarVarFetch('showsubmit', 'checkbox', $vars['showsubmit'], false, XARVAR_NOT_REQUIRED);
        xarVarFetch('showdynamic', 'checkbox', $vars['showdynamic'], false, XARVAR_NOT_REQUIRED);
        xarVarFetch('linkpubtype', 'checkbox', $vars['linkpubtype'], false, XARVAR_NOT_REQUIRED);

        $this->setContent($vars);
        return true;
    }

}
?>