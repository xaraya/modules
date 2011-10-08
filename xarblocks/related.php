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
        public $numitems          = 5;
        public $showvalue         = true;
        public $nocache           = 1; // don't cache by default
        public $usershared        = 1; // share across group members
        public $pageshared        = 0; // don't share across pages
        public $showauthor        = false;
        public $showsubmit        = false;
        public $state             = '2,3';

        public function __construct(Array $data=array())
        {
            parent::__construct($data);
            $this->text_type = 'Related publication';
            $this->text_type_long = 'Show related categories and author links';
            $this->allow_multiple = true;
            $this->form_content   = false;
            $this->form_refresh   = false;
            $this->show_preview   = true;
            
            $this->show_pubtype  = true;
            $this->show_category = true;
            $this->show_author   = true;
        }

        public function display(Array $data=array())
        {
            $data = parent::display($data);
            $vars = $data['content'];
            
            if (empty($vars['numitems']))         $vars['numitems'] = $this->numitems;
            if (empty($vars['showvalue']))        $vars['showvalue'] = $this->showvalue;
            if (empty($vars['showpubtype']))      $vars['showpubtype'] = $this->show_pubtype;
            if (empty($vars['showcategory']))     $vars['showcategory'] = $this->show_category;
            if (empty($vars['showauthor']))       $vars['showauthor'] = $this->show_author;

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
                // Set the data to return.
                $data['content'] = $vars;
                return $data;
            }
        
            return;
        }
    }
?>