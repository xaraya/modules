<?php
/**
 * Displays an RSS Display.
 *
 * @package modules
 * @copyright (C) 2005-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
/**
 * @author RevJim (revjim.net), John Cox
 * @todo Make the admin selectable number of headlines work.
 * @todo show search and image of rss site
 */
/**
 * Block init - holds security.
 */
sys::import('xaraya.structures.containers.blocks.basicblock');
class Headlines_CloudBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'cloud';
    protected $module           = 'headlines'; // module block type belongs to, if any
    protected $text_type        = 'RSS Cloud';  // Block type display name
    protected $text_type_long   = 'RSS Cloud'; // Block type description
    // Additional info, supplied by developer, optional 
    protected $type_category    = 'block'; // options [(block)|group] 
    protected $author           = '';
    protected $contact          = '';
    protected $credits          = '';
    protected $license          = '';
    
    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    protected $show_help    = false; // let the subsystem know if this block type has a help() method

    public $rssurl              = '';
    public $maxitems            = 5;
    public $showdescriptions    = false;

/**
 * Display func.
 * @param $data array containing title,content
 */
    function display()
    {
        $vars = $this->getContent();

        $links = xarMod::apiFunc('headlines', 'user', 'getall',
            array(
                'numitems' => $vars['maxitems'],
                'sort' => 'date'
            )
        );

        $feedcontent = array();
    //if (empty($links)) return
        // Check individual permissions for Edit / Delete
        for ($i = 0; $i < count($links); $i++) {
            $link = $links[$i];
            // Check and see if a feed has been supplied to us.
            if (empty($link['url'])) {
                continue;
            }
            $feedfile = $link['url'];
            // TODO: make refresh configurable
            $links[$i] = xarMod::apiFunc(
                'headlines', 'user', 'getparsed',
                array('feedfile' => $feedfile)
            );
            // Check and see if a valid feed has been supplied to us.
            if (!isset($links[$i]) || isset($links[$i]['warning'])) continue;
            if (!empty($link['title'])){
                $links[$i]['chantitle'] = $link['title'];
            }
            if (!empty($link['desc'])){
                $links[$i]['chandesc'] = $link['desc'];
            }

            $feedcontent[] = array('title' => $links[$i]['chantitle'], 'url' => $links[$i]['chanlink'], 'channel' => $links[$i]['chandesc']);
        }
        $vars['feedcontent'] = $feedcontent;

        return $vars;
    }

/**
 * Modify func.
 * @param $data array containing title,content
 */
    function modify()
    {
        return $this->getContent();
    }

/**
 * Update func.
 * @param $data array containing title,content
 */
    function update()
    {
        $vars = array();
        if (!xarVarFetch('maxitems', 'int:0', $vars['maxitems'], 5, XARVAR_NOT_REQUIRED)) {return;}
        if (!xarVarFetch('showdescriptions', 'checkbox', $vars['showdescriptions'], XARVAR_NOT_REQUIRED)) {return;}
        $this->setContent($vars);
        return true;
    }

}
?>