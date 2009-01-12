<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * Original Author of file: Jim McDonald
 */
/**
 * initialise block
 */
    sys::import('xaraya.structures.containers.blocks.basicblock');

class Publications_RelatedBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'related';
    protected $module           = 'publications'; // module block type belongs to, if any
    protected $text_type        = 'Related publication';  // Block type display name
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

    public $numitems          = 5;
    public $showvalue         = true;
    public $showsubmit        = false;
    public $showpubtype      = true;
    public $showcategory     = true;
    public $showauthor       = true;
    // chris: state is a reserved property name used by blocks
    //public $state               = '2,3';
    public $pubstate            = '2,3';


        public function display()
        {

            $vars = $this->getContent();
            
            // Trick : work with cached variables here (set by the module function)        
            // Check if we've been through publications display
            if (!xarVarIsCached('Blocks.publications','current_id')) {return;}

            $links = 0;
            
            if ($vars['showpubtype']) {
                // Show publication type (for now)
                $pubtypes = xarModAPIFunc('publications','user','get_pubtypes');
                if (xarVarIsCached('Blocks.publications','ptid')) {
                    $ptid = xarVarGetCached('Blocks.publications','ptid');
                    if (!empty($ptid) && isset($pubtypes[$ptid]['description'])) {
                        $vars['pubtypelink'] = xarModURL('publications','user','view',
                                                         array('ptid' => $ptid));
                        $vars['pubtypename'] = $pubtypes[$ptid]['description'];
                        $links++;
                    }
                }
            }
            
            if ($vars['showcategory']) {
                // Show categories (for now)
                if (xarVarIsCached('Blocks.publications','cids')) {
                    $cids = xarVarGetCached('Blocks.publications','cids');
                    // TODO: add related links
                }
            }
            
            if ($vars['showauthor']) {
                // Show author (for now)
                if (xarVarIsCached('Blocks.publications','author')) {
                    $author = xarVarGetCached('Blocks.publications','author');
                    if (!empty($author)) {
                        $vars['authorlink'] = xarModURL('publications','user','view',
                                                        array('ptid' => (!empty($ptid) ? $ptid : null),
                                                              'owner' => $author));
                        $vars['authorid'] = $author;
                        if (!empty($vars['showvalue'])) {
                            $vars['authorcount'] = xarModAPIFunc('publications','user','countitems',
                                                                 array('ptid' => (!empty($ptid) ? $ptid : null),
                                                                       'owner' => $author,
                                                                       // limit to approved / frontpage publications
                                                                       'state' => array(2,3),
                                                                       'enddate' => time()));
                        }
                        $links++;
                    }
                }
            }

            // Populate block info and pass to theme
            if ($links > 0) {
                return $vars;
            }
        
            return;
        }
}
?>