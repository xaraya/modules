<?php
/**
 * Dynamic Data Example Block
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
// All block classes extend the BasicBlock class and implement the iBlock inteface
sys::import('xaraya.structures.containers.blocks.basicblock');
Class Dyn_example_FirstBlock extends BasicBlock implements iBlock
{
    // File Information, supplied by developer, never changes during a versions lifetime, required
    protected $type             = 'first';
    protected $module           = 'dyn_example'; // module block type belongs to, if any
    protected $text_type        = 'First Block';  // Block type display name
    protected $text_type_long   = 'Show first dyn_example items (alphabetical)'; // Block type description
    // Additional info, supplied by developer, optional 
    protected $type_category    = 'block'; // options [(block)|group] 
    protected $author           = '';
    protected $contact          = '';
    protected $credits          = '';
    protected $license          = '';

    // the basicblock class declares properties required for the blocks module
    // to function properly, you should avoid using those in your block, but you
    // must declare any additional properties which are used by your block,
    // these are used as the defaults when a user creates a new block instance.

    // The first block only has one property, numitems
    public $numitems = 5;

    // declare the methods your block will use

    // display method, this is called whenever the block is displayed,
    // either as a standalone block, or when rendered as part of a block group
    function display()
    {

        $data = $this->getContent();

        // and now we return the $data to the calling function
        return $data;
    }

    // modify method, this is called whenever the block is modified in blocks admin,
    // the data here is passed to your blocks modify-{blocktype}.xt template
    function modify()
    {
        $data = $this->getContent();

        // and now we return the $data to the calling function
        return $data;
    }

    // update method, this is called whenever the block is update from blocks admin modify,
    function update()
    {
        $vars = array();
        // fetch any parameters to update from input
        if (!xarVarFetch('numitems', 'int:0:', $vars['numitems'], 5, XARVAR_NOT_REQUIRED)) {return;}

        $this->setContent($vars);
        return true;
    }

}
?>