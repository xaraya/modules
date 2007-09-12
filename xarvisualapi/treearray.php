<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * Build array with visual tree of categories (&nbsp;&nbsp;--+ style)
 * for use in &lt;select&gt; or table display
 *
 * @param $args['cid'] The ID of the root category used for the tree
 * @param $args['eid'] optional ID to exclude from the tree (e.g. the ID of
 *                     your current category)
 * @param $args['return_itself'] include the cid itself (default false)
 *
 * @return array of array('id' => 123, 'name' => '&nbsp;&nbsp;--+&nbsp;My Cat')
 */
function categories_visualapi_treearray ($args)
{
    if (!isset($args['maximum_depth'])) {
        $args['maximum_depth'] = null;
    }
    if (!isset($args['minimum_depth'])) {
        $args['minimum_depth'] = null;
    }

    // Getting categories Array
    $categories = xarModAPIFunc
    (
     'categories',
     'user',
     'getcat',
     Array
     (
      'eid' => (isset($args['eid']))?$args['eid']:false,
      'cid' => (isset($args['cid']))?$args['cid']:false,
      'return_itself' => (isset($args['return_itself']))?$args['return_itself']:false,
      'getchildren' => true,
      'maximum_depth' => $args['maximum_depth'],
      'minimum_depth' => $args['minimum_depth']
     )
    );

    if ($categories === false) {// If it returned false
        $msg = xarML('Error obtaining category.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Outputing Location Options

    $last_indentation = 0;
    $tree_data = Array ();

    foreach ($categories as $category)
    {
        $indentation_output = "";
        for ($i=1; $i < $category['indentation']; $i++) {
           $indentation_output .= "&nbsp;&nbsp;&nbsp;&nbsp;";
        }
        if ($last_indentation < $category['indentation']) {
           $indentation_output .= "--+&nbsp;&nbsp;";
        } else {
           $indentation_output .= "&nbsp;&nbsp;&nbsp;+&nbsp;";
        }

        $last_indentation = $category['indentation'];

        $tree_data[] = Array('id'   => $category['cid'],
                             'name' => $indentation_output
                                      .xarVarPrepForDisplay($category['name']));
    }
    unset($categories);

    return $tree_data;

}
?>
