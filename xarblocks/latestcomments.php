<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
sys::import('xaraya.structures.containers.blocks.basicblock');

class Comments_LatestcommentsBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'latestcomments';
    protected $module           = 'comments'; // module block type belongs to, if any
    protected $text_type        = 'Latest Comments';  // Block type display name
    protected $text_type_long   = 'Show latest comments'; // Block type description
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

    public $howmany = 5;
    public $modid = array('all');
    public $pubtypeid = 0;
    public $addauthor = true;
    public $addmodule = false;
    public $addcomment = 20;
    public $addobject = true;
    public $adddate = 'on';
    public $adddaysep = 'on';
    public $truncate = 18;
    public $addprevious = 'on';

    public function display()
    {
        $vars = $this->getContent();
        $vars['block_is_calling'] = 1;
        $vars['first'] = 1;
        $vars['order'] = 'DESC';
        return xarMod::guiFunc('comments', 'user', 'displayall', $vars) ;
    }

    public function modify()
    {
        $vars = $this->getContent();

        // get the list of modules+itemtypes that comments is hooked to
        $hookedmodules = xarMod::apiFunc('modules', 'admin', 'gethookedmodules',
                                   array('hookModName' => 'comments'));

        $modlist = array();
        $modlist['all'] = xarML('All');
        if (isset($hookedmodules) && is_array($hookedmodules)) {
            foreach ($hookedmodules as $modname => $value) {
                // Get the list of all item types for this module (if any)
                $mytypes = xarMod::apiFunc($modname,'user','getitemtypes',
                                     // don't throw an exception if this function doesn't exist
                                     array(), 0);
                // we have hooks for individual item types here
                if (!isset($value[0])) {
                    foreach ($value as $itemtype => $val) {
                        if (isset($mytypes[$itemtype])) {
                            $type = $mytypes[$itemtype]['label'];
                        } else {
                            $type = xarML('type #(1)',$itemtype);
                        }
                        $modlist["$modname.$itemtype"] = ucwords($modname) . ' - ' . $type;
                    }
                } else {
                    $modlist[$modname] = ucwords($modname);
                    // allow selecting individual item types here too (if available)
                    if (!empty($mytypes) && count($mytypes) > 0) {
                        foreach ($mytypes as $itemtype => $mytype) {
                            if (!isset($mytype['label'])) continue;
                            $modlist["$modname.$itemtype"] = ucwords($modname) . ' - ' . $mytype['label'];
                        }
                    }
                }
            }
        }
        $vars['modlist'] = $modlist;

        return $vars;
    }

    public function update()
    {
        $vars = array();
        if (!xarVar::fetch('howmany', 'int:1:', $vars['howmany'], 0, xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('modid', 'isset', $vars['modid'],  array(), xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('pubtypeid', 'isset', $vars['pubtypeid'], 0, xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('addauthor', 'isset', $vars['addauthor'], '', xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('addmodule', 'isset', $vars['addmodule'], '', xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('addcomment', 'isset', $vars['addcomment'], '', xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('addobject', 'isset', $vars['addobject'], '', xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('adddate', 'checkbox', $vars['adddate'], 0, xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('adddaysep', 'checkbox', $vars['adddaysep'], 0, xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('truncate', 'int:1:', $vars['truncate'], 0, xarVar::NOT_REQUIRED)) return;
        if (!xarVar::fetch('addprevious', 'checkbox', $vars['addprevious'], 0, xarVar::NOT_REQUIRED)) return;

        $this->setContent($vars);
        return true;
    }

}
?>