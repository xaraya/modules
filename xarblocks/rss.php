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

class Headlines_RssBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'rss';
    protected $module           = 'headlines'; // module block type belongs to, if any
    protected $text_type        = 'RSS';  // Block type display name
    protected $text_type_long   = 'RSS Newsfeed'; // Block type description
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
 
    public $rssurl              = '';
    public $maxitems            = 5;
    public $showdescriptions    = false;
    public $show_chantitle      = 1;
    public $show_chandesc       = 1;
    public $truncate            = 0;
    public $alt_chantitle       = ''; // FR - alt channel title
    public $alt_chandesc        = '';  // FR - alt channel desc
    public $alt_chanlink        = '';  // FR - alt channel link
    public $linkhid             = false;  // FR - link to headline feed
    public $show_warning        = 1;  // TODO: FR - option to hide block/display warning on failed feed
    public $show_chanimage      = 0;  // added for SimplePie
    public $show_itemimage      = 0;  // added for SimpePie
    public $show_itemcats       = 0;  // added for SimpePie
    public $rssrefresh             = 3600;


/**
 * Display func.
 * @param $data array containing title,content
 */
    function display(Array $data=array())
    {
        $vars = $this->getContent();

        // Check and see if a feed has been supplied to us.
        if (empty($vars['rssurl'])) {
            return xarML('No Feed URL Specified');
        }

        // bug[ 5322 ]
        if (is_numeric($vars['rssurl'])) {
            $headline = xarMod::apiFunc('headlines', 'user', 'get', array('hid' => $vars['rssurl']));
            if (!empty($headline)) {
                $feedfile = $headline['url'];
                $thishid = $headline['hid'];
            } else {
                return xarML('No Feed URL Specified');
            }
        } else {
            $feedfile = $vars['rssurl'];
        }

        // CHECKME: this forces early refresh regardless of caching options, why do we do that? old code below
        // define('MAGPIE_CACHE_AGE', round($refresh/2)); // set lower than block cache so we always get something
        // simplepie'cache_max_minutes' => round($refresh/2*60) - bug, simplepie caching is in seconds
        // this only happens in the rss block, need to work out if it's necessary
        $refresh = $vars['rssrefresh']/2;
        if (!$vars['showdescriptions']) {
            $vars['truncate'] = 0; // no point doing extra work for nothing :)
        }
        // call api function to get the parsed feed (or warning)
        $vars = array_merge($vars, xarMod::apiFunc('headlines', 'user', 'getparsed',
                    array('feedfile' => $feedfile, 'refresh' => $refresh,
                          'numitems' => $vars['maxitems'], 'truncate' => $vars['truncate'])));
        // TODO: option to hide block here instead
        if (!empty($vars['warning'])) {
            if (empty($vars['show_warning'])) return;
            return xarVarPrepForDisplay($vars['warning']);
        } else {
            // here we see if this feed has been updated by comparing the stored hash against the
            // hash provided by the getparsed function, if they're different, we update the feed
            // with the new hash, and the time of the last item in the feed, or the current time
            // this means the feeds can now be sorted reliably by date ala. the cloud block
            if (isset($headline['string']) && ($headline['string'] != $data['compare'])) {
                // call api function to update our feed item
                if (!xarMod::apiFunc('headlines', 'user', 'update', array('hid' => $headline['hid'], 'date' => $data['lastitem'], 'string' => $data['compare']))) return;
            }
        }

        // FR: add alt channel title/desc/link
        if (!empty($vars['alt_chantitle'])) $vars['chantitle'] = $vars['alt_chantitle'];
        if (!empty($vars['alt_chandesc'])) $vars['chandesc'] = $vars['alt_chandesc'];
        if (!empty($vars['alt_chanlink'])) $vars['chanlink'] = $vars['alt_chanlink'];
        // make sure SimplePie's available
        if (!xarMod::isAvailable('simplepie')) {
            $vars['show_chanimage'] = false;
            $vars['show_itemimage'] = false;
            $vars['show_itemcats'] = false;
        }
        if ($vars['linkhid'] && (isset($thishid)&& !empty($thishid))) {
            $vars['linkhid'] = $thishid;
        }

        return $vars;
        $data['content'] = array(
            'feedcontent'  => $data['feedcontent'],
            'blockid'      => $data['bid'],
            'chantitle'    => $data['chantitle'],
            'chanlink'     => $data['chanlink'],
            'chandesc'     => $data['chandesc'],
            'chanimage'    => $data['image'],
            'show_desc'     => $vars['showdescriptions'],
            'show_chantitle' => $vars['show_chantitle'],
            'show_chandesc'  => $vars['show_chandesc'],
            'show_chanimage' => $vars['show_chanimage'],
            'show_itemimage' => $vars['show_itemimage'],
            'show_itemcats' => $vars['show_itemcats'],
            'show_warning' => $vars['show_warning'],
            'linkhid' => $vars['linkhid']
        );

        return $data;
    }
}
?>