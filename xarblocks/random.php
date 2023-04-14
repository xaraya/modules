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
 * initialise block
 * @author Roger Keays
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class Publications_RandomBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'random';
    protected $module           = 'publications'; // module block type belongs to, if any
    protected $text_type        = 'Random Publication';  // Block type display name
    protected $text_type_long   = 'Show a single random publication'; // Block type description
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

    public $numitems          = 1;
    public $catfilter         = array();
    public $pubtype_id        = 0;
    public $locale            = '';
    public $alttitle          = '';
    public $altsummary        = '';
    public $showtitle         = true;
    public $showsummary       = true;
    public $showpubdate       = false;
    public $showauthor        = false;
    public $showsubmit        = false;
    // chris: state is a reserved property name used by blocks
    //public $state               = '2,3';
    public $pubstate            = '2,3';

        public function display()
        {
            $data = $this->getContent();

            // frontpage or approved state
            if (empty($data['pubstate']))          $statearray = $this->state;
            elseif (!is_array($data['pubstate']))  $statearray = explode(',', $data['pubstate']);
            else                                $statearray = $data['pubstate'];

            if (empty($data['locale']))             $lang = null;
            elseif ($data['locale'] == 'current')   $lang = xarMLS::setCurrentLocale();
            else                                    $lang = $data['locale'];

            // get cids for security check in getall
            $fields = array('id', 'title', 'body', 'notes', 'pubtype_id', 'cids', 'owner');

            if (!empty($data['showpubdate'])) array_push($fields, 'pubdate');
            if (!empty($data['showsummary'])) array_push($fields, 'summary');
            if (!empty($data['showauthor'])) array_push($fields, 'owner');
            if (!empty($data['alttitle'])) $blockinfo['title'] = $data['alttitle'];
            if (empty($data['pubtype_id'])) $data['pubtype_id'] = 0;

            if (!empty($data['catfilter'])) {
                // use admin defined category
                $cidsarray = array($data['catfilter']);
                $cid = $data['catfilter'];
            } else {
                $cid = 0;
                $cidsarray = array();
            }

            // check if dynamicdata is hooked for all pubtypes or the current one (= defaults to 0 anyway here)
            if (!empty($data['showdynamic']) && xarModHooks::isHooked('dynamicdata', 'publications', $data['pubtype_id'])) {
                array_push($fields, 'dynamicdata');
            }

            if (empty($data['numitems'])) $data['numitems'] = 1;

            $publications = xarMod::apiFunc('publications','user','getrandom',
                                      array('ptid'     => $data['pubtype_id'],
                                            'cids'     => $cidsarray,
                                            'andcids'  => false,
                                            'state'   => $statearray,
                                            'locale' => $lang,
                                            'numitems' => $data['numitems'],
                                            'fields'   => $fields,
                                            'unique'   => true));

            if (!isset($publications) || !is_array($publications) || count($publications) == 0) {
                return;
            } else {
                foreach (array_keys($publications) as $key) {
                    // for template compatibility :-(
                    if (!empty($publications[$key]['author']) && !empty($data['showauthor'])) {
                        $publications[$key]['authorname'] = $publications[$key]['author'];
                    }
                    $data['items'][] = $publications[$key];
                }
            }

            return;$data;
        }
}
?>