<?php

/**
 * Build array with visual tree of categories (&nbsp;&nbsp;--+ style)
 * for use in &lt;select&gt; or table display
 *
 *  -- INPUT --
 * @param $args['cid'] The ID of the root category used for the tree
 * @param $args['eid'] optional ID to exclude from the tree (e.g. the ID of
 *                     your current category)
 * @param $args['return_itself'] include the cid itself (default false)
 *
 *  -- OUTPUT --
 * @returns array
 * @return array of array('id' => 123, 'name' => '&nbsp;&nbsp;--+&nbsp;My Cat')
 */
function categories_visualapi_treearray ($args)
{
    // Load User API
    if (!xarModAPILoad('categories', 'user')) return;

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
       xarSessionSetVar('errormsg', xarML('Error obtaining category'));
       return false;
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

/**
 * Build array with visual tree of categories (&lt;ul&gt;&lt;li&gt;...&lt;/li&gt; style)
 * for use in view maps etc.
 *
 *  -- INPUT --
 * @param $args['cid'] The ID of the root category used for the tree
 * @param $args['eid'] optional ID to exclude from the tree (e.g. the ID of
 *                     your current category)
 *
 *  -- OUTPUT --
 * @returns array
 * @return array of array('id' => 123,
 *                        'name' => 'My Cat',
 *                        'beforetags' => '&lt;ul&gt;&lt;li&gt; ',
 *                        'aftertags' => ' &lt;/li&gt;&lt;/ul&gt;&lt;/ul&gt;')
 */
?>