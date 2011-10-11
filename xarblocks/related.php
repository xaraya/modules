<?php
/**
 * Articles module related articles block
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
 * Original Author of file: Jim McDonald
 */
/**
 * initialise block
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class Articles_RelatedBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'related';
    protected $module           = 'articles'; // module block type belongs to, if any
    protected $text_type        = 'Related Articles';  // Block type display name
    protected $text_type_long   = 'Show related categories and author links'; // Block type description
    // Additional info, supplied by developer, optional 
    protected $type_category    = 'block'; // options [(block)|group] 
    protected $author           = '';
    protected $contact          = '';
    protected $credits          = '';
    protected $license          = '';
    
    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    // @todo: drop the show_help flag, and go back to checking if help method is declared 
    protected $show_help    = false; // let the subsystem know if this block type has a help() method

    public $numitems    = 5;
    public $showvalue   = true;

/**
 * Display func.
 * @param $data array containing title,content
 */
    function display()
    {
        $vars = $this->getContent();

        // Defaults
        if (empty($vars['numitems'])) {
            $vars['numitems'] = 5;
        }
        if (empty($vars['showvalue'])) {
            $vars['showvalue'] = false;
        }

        // Trick : work with cached variables here (set by the module function)

        // Check if we've been through articles display
        if (!xarVarIsCached('Blocks.articles','aid')) {
            return;
        }

        $pubtypes = xarMod::apiFunc('articles','user','getpubtypes');

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
                                                array('ptid' => (!empty($ptid) ? $ptid : null),
                                                      'authorid' => $authorid));
                $vars['authorname'] = $author;
                $vars['authorid'] = $authorid;
                if (!empty($vars['showvalue'])) {
                    $vars['authorcount'] = xarMod::apiFunc('articles','user','countitems',
                                                         array('ptid' => (!empty($ptid) ? $ptid : null),
                                                               'authorid' => $authorid,
                                                               // limit to approved / frontpage articles
                                                               'status' => array(2,3),
                                                               'enddate' => time()));
                }
                $links++;
            }
        }

        // Populate block info and pass to theme
        if ($links > 0) {
            return $vars;
        }

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
        // Defaults
        if (empty($data['numitems'])) {
            $data['numitems'] = 5;
        }
        if (empty($data['showvalue'])) {
            $data['showvalue'] = false;
        }

        // Return output
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

        if (!xarVarFetch('numitems', 'int:1:100', $vars['numitems'], 5, XARVAR_NOT_REQUIRED)) {return;}
        if (!xarVarFetch('showvalue', 'checkbox', $vars['showvalue'], false, XARVAR_NOT_REQUIRED)) {return;}
        
        $this->setContent($vars);
        return true;
    }

}
?>