<?php

/**
 * Make a &lt;select&gt; box with tree of categories (&nbsp;&nbsp;--+ style)
 * e.g. for use in your own admin pages to select root categories for your
 * module, choose a particular subcategory for an item etc.
 *
 *  -- INPUT --
 * @param $args['cid'] optional ID of the root category used for the tree
 *                     (if not specified, the whole tree is shown)
 * @param $args['eid'] optional ID to exclude from the tree (probably not
 *                     very useful in this context)
 * @param $args['multiple'] optional flag (1) to have a multiple select box
 * @param $args['values'] optional array $values[$id] = 1 to mark option $id
 *                        as selected
 * @param $args['return_itself'] include the cid itself (default false)
 * @param $args['select_itself'] allow selecting the cid itself if included (default false)
 * @param $args['show_edit'] show edit link for current selection (default false)
 * @param $args['javascript'] add onchange, onblur or whatever javascript to select (default empty)
 *
 *  -- OUTPUT --
 * @returns string
 * @return select box for categories :
 *
 * &lt;select name="cids[]"&gt; (or &lt;select name="cids[]" multiple&gt;)
 * &lt;option value="123"&gt;&nbsp;&nbsp;--+&nbsp;My Cat 123
 * &lt;option value="124" selected&gt;&nbsp;&nbsp;&nbsp;&nbsp;+&nbsp;My Cat 123
 * ...
 * &lt;/select&gt;
 *
 */
function categories_visualapi_makeselect ($args)
{
    $tree_array = xarModAPIFunc('categories','visual','treearray',$args);

    if (!isset($args['multiple'])) {
        $args['multiple'] = 0;
    }

    if (empty($args['show_edit']) || !empty($args['multiple'])) {
        $args['show_edit'] = 0;
    }

// TODO: templatize !

    $select = '<select name="cids[]"' . (($args['multiple'] == 1)?' multiple="multiple"':'').
               (!empty($args['javascript']) ? ' ' . $args['javascript'] : '').'>'."\n";
    if (!empty($args['select_itself'])) {
        $select .= '<option value="">' . xarML('Select :') . '</option>'."\n";
    }
    $already_passed = false;
    $current_id = 0;
    foreach ($tree_array as $option)
    {
        if (isset($args['cid']) && $option['id'] == $args['cid']) {
            $name = $option['name'];
            if (!empty($args['select_itself'])) {
                if (!empty($args['values'][$option['id']])) {
                    $select .= '<option value="'.$option['id'].'" selected="selected">' . $name . '</option>'."\n";
                } else {
                    $select .= '<option value="'.$option['id'].'">' . $name . '</option>'."\n";
                }
            } else {
                $name = preg_replace('/&nbsp;/','',$name);
                $name = preg_replace('/^[ +-]*/','',$name);
                $select .= '<option value="">'.$name;
                if ($args['multiple'] == 1) {
                    $select .= ' :';
                }
            }
            continue;
        }
        $select .= "<option ";
        if (isset($args['values']) && isset($args['values'][$option['id']]) &&
            ($args['multiple'] == 1 || !$already_passed) &&
            ($args['values'][$option['id']]>0))
        {
            $select .= 'selected="selected" ';
            $args['values'][$option['id']]--;
            $already_passed = true;
            $current_id = $option['id'];
        }
        $select .= 'value="'.$option['id'].'">'.$option['name'] . '</option>'. "\n";
    }
    unset($tree_array);
    $select .= "</select>\n";

    if (!empty($args['show_edit']) && !empty($current_id) &&
		xarSecurityCheck('EditCategories',0,'All',"All:$current_id")) {
        $select .= '&nbsp;[ <a href="' . xarModURL('categories','admin','modifycat',
                                                array('cid' => $current_id));
        $select .= '">' . xarML('edit') . '</a> ]';
    }

    return $select;
}

?>
