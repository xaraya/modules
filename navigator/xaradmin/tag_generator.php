<?php
/*
 * File: $Id: $
 *
 * @package Navigator
 * @copyright (C) 2004 by the Schwab Foundation
 * @link http://wwwk.schwabfoundation.org
 *
 * @subpackage navigator module
 * @author "Carl P. Corliss" <ccorliss@schwabfoundation.org>
*/

/**
 * Tool for creating Navigation tags that
 * can be pasted into a template.
 *
 * @author Carl P. Corliss
 * @returns array
 * @return $data
 */
function navigator_admin_tag_generator()
{
    // Security check
    $menuTypes = array('images', 'list');
    $locTypes  = array('simple', 'crumbtrail');

    if (!xarSecurityCheck('AdminNavigator')) return;

    if (!xarVarFetch('tag',     'enum:location:menu:image:', $tag)) return;

    if (in_array($tag, array('location', 'menu'))) {
        $matrix = xarModGetVar('navigator', 'style.matrix');
        if (!isset($matrix) || empty($matrix)) {
            $matrix = 0;
        } else {
            $matrix = 1;
        }

        if (!$matrix) {
            $base = 'primary';
        } else {
            if (!xarVarFetch('base',    'enum:primary:secondary',   $base, 'primary', XARVAR_NOT_REQUIRED)) return;
        }

        // Menu is handled differently than location
        // in particular, it's attibutes are different.
        if ('menu' == $tag) {
            $types = implode(':', $menuTypes);

            if (!xarVarFetch('tagtype', "enum:$types", $tagtype, 'list',
                              XARVAR_NOT_REQUIRED)) return;

            if (!xarVarFetch('emptygroups',"enum:hide:show",$emptygroups, 'show',
                              XARVAR_NOT_REQUIRED)) return;

            if (!xarVarFetch('intersects', "list:int:1:",   $intersects, array(),
                              XARVAR_NOT_REQUIRED)) return;

            if (!xarVarFetch('maxdepth', "int:1:",   $maxdepth, NULL,
                              XARVAR_NOT_REQUIRED)) return;

            if (!xarVarFetch('rename',     "array:1:",      $rename, array(),
                              XARVAR_NOT_REQUIRED)) return;

            if (!xarVarFetch('exclude',    "isset",         $exclude, array(),
                              XARVAR_NOT_REQUIRED)) return;

            if (isset($maxdepth)) {
                $data['maxdepth'] = $attributes['maxdepth'] = $maxdepth;
            }

            // remove all empty renamed categories
            $renamed = array_filter($rename);
            $data['rename'] = $renamed;
            if (!empty($renamed)) {
                unset($rename);
                foreach ($renamed as $cid => $name) {
                    $rlist[] = implode('+', array($cid, $name));
                }
                $rename = implode('|', $rlist);
                unset($rlist);
                unset($renamed);
                $attributes['rename'] = $rename;
            }

            if (!empty($exclude)) {
                $exclude = array_keys(array_filter($exclude));
                $data['exclude'] = $exclude;
                 $attributes['exclude'] = implode(',', $exclude);
            } else {
                $data['exclude'] = array();
            }

            if (($matrix && $base == 'secondary') &&
                (isset($intersects) && count($intersects))) {
                    $attributes['intersects'] = implode(',', $intersects);
            }

            $plist = xarModGetVar('navigator', 'categories.list.primary');
            $plist = @unserialize($plist);

            xarModAPIFunc('navigator', 'user', 'nested_tree_remove_node',
                           array('tree' => &$plist,
                                 'cids' => $exclude,
                                 'keep-parent' => TRUE));

            xarModAPIFunc('navigator', 'user', 'nested_tree_flatten', &$plist);
            foreach ($plist as $key => $node) {
                $primary_list[$node['cid']] = $node;
                unset($plist[$key]);
            }

            if ('secondary' == $base) {
                $slist = xarModGetVar('navigator', 'categories.list.secondary');
                $slist = @unserialize($slist);

                xarModAPIFunc('navigator', 'user', 'nested_tree_remove_node',
                               array('tree' => &$slist,
                                     'cids' => $exclude,
                                     'keep-parent' => TRUE));

                xarModAPIFunc('navigator', 'user', 'nested_tree_flatten',
                               &$slist);

                foreach ($slist as $key => $node) {
                    $secondary_list[$node['cid']] = $node;
                    unset($slist[$key]);
                }
                $data['secondary_list'] = $secondary_list;
            }

            if (!$matrix || 'primary' == $base) {
                $data['list'] = $primary_list;
            } else {
                $data['list'] = $secondary_list;
            }

            $funcName = 'menutype_';
            $current_tag = 'navigator-menu';

            $data['emptygroups'] = $attributes['emptygroups'] = $emptygroups;
            $data['base'] = $attributes['base'] = $base;
            $data['primary_list'] = $primary_list;
            $data['intersects'] = $intersects;
        } else {
            $types = implode(':', $locTypes);
            $funcName = 'location_';
            $current_tag = 'navigator-location';
            if (!xarVarFetch('tagtype', "enum:$types",  $tagtype, 'simple',
                              XARVAR_NOT_REQUIRED)) return;
        }

        $function = $funcName . $tagtype;

        $data['tagtype']  = $tagtype;
        $data['matrix']   = $matrix;

        ksort($primary_list);
        $primary_cids = array_keys($primary_list);
        $cids = array(current($primary_cids));

        xarVarSetCached('Blocks.articles', 'cids', $cids);

        $attributes['id']         = 'sample_block';
        $attributes['type']       = $tagtype;

        $data['sample_block'] = xarModFunc('navigator', 'user', $function,
                                           $attributes);
    } else {
        $current_tag = 'navigator-image';
        $attributes['id'] = 'left';
    }

    // Create the attribute list
    foreach ($attributes as $name => $value) {
        $attributes[] = "$name=\"$value\"";
        unset($attributes[$name]);
    }

    // Push the attributes onto seperate 'lines' so don't
    // end up having to scroll to the right to see the tag
    $sep = ' <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
//     $sep = ' ';

    $current_tag = '&lt;xar:' . $current_tag . $sep .
                    implode($sep, $attributes) . ' /&gt;';

    $data['tag'] = $tag;
    $data['current_tag'] = $current_tag;

    // Return the template variables defined in this function
    return $data;
}

?>
