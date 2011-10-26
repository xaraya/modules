<?php
/**
 * @package modules
 * @copyright (C) 2008-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispbb module
 * @link http://www.xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
/**
 * @author crisp <crisp@crispcreations.co.uk>
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class Crispbb_UserPanelBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'userpanel';
    protected $module           = 'crispbb'; // module block type belongs to, if any
    protected $text_type        = 'crispBB User Panel';  // Block type display name
    protected $text_type_long   = 'Display user information to current logged in user'; // Block type description
    // Additional info, supplied by developer, optional 
    protected $type_category    = 'block'; // options [(block)|group] 
    protected $author           = 'Chris Powis';
    protected $contact          = 'crisp@crispcreations.co.uk';
    protected $credits          = '';
    protected $license          = '';

    // blocks subsystem flags
    protected $show_preview = true;  // let the subsystem know if it's ok to show a preview
    protected $show_help    = false; // let the subsystem know if this block type has a help() method

    public $showusername        = true;
    public $showaccount         = true;
    public $showtimenow         = true;
    public $showlastvisit       = true;
    public $showthisvisit       = true;
    public $showtotalvisit      = true;
    public $showwaiting         = false;
    public $showlogout          = true;

/**
 * Display func.
 * @param $data array containing title,content
 */
    function display()
    {
        if (!xarUserIsLoggedIn()) return;

        $vars = $this->getContent();

        $now = time();
        sys::import('modules.crispbb.class.tracker');
        if (xarVarIsCached('Blocks.crispbb', 'tracker')) {
            $tracker = xarVarGetCached('Blocks.crispbb', 'tracker_object');
        } else {
            $tracker = unserialize(xarModUserVars::get('crispbb', 'tracker_object'));
        }
        $userpanel = $tracker->getUserPanelInfo();
        $vars['id'] = $userpanel['id'];
        $vars['name'] = $userpanel['name'];
        if ($vars['showlastvisit']) {
        $vars['lastvisit'] = $userpanel['lastvisit'];
        }
        if ($vars['showthisvisit']) {
        $vars['visitstart'] = $userpanel['visitstart'];
        }
        if ($vars['showtotalvisit']) {
        $vars['numvisits'] = $userpanel['numvisits'];
        $vars['totalvisit'] = $userpanel['onlinestamp'];
        }
        if ($vars['showtimenow']) {
            $vars['timenow'] = $now;
        }
        if ($vars['showwaiting']) {
            $vars['showwaiting'] = xarMod::guiFunc('crispbb', 'admin', 'waitingcontent');
        }

        return $vars;
    }


/**
 * Modify Function to the Blocks Admin
 * @param $data array containing title,content
 */
    public function modify(Array $data=array())
    {
        $data = $this->getContent();

        return $data;
    }

/**
 * Updates the Block config from the Blocks Admin
 * @param $data array containing title,content
 */
    public function update(Array $data=array())
    {
        $vars = array();
        if (!xarVarFetch('showusername', 'checkbox', $vars['showusername'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showaccount', 'checkbox', $vars['showaccount'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showtimenow', 'checkbox', $vars['showtimenow'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showlastvisit', 'checkbox', $vars['showlastvisit'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showthisvisit', 'checkbox', $vars['showthisvisit'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showtotalvisit', 'checkbox', $vars['showtotalvisit'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showwaiting', 'checkbox', $vars['showwaiting'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showlogout', 'checkbox', $vars['showlogout'], false, XARVAR_NOT_REQUIRED)) return;
        $this->setContent($vars);
        return true;
    }
}
?>
