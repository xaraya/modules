<?php

/**
// TODO: move this to some common place in Xaraya (base module ?)
 * show some predefined form field in a template
 *
 * @param $args array containing the definition of the field (type, name, value, ...)
 * @returns string
 * @return string containing the HTML (or other) text to output in the BL template
 */
function articles_userapi_showfield($args)
{
    if (empty($args['type']) || $args['type'] != 'fieldtype') {
        return xarModAPIFunc('dynamicdata','admin','showinput',$args);
    }

    extract($args);
    if (empty($name)) {
        return xarML('Missing \'name\' attribute in field tag or definition');
    }
    if (!isset($type)) {
        $type = 'text';
    }
    if (!isset($value)) {
        $value = '';
    }
    if (!isset($id)) {
        $id = '';
    } else {
        $id = ' id="'.$id.'"';
    }
    if (!isset($tabindex)) {
        $tabindex = '';
    } else {
        $tabindex = ' tabindex="'.$tabindex.'"';
    }

    // Note: if we want to re-use this for dynamic data, types are numeric there
    if (is_numeric($type)) {
        $fieldformatnums = xarModAPIFunc('articles','user','getfieldformatnums');
        foreach ($fieldformatnums as $fname => $fid) {
            if ($fid == $type) {
                $type = $fname;
                break;
            }
        }
    }

    $output = '';
    switch ($type) {
        case 'text':
        case 'textbox':
            if (empty($size)) {
                $size = 50;
            }
            $output .= '<input type="text" name="'.$name.'" value="'.$value.'" size="'.$size.'"'.$id.$tabindex.' />';
            break;
        case 'textarea':
        case 'textarea_small':
        case 'textarea_medium':
        case 'textarea_large':
            if (empty($wrap)) {
                $wrap = 'soft';
            }
            if (empty($cols)) {
                $cols = 50;
            }
            if (empty($rows)) {
                if ($type == 'textarea_small') {
                    $rows = 2;
                } elseif ($type == 'textarea_large') {
                    $rows = 20;
                } else {
                    $rows = 8;
                }
            }
            $output .= '<textarea name="'.$name.'" wrap="'.$wrap.'" rows="'.$rows.'" cols="'.$cols.'"'.$id.$tabindex.'>'.$value.'</textarea>';
            break;
    // TEST ONLY
        case 'webpage':
            if (!isset($options) || !is_array($options)) {
                $options = array();
            // Load admin API for HTML file browser
                if (!xarModAPILoad('articles', 'admin'))  return 'Unable to load articles admin API';
                $basedir = '/home/mikespub/www/pictures';
                $filetype = 'html?';
                $files = xarModAPIFunc('articles','admin','browse',
                                       array('basedir' => $basedir,
                                             'filetype' => $filetype));
                natsort($files);
                array_unshift($files,'');
                foreach ($files as $file) {
                    $options[] = array('id' => $file,
                                       'name' => $file);
                }
                unset($files);
            }
            // fall through to the next one
        case 'status':
            if (!isset($options) || !is_array($options)) {
                $options = array(
                                 array('id' => 0, 'name' => xarML('Submitted')),
                                 array('id' => 1, 'name' => xarML('Rejected')),
                                 array('id' => 2, 'name' => xarML('Approved')),
                                 array('id' => 3, 'name' => xarML('Front Page')),
                           );
            }
            if (empty($value)) {
                $value = 0;
            }
            // fall through to the next one
        case 'select':
        case 'dropdown':
        case 'listbox':
            if (!isset($multiple)) {
                $multiple = '';
            } else {
                $multiple = ' multiple';
            }
            $output .= '<select name="'.$name.'"'.$id.$tabindex.$multiple.'>';
            if (!isset($selected)) {
                if (!empty($value)) {
                    $selected = $value;
                } else {
                    $selected = '';
                }
            }
            if (!isset($options) || !is_array($options)) {
                $options = array();
            }
            foreach ($options as $option) {
                $output .= '<option value="'.$option['id'].'"';
                if ($option['id'] == $selected) {
                    $output .= ' selected';
                }
                $output .= '>'.$option['name'].'</option>';
            }
            $output .= '</select>';
            break;
        case 'file':
        case 'fileupload':
            if (empty($maxsize)) {
                $maxsize = 1000000;
            }
            $output .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.$maxsize.'" />';
            if (empty($size)) {
                $size = 40;
            }
            $output .= '<input type="file" name="'.$name.'" size="'.$size.'"'.$id.$tabindex.' />';
            break;
        case 'url':
            if (empty($size)) {
                $size = 50;
            }
            if (empty($value)) {
                $value = 'http://';
            }
            $output .= '<input type="text" name="'.$name.'" value="'.$value.'" size="'.$size.'"'.$id.$tabindex.' />';
            if (!empty($value) && $value != 'http://') {
                $output .= ' [ <a href="'.$value.'" target="preview">'.xarML('check').'</a> ]';
            }
            break;
        case 'image':
            if (empty($size)) {
                $size = 50;
            }
            $output .= '<input type="text" name="'.$name.'" value="'.$value.'" size="'.$size.'"'.$id.$tabindex.' />';
            if (!empty($value)) {
                $output .= ' [ <a href="'.$value.'" target="preview">'.xarML('show').'</a> ]';
            }
            $output .= '<br />// TODO: add image picker ?';
            break;
        case 'static':
            $output .= $value;
            break;
        case 'hidden':
            $output .= '<input type="hidden" name="'.$name.'" value="'.$value.'"'.$id.$tabindex.' />';
            break;
        case 'username':
            if (empty($value)) {
                $value = xarUserGetVar('uid');
            }
            $user = xarUserGetVar('name', $value);
            if (empty($user)) {
                $user = xarUserGetVar('uname', $value);
            }
            $output .= $user;
            if ($value > 1) {
                $output .= ' [ <a href="'.xarModURL('roles','user','display',
                                                    array('uid' => $value))
                           . '" target="preview">'.xarML('profile').'</a> ]';
            }
            break;
        case 'date':
        case 'calendar':
            if (empty($value)) {
                $value = time();
            }
        // TODO: adapt to local/user time !
            $output .= strftime('%a, %d %B %Y %H:%M:%S %Z', $value);
            $output .= '<br />';
            $localtime = localtime($value,1);
            $output .= xarML('Date') . ' <select name="'.$name.'[year]"'.$id.$tabindex.'>';
            if (empty($minyear)) {
                $minyear = $localtime['tm_year'] + 1900 - 2;
            }
            if (empty($maxyear)) {
                $maxyear = $localtime['tm_year'] + 1900 + 2;
            }
            for ($i = $minyear; $i <= $maxyear; $i++) {
                if ($i == $localtime['tm_year'] + 1900) {
                    $output .= '<option selected>' . $i;
                } else {
                    $output .= '<option>' . $i;
                }
            }
            $output .= '</select> - <select name="'.$name.'[mon]">';
            for ($i = 1; $i <= 12; $i++) {
                if ($i == $localtime['tm_mon'] + 1) {
                    $output .= '<option selected>' . $i;
                } else {
                    $output .= '<option>' . $i;
                }
            }
            $output .= '</select> - <select name="'.$name.'[mday]">';
            for ($i = 1; $i <= 31; $i++) {
                if ($i == $localtime['tm_mday']) {
                    $output .= '<option selected>' . $i;
                } else {
                    $output .= '<option>' . $i;
                }
            }
            $output .= '</select> ';
            $output .= xarML('Time') . ' <select name="'.$name.'[hour]">';
            for ($i = 0; $i < 24; $i++) {
                if ($i == $localtime['tm_hour']) {
                    $output .= '<option selected>' . sprintf("%02d",$i);
                } else {
                    $output .= '<option>' . sprintf("%02d",$i);
                }
            }
            $output .= '</select> : <select name="'.$name.'[min]">';
            for ($i = 0; $i < 60; $i++) {
                if ($i == $localtime['tm_min']) {
                    $output .= '<option selected>' . sprintf("%02d",$i);
                } else {
                    $output .= '<option>' . sprintf("%02d",$i);
                }
            }
            $output .= '</select> : <select name="'.$name.'[sec]">';
            for ($i = 0; $i < 60; $i++) {
                if ($i == $localtime['tm_sec']) {
                    $output .= '<option selected>' . sprintf("%02d",$i);
                } else {
                    $output .= '<option>' . sprintf("%02d",$i);
                }
            }
            $output .= '</select> ';
            break;
    // yes, we auto-declare the allowed field types here too :-)
        case 'fieldtype':
            // Get the list of defined field formats
            $pubfieldformats = xarModAPIFunc('articles','user','getpubfieldformats');

        // Note: if we want to re-use this for dynamic data, id's need to be numeric
            if (!empty($numeric_id)) {
                $fieldformatnums = xarModAPIFunc('articles','user','getfieldformatnums');
            }

            $output .= '<select name="'.$name.'"'.$id.$tabindex.'>';
            foreach ($pubfieldformats as $fid => $fname) {
                if (!empty($numeric_id)) {
                    $numid = $fieldformatnums[$fid];
                    $output .= '<option value="'.$numid.'"';
                    if ($numid == $value) {
                        $output .= ' selected';
                    }
                } else {
                    $output .= '<option value="'.$fid.'"';
                    if ($fid == $value) {
                        $output .= ' selected';
                    }
                }
                $output .= '>'.$fname.'</option>';
            }
            $output .= '</select>';
            break;
        default:
            $output .= 'Unknown type '.$type;
            break;
    }
    return $output;
}

?>
