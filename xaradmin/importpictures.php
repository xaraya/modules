<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * import pictures into articles
 */
function articles_admin_importpictures()
{
    if (!xarSecurityCheck('AdminArticles')) return;

    // Get parameters
    if(!xarVarFetch('basedir',      'isset', $basedir,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('baseurl',      'isset', $baseurl,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('thumbnail',    'isset', $thumbnail,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('filelist',     'isset', $filelist,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('refresh',      'isset', $refresh,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('ptid',         'isset', $ptid,         NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('title',        'isset', $title,        NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('summary',      'isset', $summary,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('content',      'isset', $content,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('usefilemtime', 'isset', $usefilemtime, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('cids',         'isset', $cids,         NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('test',         'isset', $test,         NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('import',       'isset', $import,       NULL, XARVAR_DONT_SET)) {return;}

    // Initialise the template variables
    $data = array();

    if (!isset($baseurl)) {
        $data['baseurl'] = 'modules/articles/xarimages/';
    } else {
        $data['baseurl'] = $baseurl;
    }
    if (!isset($basedir)) {
        $data['basedir'] = realpath($data['baseurl']);
    } else {
        $data['basedir'] = realpath($basedir);
    }

    if (!isset($thumbnail)) {
        $data['thumbnail'] = 'tn_';
    } else {
        $data['thumbnail'] = $thumbnail;
    }

    $data['filelist'] = xarModAPIFunc('articles','admin','browse',
                                      array('basedir' => $data['basedir'],
                                            'filetype' => '(gif|jpg|jpeg|png)'));

    // try to match the thumbnails with the pictures
    $data['thumblist'] = array();
    if (!empty($data['thumbnail'])) {
        foreach ($data['filelist'] as $file) {
            // for subdir/myfile.jpg
            $fileparts = pathinfo($file);
            // jpg
            $extension = $fileparts['extension'];
            // subdir
            $dirname = $fileparts['dirname'];
            // myfile
            $basename = $fileparts['basename'];
            $basename = preg_replace("/\.$extension/",'',$basename);
            if (!empty($dirname) && $dirname != '.') {
                $thumb = $dirname . '/' . $data['thumbnail'] . $basename;
            } else {
                $thumb = $data['thumbnail'] . $basename;
            }
            // subdir/tn_file.jpg
            if (in_array($thumb.'.'.$extension,$data['filelist'])) {
                $data['thumblist'][$file] = $thumb.'.'.$extension;

            // subdir/tn_file_jpg.jpg
            } elseif (in_array($thumb.'_'.$extension.'.'.$extension,$data['filelist'])) {
                $data['thumblist'][$file] = $thumb.'_'.$extension.'.'.$extension;

            // subdir/tn_file.jpg.jpg
            } elseif (in_array($thumb.'.'.$extension.'.'.$extension,$data['filelist'])) {
                $data['thumblist'][$file] = $thumb.'.'.$extension.'.'.$extension;

            }
        }
        if (count($data['thumblist']) > 0) {
            $deletelist = array_values($data['thumblist']);
            $data['filelist'] = array_diff($data['filelist'], $deletelist);
        }
    }

    if (isset($refresh) || isset($test) || isset($import)) {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;
    }

    $data['authid'] = xarSecGenAuthKey();

    // Get current publication types
    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

    // Set default pubtype to Pictures (if it exists)
    if (!isset($ptid) && isset($pubtypes[5])) {
        $ptid = 5;
        $title = 'title';
        $summary = 'summary';
        $content = 'body';
    }

    $data['pubtypes'] = $pubtypes;
    $data['fields'] = array();
    $data['cats'] = array();
    if (!empty($ptid)) {
        $data['ptid'] = $ptid;

        $pubfields = xarModAPIFunc('articles','user','getpubfields');
        $pubfieldtypes = xarModAPIFunc('articles','user','getpubfieldtypes');
        $pubfieldformats = xarModAPIFunc('articles','user','getpubfieldformats');
        foreach ($pubfields as $field => $dummy) {
            if (($pubfieldtypes[$field] == 'text' || $pubfieldtypes[$field] == 'string') &&
                !empty($pubtypes[$ptid]['config'][$field]['label']) &&
                $pubtypes[$ptid]['config'][$field]['format'] != 'fileupload') {
                $data['fields'][$field] = $pubtypes[$ptid]['config'][$field]['label'] . ' [' .
                                          $pubfieldformats[$pubtypes[$ptid]['config'][$field]['format']] . ']';
            }
        }

        $catlist = array();
        $rootcats = unserialize(xarModGetUserVar('articles','basecids',$ptid));
        foreach ($rootcats as $catid) {
            $catlist[$catid] = 1;
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
            $data['cats'][] = xarModAPIFunc('categories',
                                            'visual',
                                            'makeselect',
                                            Array('cid' => $catid,
                                                  'return_itself' => true,
                                                  'select_itself' => true,
                                                  'values' => &$seencid,
                                                  'multiple' => 1));
        }
    }

    $data['selected'] = array();
    if (!isset($refresh) && isset($filelist) && is_array($filelist) && count($filelist) > 0) {
        foreach ($filelist as $file) {
            if (!empty($file) && in_array($file,$data['filelist'])) {
                $data['selected'][$file] = 1;
            }
        }
    }

    if (isset($title) && isset($data['fields'][$title])) {
        $data['title'] = $title;
    }
    if (isset($summary) && isset($data['fields'][$summary])) {
        $data['summary'] = $summary;
    }
    if (isset($content) && isset($data['fields'][$content])) {
        $data['content'] = $content;
    }
    if (empty($usefilemtime)) {
        $data['usefilemtime'] = 0;
    } else {
        $data['usefilemtime'] = 1;
    }

    if (isset($data['ptid']) && isset($data['content']) && count($data['selected']) > 0
        && (isset($test) || isset($import))) {

// TODO: allow changing the order of import + editing the titles etc. before creating the articles

        $data['logfile'] = '';
        foreach (array_keys($data['selected']) as $file) {
            $curfile = realpath($basedir . '/' . $file);
            if (!file_exists($curfile) || !is_file($curfile)) {
                continue;
            }

            $filename = $file;
            if (empty($baseurl)) {
                $imageurl = $file;
            } elseif (substr($baseurl,-1) == '/') {
                $imageurl = $baseurl . $file;
            } else {
                $imageurl = $baseurl . '/' . $file;
            }
            if (!empty($data['thumblist'][$file])) {
                if (empty($baseurl)) {
                    $thumburl = $data['thumblist'][$file];
                } elseif (substr($baseurl,-1) == '/') {
                    $thumburl = $baseurl . $data['thumblist'][$file];
                } else {
                    $thumburl = $baseurl . '/' . $data['thumblist'][$file];
                }
            } else {
                $thumburl = '';
            }

            $article = array('title' => ' ',
                             'summary' => '',
                             'body' => '',
                             'notes' => '',
                             'pubdate' => (empty($usefilemtime) ? time() : filemtime($curfile)),
                             'status' => 2,
                             'ptid' => $data['ptid'],
                             'cids' => $cids,
                          // for preview
                             'pubtypeid' => $data['ptid'],
                             'authorid' => xarUserGetVar('id'),
                             'id' => 0);
            if (!empty($data['title']) && !empty($filename)) {
                $article[$data['title']] = $filename;
            }
            if (!empty($data['summary']) && !empty($thumburl)) {
                $article[$data['summary']] = $thumburl;
            }
            if (!empty($data['content']) && !empty($imageurl)) {
                $article[$data['content']] = $imageurl;
            }
            if (isset($test)) {
                // preview the first file as a test
                $data['preview'] = xarModFunc('articles','user','display',
                                              array('article' => $article, 'preview' => true));
                break;
            } else {
                $id = xarModAPIFunc('articles', 'admin', 'create', $article);
                if (empty($id)) {
                    return; // throw back
                } else {
                    $data['logfile'] .= xarML('File #(1) was imported as #(2) #(3)',$curfile,$pubtypes[$data['ptid']]['descr'],$id);
                    $data['logfile'] .= '<br />';
                }
            }
        }
    }

    // Return the template variables defined in this function
    return $data;
}

?>
