<?php
/**
 * Dynamic Object User Interface
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
/**
 * Use the dynamic object user interface
 * @param none
 * @return string
 */
function dyn_example_object_main()
{
    sys::import('modules.dynamicdata.class.userinterface');

    // Specify some arguments for the user interface
    $args = array(// show this object in the interface
                  'object'    => 'dyn_example',
                  // look for object templates in this module
                  'tplmodule' => 'dyn_example',
                  // use this title in the generic objects/ui_* templates (or override them yourself)
                  'tpltitle'  => 'Dynamic Example Object Interface',
                  // use the current URL as basis for all links
                  'linktype'  => 'current');

    // Get the user interface
    $interface = new DataObjectUserInterface($args);

    // Handle the request of the user and return the output
    return $interface->handle($args);
}

?>
