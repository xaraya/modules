<?php
/**
 * Featured items
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 *
 */
/**
 * Featured initialise block
 *
 * @author Jonn Beams (based on code from TopItems block)
 *
 */

function publications_featureditemsblock_init()
{
    return array(
        'featuredid'       => 0,
        'alttitle'          => '',
        'altsummary'        => '',
        'moreitems'         => array(),
        'toptype'           => 'date',
        'showvalue'         => true,
        'pubtype_id'         => '',
        'catfilter'         => '',
        'state'            => array(3, 2),
        'itemlimit'         => 10,
        'showfeaturedsum'   => false,
        'showfeaturedbod'   => false,
        'moreitems'         => array(),
        'showsummary'       => false,
        'linkpubtype'       => false,
        'linkcat'           => false
    );
}

/**
 * get information on block
 */
function publications_featureditemsblock_info()
{
    // Details of block.
    return array(
        'text_type'         => 'Featured Items',
        'module'            => 'publications',
        'text_type_long'    => 'Show featured publications',
        'allow_multiple'    => true,
        'form_content'      => false,
        'form_refresh'      => false,
        'show_preview'      => true
    );
}

/**
 * display block
 */
function publications_featureditemsblock_display(& $blockinfo)
{
    // Security check
    // TODO: can be removed when handled centrally.
    if (!xarSecurityCheck('ReadPublicationsBlock', 0, 'Block', $blockinfo['title'])) {return;}

    // Get variables from content block
    if (is_string($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars =& $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['featuredid'])) {$vars['featuredid'] = 0;}
    if (empty($vars['alttitle'])) {$vars['alttitle'] = '';}
    if (empty($vars['altsummary'])) {$vars['altsummary'] = '';}
    if (empty($vars['toptype'])) {$vars['toptype'] = 'date';}
    if (empty($vars['moreitems'])) {$vars['moreitems'] = array();}
    if (empty($vars['linkcat'])) {$vars['linkcat'] = false;}
    if (!isset($vars['showvalue'])) {
        if ($vars['toptype'] == 'rating') {
            $vars['showvalue'] = false;
        } else {
            $vars['showvalue'] = true;
        }
    }

    $featuredid = $vars['featuredid'];

    $fields = array('id', 'title', 'cids');

    $fields[] = 'dynamicdata';

    // Initialize arrays
    $data['feature'] = array();
    $data['items'] = array();

    // Setup featured item
    if ($featuredid > 0) {

        if (xarModIsHooked('uploads', 'publications', $vars['pubtype_id'])) {
            xarVarSetCached('Hooks.uploads','ishooked',1);
        }

          if($featart = xarModAPIFunc(
            'publications','user','getall',
            array(
                'ids' => array($featuredid),
                'extra' => array('cids','dynamicdata')))) {

                foreach($featart as $featuredart) {

            $fieldlist = array('id', 'title', 'summary', 'owner', 'pubdate',
                               'pubtype_id', 'notes', 'state', 'body', 'cids');

            $featuredlink = xarModURL(
                'publications', 'user', 'display',
                array(
                    'id' => $featuredart['id'],
                    'itemtype' => (!empty($vars['linkpubtype']) ? $featuredart['pubtype_id'] : NULL),
                    'catid' => ((!empty($vars['linkcat']) && !empty($vars['catfilter'])) ? $vars['catfilter'] : NULL)
                )
            );
            if (empty($vars['showfeaturedbod'])) {$vars['showfeaturedbod'] = false;}
            if(!isset($featuredart['cids'])) $featuredart['cids'] = "";

            $feature= array(
                'featuredlabel'     => $featuredart['title'],
                'featuredlink'      => $featuredlink,
                'alttitle'          => $vars['alttitle'],
                'altsummary'        => $vars['altsummary'],
                'showfeaturedsum'   => $vars['showfeaturedsum'],
                'showfeaturedbod'   => $vars['showfeaturedbod'],
                'featureddesc'      => $featuredart['summary'],
                'featuredbody'      => $featuredart['body'],
                'featuredcids'      => $featuredart['cids'],
                'pubtype_id'         => $featuredart['pubtype_id'],
                'featuredid'       => $featuredart['id'],
                'featureddate'      => $featuredart['pubdate']
            );

            // Get rid of the default fields so all we have left are the DD ones
            foreach ($fieldlist as $field) {
                if (isset($featuredart[$field])) {
                    unset($featuredart[$field]);
                }
            }

            // now add the DD fields to the featuredart
            $feature = array_merge($featuredart, $feature);
            $data['feature'][] = $feature;
        }
    }

    // Setup additional items
    $fields = array('id', 'title', 'pubtype_id', 'cids');

    // Added the 'summary' field to the field list.
    if (!empty($vars['showsummary'])) {
        $fields[] = 'summary';
    }

    if ($vars['toptype'] == 'rating') {
        $fields[] = 'rating';
        $sort = 'rating';
    } elseif ($vars['toptype'] == 'hits') {
        $fields[] = 'counter';
        $sort = 'hits';
    } elseif ($vars['toptype'] == 'date') {
        $fields[] = 'pubdate';
        $sort = 'date';
    } else {
       $sort = $vars['toptype'];
    }

    if (!empty($vars['moreitems'])) {
        $publications = xarModAPIFunc(
            'publications', 'user', 'getall',
            array(
                'ids' => $vars['moreitems'],
                'enddate' => time(),
                'fields' => $fields,
                'sort' => $sort
            )
        );

        // See if we're currently displaying an article
        if (xarVarIsCached('Blocks.publications', 'id')) {
            $curid = xarVarGetCached('Blocks.publications', 'id');
        } else {
            $curid = -1;
        }

        foreach ($publications as $article) {
            if ($article['id'] != $curid) {
                $link = xarModURL(
                    'publications', 'user', 'display',
                    array (
                        'id' => $article['id'],
                        'itemtype' => (!empty($vars['linkpubtype']) ? $article['pubtype_id'] : NULL),
                        'catid' => ((!empty($vars['linkcat']) && !empty($vars['catfilter'])) ? $vars['catfilter'] : NULL)
                    )
                );
            } else {
                $link = '';
            }

            $count = '';
            // TODO: find a nice clean way to show all sort types
            if ($vars['showvalue']) {
                if ($vars['toptype'] == 'rating') {
                    $count = intval($article['rating']);
                } elseif ($vars['toptype'] == 'hits') {
                    $count = $article['counter'];
                } elseif ($vars['toptype'] == 'date') {
                    // TODO: make user-dependent
                    if (!empty($article['pubdate'])) {
                        $count = strftime("%Y-%m-%d", $article['pubdate']);
                    } else {
                        $count = 0;
                    }
                } else {
                    $count = 0;
                }
            } else {
                $count = 0;
            }
            if (isset($article['cids'])) {
               $cids=$article['cids'];
            }else{
               $cids='';
            }
            if (isset($article['pubdate'])) {
               $pubdate=$article['pubdate'];
            }else{
               $pubdate='';
            }
            // Pass $desc to items[] array so that the block template can render it
            $data['items'][] = array(
                'label' => $article['title'],
                'link' => $link,
                'count' => $count,
                'cids' => $cids,
                'pubdate' => $pubdate,
                'desc' => ((!empty($vars['showsummary']) && !empty($article['summary'])) ? $article['summary'] : ''),
                'id' => $article['id']
            );
        }
    }}
    if (empty($data['feature']) && empty($data['items'])) {
        // Nothing to display.
        return;
    }

    // Set the data to return.
    $blockinfo['content'] = $data;
    return $blockinfo;
}

/**
 * built-in block help/information system.
 */
function publications_featureditemsblock_help()
{
    // No information yet.
    return '';
}

?>