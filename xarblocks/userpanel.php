<?php
/**
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispbb module
 * @link http://www.xaraya.com/index.php/release/970.html
 * @author
 */
/**
 * @author crisp <crisp@crispcreations.co.uk>
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class UserPanelBlock extends BasicBlock implements iBlock
{
    public $no_cache            = 1;

    public $name                = 'UserpanelBlock';
    public $module              = 'crispbb';
    public $text_type           = 'crispBB Userpanel';
    public $text_type_long      = 'Displays user information for current logged in user';
    public $show_preview        = true;

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
    function display(Array $data=array())
    {
        $data = parent::display($data);
        if (empty($data)) return;
        if (!xarUserIsLoggedIn()) return;

        $vars = isset($data['content']) ? $data['content'] : array();
        // Defaults
        if (!isset($vars['showusername']))   $vars['showusername']   = $this->showusername;
        if (!isset($vars['showaccount']))    $vars['showaccount']    = $this->showaccount;
        if (!isset($vars['showtimenow']))    $vars['showtimenow']    = $this->showtimenow;
        if (!isset($vars['showlastvisit']))  $vars['showlastvisit']  = $this->showlastvisit;
        if (!isset($vars['showthisvisit']))  $vars['showthisvisit']  = $this->showthisvisit;
        if (!isset($vars['showtotalvisit'])) $vars['showtotalvisit'] = $this->showtotalvisit;
        if (!isset($vars['showwaiting']))    $vars['showwaiting']    = $this->showwaiting;
        if (!isset($vars['showlogout']))     $vars['showlogout']     = $this->showlogout;

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
        $vars['totalvisit'] = $userpanel['timeonline'];
        }
        if ($vars['showtimenow']) {
            $vars['timenow'] = $now;
        }
        if ($vars['showwaiting']) {
            $vars['showwaiting'] = xarMod::guiFunc('crispbb', 'admin', 'waitingcontent');
        }

        $data['content'] = $vars;
        return $data;
    }


/**
 * Modify Function to the Blocks Admin
 * @param $data array containing title,content
 */
    public function modify(Array $data=array())
    {
        $data = parent::modify($data);

        // Defaults
        if (!isset($data['showusername']))   $data['showusername']   = $this->showusername;
        if (!isset($data['showaccount']))    $data['showaccount']    = $this->showaccount;
        if (!isset($data['showtimenow']))    $data['showtimenow']    = $this->showtimenow;
        if (!isset($data['showlastvisit']))  $data['showlastvisit']  = $this->showlastvisit;
        if (!isset($data['showthisvisit']))  $data['showthisvisit']  = $this->showthisvisit;
        if (!isset($data['showtotalvisit'])) $data['showtotalvisit'] = $this->showtotalvisit;
        if (!isset($data['showwaiting']))    $data['showwaiting']    = $this->showwaiting;
        if (!isset($data['showlogout']))     $data['showlogout']     = $this->showlogout;

        return $data;
    }

/**
 * Updates the Block config from the Blocks Admin
 * @param $data array containing title,content
 */
    public function update(Array $data=array())
    {
        $data = parent::update($data);
        $vars = array();
        if (!xarVarFetch('showusername', 'checkbox', $vars['showusername'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showaccount', 'checkbox', $vars['showaccount'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showtimenow', 'checkbox', $vars['showtimenow'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showlastvisit', 'checkbox', $vars['showlastvisit'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showthisvisit', 'checkbox', $vars['showthisvisit'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showtotalvisit', 'checkbox', $vars['showtotalvisit'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showwaiting', 'checkbox', $vars['showwaiting'], false, XARVAR_NOT_REQUIRED)) return;
        if (!xarVarFetch('showlogout', 'checkbox', $vars['showlogout'], false, XARVAR_NOT_REQUIRED)) return;
        $data['content'] = $vars;
        return $data;
    }
}
?>