<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * Original Author of file: Jim McDonald
 */
sys::import('modules.publications.xarblocks.navigation');

class Publications_NavigationBlockAdmin extends Publications_NavigationBlock
{
    function modify()
    {

    $vars = $this->getContent();

    // Defaults
    if (empty($vars['layout'])) {
        $vars['layout'] = 1;
    }
    if (empty($vars['show_catcount'])) {
        $vars['show_catcount'] = 0;
    }
    if (empty($vars['showchildren'])) {
        $vars['showchildren'] = 0;
    }
    if (empty($vars['startmodule'])) {
        $vars['startmodule'] = '';
    }
    if (empty($vars['showempty'])) {
        $vars['showempty'] = 0;
    }
    if (empty($vars['dynamictitle'])) {
        $vars['dynamictitle'] = 0;
    }

    $vars['catcounts'] = array(array('id' => 0,
                                   'name' => 'None'),
                             array('id' => 1,
                                   'name' => 'Simple count'),
                             array('id' => 2,
                                   'name' => 'Cascading count'));
    $vars['layouts'] = array(array('id' => 1,
                                   'name' => 'Tree (Side Block)'),
                             array('id' => 2,
                                   'name' => 'Crumbtrail (Top Block)'),
                             array('id' => 3,
                                   'name' => 'Prev/Next (Bottom Block)'));

    $vars['children'] = array(array('id' => 0,
                                    'name' => xarML('None')),
                              array('id' => 1,
                                    'name' => xarML('Direct children only')),
                              array('id' => 2,
                                    'name' => xarML('All children')));

    $vars['modules'] = array();
    $vars['modules'][] = array('id' => '',
                               'name' => xarML('Adapt dynamically to current page'));

    // List contains:
    // 0. option group for the module
    // 1. module [base1|base2]
    // 2.    module [base1]    (for itemtype 0)
    //       module [base2]
    // 3.    module:itemtype [base3|base4]
    // 4.       itemtype [base3]
    //          itemtype [base4]

    $allcatbases = xarModAPIfunc(
        'categories', 'user', 'getallcatbases',
        array('order'=>'module', 'format'=>'tree')
    );

    foreach($allcatbases as $modulecatbases) {
        // Module label for the option group in the list.
        $modlabel = ucwords($modulecatbases['module']);

        $vars['modules'][] = array(
            'label' => $modlabel
        );

        $indent = '&#160;&#160;&#160;';

        foreach($modulecatbases['itemtypes'] as $thisitemtype => $itemtypecatbase) {
            if (!empty($itemtypecatbase['catbases'])) {
                $catlist = '[';
                $join = '';
                foreach($itemtypecatbase['catbases'] as $itemtypecatbases) {
                    $catlist .= $join . $itemtypecatbases['category']['name'];
                    $join = ' | ';
                }
                $catlist .= ']';

                //if (empty($itemtypecatbase['itemtype']['label'])) {
                if ($thisitemtype == 0) {
                    // Default module cats at top level.
                    $indent_level = 0;
                    $itemtypelabel = '';
                } else {
                    // Item types at one level deeper
                    $indent_level = 1;
                    $itemtypelabel = ' -&gt; ' . $itemtypecatbase['itemtype']['label'];
                }

                // Module-Itemtype [all cats]
                $vars['modules'][] = array(
                    'id' => $modulecatbases['module'] . '.' . $thisitemtype . '.0',
                    'name' => str_repeat($indent, $indent_level) . $modlabel . $itemtypelabel . ' ' . $catlist
                );

                // Individual publications a level deeper.
                $indent_level += 1;

                // Individual base publications where there are more than one.
                if (count($itemtypecatbase['catbases']) > 1) {
                    foreach($itemtypecatbase['catbases'] as $itemtypecatbases) {
                        $catlist = '[' . $itemtypecatbases['category']['name'] . ']';
                        if ($thisitemtype == 0) {$itemtypelabel = $modlabel;}
                        $vars['modules'][] = array(
                            'id' => $modulecatbases['module'] . '.' . $thisitemtype . '.' . $itemtypecatbases['category']['cid'],
                            'name' => str_repeat($indent, $indent_level) . $itemtypelabel . ' ' . $catlist
                        );
                    }
                }
            }
        }
    }

    $vars['blockid'] = $this->block_id;
    // Return output
    return xarTplBlock('publications', 'nav-admin', $vars);
    }
    
    public function update()
    {
    $vars = array();
    if(!xarVarFetch('layout',       'isset', $vars['layout'],       NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('show_catcount', 'isset', $vars['show_catcount'], NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('showchildren', 'isset', $vars['showchildren'], NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('showempty',    'checkbox', $vars['showempty'],    false, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('startmodule',  'isset', $vars['startmodule'],  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('dynamictitle', 'checkbox', $vars['dynamictitle'], false, XARVAR_DONT_SET)) {return;}
    $this->setContent($vars);
    return true;
    }    

}
?>