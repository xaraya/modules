<?php
/**
 * Xaraya Google Search
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Google Search Module
 * @link http://xaraya.com/index.php/release/809.html
 * @author John Cox
 */
function googlesearch_admin_viewcached()
{
  xarVarFetch('cache', 'str', $cachePage, '', XARVAR_NOT_REQUIRED);
  xarVarFetch('filter', 'str', $filter, '1', XARVAR_NOT_REQUIRED);
  xarVarFetch('list', 'isset', $list, 'none', XARVAR_NOT_REQUIRED);
  $filters = explode(',', $filter);

  if ($list != 'none') {
    $content = join('', file('var/cache/google/CACHEKEYS'));

  } else if (!empty($cachePage) && file_exists('var/cache/google/'.$cachePage.'.html')) {
    $content = join('', file('var/cache/google/'.$cachePage.'.html'));

    # process the content filters in order
    foreach($filters as $filter) {

      switch($filter) {
      case '0':
        # TODO: do the data extraction
        $found['title'] = preg_match('/<div class="xar-title">\s*([^\(]+)/is', $content, $matches['title']);
        $found['article'] = preg_match('/<div class="xar-a\d+-text">(.*)<\/div>\s+<div class="xar-a\d+-otherlinks">/is', $content, $matches['article']);

        foreach($found as $k=>$v) {
          if ($v) {
            $tplData['extracted'][ucfirst($k)] = $matches[$k][1]; # assume one 1 match per test right now
          } else {
            $tplData['extracted'][ucfirst($k)] = 'Failed to retrieve '.$k;
          }// if
        }// foreach

        $tplData['content'] = $content;
        $content = xarTplModule('googlesearch', 'admin', 'viewcached', $tplData);
        break;

      case '1':
        $search = array('/<div id="footer">.*$/is',
                        '/^.*<div class="contentarea">/is'
                        );
        $content = preg_replace($search, '', $content);
        $search = array(
                        '/^.*<\!-- <br \/> -->\s*<\/div>/is'
                        );
        $content = preg_replace($search, '', $content);
        break;

      case '2':
        $content = '<pre>'.htmlentities($content).'</pre>';
        break;

      case 'base64':
        $content = base64_encode($content);
        break;
      }// switch

    }// foreach

  } else {
    $content = 'Requested page '.$cachePage.' does not exist ('.'var/cache/google/'.$cachePage.'.html)';

  }// if

  echo $content;
  exit;

}

?>