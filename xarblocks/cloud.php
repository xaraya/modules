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

    public $name                = 'CloudBlock';
    public $module              = 'headlines';
    public $text_type           = 'RSS Cloud';
    public $text_type_long      = 'RSS Cloud';
    public $pageshared          = 1;
    public $usershared          = 1;
    public $nocache             = 1;

    public $rssurl              = '';
    public $maxitems            = 5;
    public $showdescriptions    = false;

/**
 * Display func.
 * @param $data array containing title,content
 */
    function display(Array $data=array())
    {
        $data = parent::display($data);
        if (empty($data)) return;

        $vars = $data['content'];

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

        $data['content'] = $vars;

        return $data;
    }

/**
 * Modify func.
 * @param $data array containing title,content
 */
    function modify(Array $data=array())
    {
        return parent::modify($data);
    }

/**
 * Update func.
 * @param $data array containing title,content
 */
    function update(Array $data=array())
    {
        $data = parent::update($data);
        if (empty($data)) return;

        $vars = array();
        if (!xarVarFetch('maxitems', 'int:0', $vars['maxitems'], 5, XARVAR_NOT_REQUIRED)) {return;}
        if (!xarVarFetch('showdescriptions', 'checkbox', $vars['showdescriptions'], XARVAR_NOT_REQUIRED)) {return;}
        $data['content'] = $vars;
        return $data;

    }

}
?>