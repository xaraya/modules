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
    public function display()
    {
        if (!xarUser::isLoggedIn()) {
            return;
        }

        $vars = $this->getContent();

        $now = time();
        sys::import('modules.crispbb.class.tracker');
        if (xarVar::isCached('Blocks.crispbb', 'tracker')) {
            $tracker = xarVar::getCached('Blocks.crispbb', 'tracker_object');
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
    public function modify(array $data=array())
    {
        $data = $this->getContent();

        return $data;
    }

    /**
     * Updates the Block config from the Blocks Admin
     * @param $data array containing title,content
     */
    public function update(array $data=array())
    {
        $vars = array();
        if (!xarVar::fetch('showusername', 'checkbox', $vars['showusername'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showaccount', 'checkbox', $vars['showaccount'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showtimenow', 'checkbox', $vars['showtimenow'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showlastvisit', 'checkbox', $vars['showlastvisit'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showthisvisit', 'checkbox', $vars['showthisvisit'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showtotalvisit', 'checkbox', $vars['showtotalvisit'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showwaiting', 'checkbox', $vars['showwaiting'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!xarVar::fetch('showlogout', 'checkbox', $vars['showlogout'], false, xarVar::NOT_REQUIRED)) {
            return;
        }
        $this->setContent($vars);
        return true;
    }
}
