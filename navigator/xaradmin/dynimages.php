<?php

function navigator_admin_dynimages()
{
    if (!xarSecurityCheck('AdminNavigator')) return;

    // Grab the parent id's / names for use later on
    $primary_list    = xarModGetVar('navigator', 'categories.list.primary');
    $secondary_list  = xarModGetVar('navigator', 'categories.list.secondary');
    $matrix          = xarModGetVar('navigator', 'style.matrix');

    if (empty($primary_list) || empty($secondary_list)) {
        // Redirect to the config page so the admin can setup the default-primaries
        xarResponseRedirect(xarModURL('navigator', 'admin', 'modifyconfig'));
        exit();
    } else {
        $primary_list    = unserialize($primary_list);
        $secondary_list  = unserialize($secondary_list);

        xarModAPIFunc('navigator', 'user', 'nested_tree_flatten', &$primary_list);
        xarModAPIFunc('navigator', 'user', 'nested_tree_flatten', &$secondary_list);

        $plist = $primary_list;
        $slist = $secondary_list;

        foreach ($plist as $key => $item) {
            unset($primary_list[$key]);
            $primary_list[$item['cid']] = $item;
        }

        foreach ($slist as $key => $item) {
            unset($secondary_list[$key]);
            $secondary_list[$item['cid']] = $item;
        }
        $primaries       = implode(':', array_keys($primary_list));
    }

    if (!xarVarFetch('Id',     'enum:left:right',   $Id, NULL, XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('catid',    "enum:0:$primaries", $catid, NULL, XARVAR_NOT_REQUIRED)) { return; }
    if (!xarVarFetch('dynimages',"list:list:int:0:",  $dynimages, NULL, XARVAR_NOT_REQUIRED)) { return; }

    $data['left_label'] = xarML('Left');
    $data['right_label'] = xarML('Right');

    $data['categories']    = array();

    if (!isset($catid) || empty($catid))  {
        $catid = 0;
    }

    // Grab the list of categories->images for this parent category (if there is one)
    $paImageList    = xarModGetVar('navigator', "category.image-list.$catid");

    // Check to make sure the list is not null, if it is
    // then create an empty array and save it for future use
    if (empty($paImageList)) {
        $paImageList = array();
        xarModSetVar('navigator', "category.image-list.$catid", serialize($paImageList));
    } else {
        // Otherwise, unserialize the array
        $paImageList = unserialize($paImageList);
    }

    // Update the list of images
    if (isset($dynimages) && is_array($dynimages) && count($dynimages)) {
        foreach ($dynimages as $key => $ctype) {
            $ctype2 = (isset($paImageList[$key]) ? $paImageList[$key] : NULL);
            $paImageList[$key] = array_merge($ctype2, $ctype);
        }
        xarModSetVar('navigator', 'category.image-list.'.$catid, serialize($paImageList));
    }

    // Grab the default image list
    $defSiteImages  = xarModGetVar('navigator', "category.image-list.0");
    if (isset($defSiteImages) && !empty($defSiteImages)) {
        $defSiteImages = unserialize($defSiteImages);
    }

    $data['paImageList']   = $paImageList;
    $data['defSiteImages'] = $defSiteImages;
    $data['jsURL']         = xarModURL('images', 'user', 'display', array('width' => 64));
    $data['catid'] = $catid;

    switch($Id) {
        case 'left':
            // if we're on the left, only create a link for the right
            $data['right_url'] = xarModURL('navigator', 'admin', 'dynimages', array('Id' => 'right', 'catid' => $catid));
            $data['Id'] = 'left';
            break;
        case 'right':
            // if we're on the left, only create a link for the left
            $data['left_url'] = xarModURL('navigator', 'admin', 'dynimages', array('Id' => 'left', 'catid' => $catid));
            $data['Id'] = 'right';
            break;
        default:
            // if we're not on the left or right, display both links
            $data['right_url'] = xarModURL('navigator', 'admin', 'dynimages', array('Id' => 'right'));
            $data['left_url'] = xarModURL('navigator', 'admin', 'dynimages', array('Id' => 'left'));
            break;
    }

    if (!empty($Id)) {

        $default[0]   = xarML('Default');
        $default[0]   = array('name' => $default[0], 'cid' => 0, 'indent' => 0);
        $primary_list = $default + $primary_list;

        if (0 == $catid) {
            $secondary_list = $default;
        } else {
            $secondary_list = $default + $secondary_list;
        }

        $data['secondary_list'] = $secondary_list;

        foreach($primary_list as $id => $item) {
            $data['categories'][$id]['name'] = $item['name'];
            $data['categories'][$id]['cid']   = $item['cid'];
            $data['categories'][$id]['url']  = xarModURL('navigator', 'admin', 'dynimages',
                                                          array('Id' => $Id,
                                                                'catid' => $item['cid']));
        }

        // remove the url for the currently selected prorgram area
        if (isset($data['categories'][$catid]['url'])) {
            unset($data['categories'][$catid]['url']);
        }

        // remove current (.) and previous (..) directories
        // and spaces and lowercase the program area name
        $image_dir  = getcwd() .'/'. xarTplGetThemeDir() . '/images/navigator';
        $paImageDir = $image_dir . "/$Id";

        $fileList   = xarModAPIFunc('uploads', 'user', 'import_get_filelist',
                                     array('fileLocation' => $paImageDir,
                                           'descend' => FALSE,
                                           'search'  => '.*'.$data['Id'].'.*'));

        $fileListAll = xarModAPIFunc('uploads', 'user', 'import_get_filelist',
                                      array('fileLocation' => $image_dir,
                                            'descend' => TRUE,
                                            'search' => '.*'.$data['Id'].'.*'));

        if (empty($fileList) || !count($fileList)) {
            $msg  = xarML('No images to select.');
            $msg .= xarML('Please add images to the themes/[your theme]/images/navigator directory.');
            $data['secondary_list']['error'] = $msg;
        } else {

            foreach($fileListAll as $key => $file) {

                xarModAPILoad('uploads', 'user');

                if ($file['inodeType'] == _INODE_TYPE_DIRECTORY) {
                    // we don't need directories.
                    continue;
                }

                $args['storeType'] = _UPLOADS_STORE_FSDB;
                $args['action']    = _UPLOADS_GET_EXTERNAL;
                $args['import']    = "file://$file[fileLocation]";
                $file  = end(xarModAPIFunc('uploads','user','process_files', $args));
                $data['fileListAll'][$key] = $file;

                // make the file entries in the Program Area
                // specific filelist match the complete list
                if (isset($fileList[$key])) {
                    $data['fileList'][$key] = $file;
                }

                if (!empty($file) && count($file)) {
                    // PreCreate all thumbs
                    if (!isset($file['fileId']) || empty($file['fileId']) ||
                        !is_numeric($file['fileId'])) {
                            xarDerefData('$file', $file, TRUE);
                    }
                    xarModAPIFunc('images', 'user', 'resize',
                                   array('src' => $file['fileId'],
                                         'width' => '64px',
                                         'label' => 'Image'));
                }
            }
            unset($fileListAll);
            unset($fileList);
        }
    }

    return $data;
}

?>
