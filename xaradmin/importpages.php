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
 * manage publication types (all-in-one function for now)
 */
function publications_admin_importpages()
{
    if (!xarSecurity::check('AdminPublications')) {
        return;
    }

    // Get parameters
    if (!xarVar::fetch('basedir', 'isset', $basedir, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('filelist', 'isset', $filelist, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('refresh', 'isset', $refresh, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('ptid', 'int', $data['ptid'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('contentfield', 'str', $data['contentfield'], '', xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('titlefield', 'str', $data['titlefield'], '', xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('cids', 'isset', $cids, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('filterhead', 'isset', $filterhead, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('filtertail', 'isset', $filtertail, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('findtitle', 'isset', $findtitle, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('numrules', 'isset', $numrules, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('search', 'isset', $search, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('replace', 'isset', $replace, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('test', 'isset', $test, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('import', 'isset', $import, null, xarVar::DONT_SET)) {
        return;
    }

    # --------------------------------------------------------
#
    # Get the base directory where the html files to be imported are located
#
    if (empty($basedir)) {
        $data['basedir'] = realpath(sys::code() . 'modules/publications');
    } else {
        $data['basedir'] = realpath($basedir);
    }

    $data['filelist'] = xarMod::apiFunc(
        'publications',
        'admin',
        'browse',
        array('basedir' => $data['basedir'],
                                            'filetype' => 'html?')
    );

    if (isset($refresh) || isset($test) || isset($import)) {
        // Confirm authorisation code
        if (!xarSec::confirmAuthKey()) {
            return;
        }
    }

    $data['authid'] = xarSec::genAuthKey();

    # --------------------------------------------------------
#
    # Get the current publication types
#
    $pubtypes = xarMod::apiFunc('publications', 'user', 'get_pubtypes');

    $data['pubtypes'] = array();
    foreach ($pubtypes as $pubtype) {
        $data['pubtypes'][] = array('id' => $pubtype['id'], 'name' => $pubtype['description']);
    }
    $data['fields'] = array();
    $data['cats'] = array();
    if (!empty($data['ptid'])) {

# --------------------------------------------------------
#
        # Get the fields of hte chosen pubtype
#
        sys::import('modules.dynamicdata.class.objects.master');
        $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
        $pubtypeobject->getItem(array('itemid' => $data['ptid']));
        $objectname = $pubtypeobject->properties['name']->value;
        $pageobject = DataObjectMaster::getObject(array('name' => $objectname));
    
        foreach ($pageobject->properties as $name => $property) {
            if ($property->basetype == 'string') {
                $data['fields'][] = array('id' => $name, 'name' => $property->label);
            }
        }
        /*
                $catlist = array();
                $rootcats = xarMod::apiFunc('categories','user','getallcatbases',array('module' => 'publications','itemtype' => $data['ptid']));
                foreach ($rootcats as $catid) {
                    $catlist[$catid['category_id']] = 1;
                }
                $seencid = array();
                if (isset($cids) && is_array($cids)) {
                    foreach ($cids as $catid) {
                        if (!empty($catid)) {
                            $seencid[$catid] = 1;
                        }
                    }
                }
                $cids = array_keys($seencid);
                foreach (array_keys($catlist) as $catid) {
                    $data['cats'][] = xarMod::apiFunc('categories',
                                                    'visual',
                                                    'makeselect',
                                                    Array('cid' => $catid,
                                                          'return_itself' => true,
                                                          'select_itself' => true,
                                                          'values' => &$seencid,
                                                          'multiple' => 1));
                }
                */
    }

    # --------------------------------------------------------
#
    # Get the data from the form
#
    $data['selected'] = array();
    if (!isset($refresh) && isset($filelist) && is_array($filelist) && count($filelist) > 0) {
        foreach ($filelist as $file) {
            if (!empty($file) && in_array($file, $data['filelist'])) {
                $data['selected'][$file] = 1;
            }
        }
    }

    if (!isset($filterhead)) {
        $data['filterhead'] = '#^.*<body[^>]*>#is';
    } else {
        $data['filterhead'] = $filterhead;
    }
    if (!isset($filtertail)) {
        $data['filtertail'] = '#</body.*$#is';
    } else {
        $data['filtertail'] = $filtertail;
    }
    if (!isset($findtitle)) {
        $data['findtitle'] = '#<title>(.*?)</title>#is';
    } else {
        $data['findtitle'] = $findtitle;
    }

    if (!isset($numrules)) {
        $numrules = 3;
    }
    $data['search'] = array();
    $data['replace'] = array();
    for ($i = 0; $i < $numrules; $i++) {
        if (isset($search[$i])) {
            $data['search'][$i] = $search[$i];
            if (isset($replace[$i])) {
                $data['replace'][$i] = $replace[$i];
            } else {
                $data['replace'][$i] = '';
            }
        } else {
            $data['search'][$i] = '';
            $data['replace'][$i] = '';
        }
    }

    # --------------------------------------------------------
#
    # Perform the import
#
    if (!empty($data['ptid']) && isset($data['contentfield']) && count($data['selected']) > 0
        && (isset($test) || isset($import))) {
        $mysearch = array();
        $myreplace = array();
        for ($i = 0; $i < $numrules; $i++) {
            if (!empty($data['search'][$i])) {
                $mysearch[] = $data['search'][$i];
                if (!empty($data['replace'][$i])) {
                    $myreplace[] = $data['replace'][$i];
                } else {
                    $myreplace[] = '';
                }
            }
        }

        $data['logfile'] = '';
        foreach (array_keys($data['selected']) as $file) {
            $curfile = realpath($basedir . '/' . $file);
            if (!file_exists($curfile) || !is_file($curfile)) {
                continue;
            }
            $page = @join('', file($curfile));
            if (!empty($data['findtitle']) && preg_match($data['findtitle'], $page, $matches)) {
                $title = $matches[1];
            } else {
                $title = '';
            }
            if (!empty($data['filterhead'])) {
                $page = preg_replace($filterhead, '', $page);
            }
            if (!empty($data['filtertail'])) {
                $page = preg_replace($filtertail, '', $page);
            }
            if (count($mysearch) > 0) {
                $page = preg_replace($mysearch, $myreplace, $page);
            }

            $args[$data['contentfield']] = $page;
            if (!empty($data['titlefield'])) {
                $args[$data['titlefield']] = $title;
                $args['name'] = str_replace(' ', '_', trim(strtolower($title)));
            }
            $pageobject = DataObjectMaster::getObject(array('name' => $objectname));
            $pageobject->setFieldValues($args, 1);

            if (isset($test)) {
                // preview the first file as a test
                $data['preview'] = xarMod::guiFunc(
                    'publications',
                    'user',
                    'preview',
                    array('object' => $pageobject)
                );
                break;
            } else {
                $id = $pageobject->createItem();
                if (empty($id)) {
                    return; // throw back
                } else {
                    $data['logfile'] .= xarML('File #(1) was imported as #(2) #(3)', $curfile, $pubtypes[$data['ptid']]['description'], $id);
                    $data['logfile'] .= '<br />';
                }
            }
        }
    }

    $data['filterhead'] = xarVar::prepForDisplay($data['filterhead']);
    $data['filtertail'] = xarVar::prepForDisplay($data['filtertail']);
    $data['findtitle'] = xarVar::prepForDisplay($data['findtitle']);
    for ($i = 0; $i < $numrules; $i++) {
        if (!empty($data['search'][$i])) {
            $data['search'][$i] = xarVar::prepForDisplay($data['search'][$i]);
        }
        if (!empty($data['replace'][$i])) {
            $data['replace'][$i] = xarVar::prepForDisplay($data['replace'][$i]);
        }
    }

    return $data;
}
