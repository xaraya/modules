<?php
/**
 * Navigation Menu Element Type: list
 *
 *
 */

function navigator_user_menutype_images( $args )
{
    $data = xarModAPIFunc('navigator', 'user',
                          'process_menu_attributes', &$args);

    if (!isset($data) || empty($data)) {
        return;
    } else {
        extract($args);
        extract($data);
    }

    $themedir = xarTplGetThemeDir();

    foreach ($tree as $key => $item) {
        $image = preg_replace('/[^a-z0-9._-]/i', '_', strtolower($item['name']));
        if ($item['cid'] == $current_primary_id ||
            $item['cid'] == $current_secondary_id) {
                $mItem['selected'] = TRUE;
        } else {
            $mItem['selected'] = FALSE;
        }

        $image_normal = $themedir .'/images/' . $image . '.gif';
        $mItem['image-normal'] = "../../$image_normal";
        $mItem['image-normal-url'] = xarServerGetBaseURL() . $image_normal;
        if ($dimensions = @getimagesize($image_normal)) {
            $mItem['image-normal-width'] = $dimensions[0];
            $mItem['image-normal-height'] = $dimensions[1];
        }

        $image_hover  = $themedir .'/images/' . $image . '-hover.gif';
        $mItem['image-hover'] = "../../$image_hover";
        $mItem['image-hover-url'] = xarServerGetBaseURL() . $image_hover;
        if ($dimensions = @getimagesize($image_hover)) {
            $mItem['image-hover-width'] = $dimensions[0];
            $mItem['image-hover-height'] = $dimensions[1];
        }

        $mItem['alt-text']     = $item['name'];
        if (!isset($secDef) || empty($secDef)) {

            $mItem['url'] = xarModURL('articles', 'user', 'view',
                                       array('cids' => array($item['cid'])));
        } else {
            $mItem['url'] = xarModURL('articles', 'user', 'view',
                                       array('cids' => array($item['cid'],
                                                             $secDef),
                                             'andcids' => TRUE));
        }

        if (!isset($first)) {
            $first = TRUE;

            $list[1] = $mItem;
        } else {
            $list[] = $mItem;
        }
    }

    $data['tree']     = $list;
    $data['tagId']    = $id;

    xarModAPIFunc('navigator', 'user', 'set_style',
                   array('data' => $data, 'name' => 'image-menu'));

    return $data;
}

?>
