<?php
/**
 * Twitter Module
 *
 * @package modules
 * @copyright (C) 2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */
sys::import('xaraya.structures.containers.blocks.basicblock');
class Twitter_TimelineBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'timeline';
    protected $module           = 'twitter'; // module block type belongs to, if any
    protected $text_type        = 'Twitter Timeline';  // Block type display name
    protected $text_type_long   = 'Show Twitter timeline'; // Block type description
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

    public $screen_name     = '';
    public $numitems        = 3;
    public $truncate        = 0;
    public $showimages      = false;
    public $showsource      = true;
    public $showmodule      = false;
    public $showfollow      = false;
    public $dyntitle        = false;

/**
 * Display func.
 * @param $data array containing title,content
 */
    function display()
    {
        $vars = $this->getContent();

        if (empty($vars['screen_name'])) {
            $items = xarMod::apiFunc('twitter', 'rest', 'timeline',
                array(
                    'method' => 'public_timeline',
                ));
        } else {
            $items = xarMod::apiFunc('twitter', 'rest', 'timeline',
                array(
                    'method' => 'user_timeline',
                    'screen_name' => $vars['screen_name'],
                ));
        }
        if (count($items) > $vars['numitems'])
            $items = array_slice($items, 0, $vars['numitems']);
        $vars['status_elements'] = !$items ? array() : $items;
        if (!empty($vars['dyntitle'])) {
            $title = !empty($vars['screen_name']) ? '@'.$vars['screen_name'] : xarML('Public Timeline');
            $this->setTitle($title);
        }
        return $vars;
    }
    
    public function modify()
    {
        return $this->getContent();
    }
/**
 * Updates the Block config from the Blocks Admin
 * @param $data array containing title,content
 */
    public function update()
    {
        $vars = array();
        if (!xarVarFetch('screen_name', 'str:1:20', $vars['screen_name'], '', XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('numitems', 'int', $vars['numitems'], 3, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('truncate', 'int', $vars['truncate'], 0, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showimages', 'checkbox', $vars['showimages'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showmyimage', 'checkbox', $vars['showmyimage'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showsource', 'checkbox', $vars['showsource'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showmodule', 'checkbox', $vars['showmodule'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showfollow', 'checkbox', $vars['showfollow'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('dyntitle', 'checkbox', $vars['dyntitle'], false, XARVAR_NOT_REQUIRED)) return;
        $this->setContent($vars);
        return true;
    }

}

?>