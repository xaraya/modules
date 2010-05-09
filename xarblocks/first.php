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
    // declare the name of your block, the module it belongs to and an
    // optional description, these are required
    public $module          = 'dyn_example';  // Module your block belongs to
    public $text_type       = 'First Block';  // Block name
    public $text_type_long  = 'Show first dyn_example items (alphabetical)'; // Block description

    // the basicblock class declares properties required for the blocks module
    // to function properly, you should avoid using those in your block, but you
    // must declare any additional properties which are used by your block,
    // these are used as the defaults when a user creates a new block instance.

    // The first block only has one property, numitems
    public $numitems = 5;

    // declare the methods your block will use

    // display method, this is called whenever the block is displayed,
    // either as a standalone block, or when rendered as part of a block group
    // data here is passed to you blocks {blocktype}.xt template
    function display(Array $args=array())
    {
        // the parent class supplies blockinfo for the current block instance
        // so we call the parent method here to obtain the data
        $data = parent::display($args);

        // the data passed to the block template for your block is stored as
        // an array in $data['content'], if you need to pass additional variables,
        // or act on the ones stored, you should use that array.

        // here we're just going to check that numitems is set, and if not,
        // set the default
        if (!isset($data['content']['numitems'])) {
            $data['content']['numitems'] = $this->numitems;
        }

        // and now we return the $data to the calling function
        return $data;
    }

    // modify method, this is called whenever the block is modified in blocks admin,
    // the data here is passed to your blocks modify-{blocktype}.xt template
    function modify(Array $args=array())
    {
        // the parent class supplies content for the current block instance
        // so we call the parent method here to obtain the data
        $data = parent::modify($args);

        // here we're just going to check that numitems is set, and if not,
        // set a default
        if (!isset($data['content']['numitems'])) {
            $data['content']['numitems'] = $this->numitems;
        }

        // and now we return the content to the calling function
        return $data['content'];
    }

    // update method, this is called whenever the block is update from blocks admin modify,
    function update(Array $args=array())
    {
        // the parent class supplies blockinfo for the current block instance
        // so we call the parent method here to obtain the data
        $data = parent::update($args);

        // fetch any parameters to update from input
        if (!xarVarFetch('numitems', 'int:0:', $numitems, 5, XARVAR_NOT_REQUIRED)) {return;}

        // update the var in the content array
        $data['content']['numitems'] = $numitems;

        // and pass the data back to the calling function
        return $data;
    }

}
?>