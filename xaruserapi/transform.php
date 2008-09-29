<?php
/**
 * Xaraya Smilies
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Smilies Module
 * @author Jim McDonald, Mikespub, John Cox
*/
/**
 * transform text
 * @param $args['extrainfo'] string or array of text items
 * @returns string
 * @return string or array of transformed text items
 */
function smilies_userapi_transform($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($extrainfo)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    // When called via hooks, modname will be empty, but we get it from the
    // extrainfo or from the current module
    if (empty($modname) || !is_string($modname)) {
        if (isset($extrainfo) && is_array($extrainfo) &&
            isset($extrainfo['module']) && is_string($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        } else {
            $modname = xarModGetName();
        }
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
         if (isset($extrainfo) && is_array($extrainfo) &&
             isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
             $itemtype = $extrainfo['itemtype'];
         } else {
             $itemtype = 0;
         }
    }
    
    xarVarSetCached('Hooks.smilies', 'modname', $modname);
    xarVarSetCached('Hooks.smilies', 'itemtype', $itemtype);


    // Bug 5771: allow disable smilies on the fly
    $nosmilies = xarVarIsCached('Hooks.smilies', 'nosmilies') ? xarVarGetCached('Hooks.smilies', 'nosmilies') : false;

    if ($nosmilies) return $extrainfo;

    if (is_array($extrainfo)) {
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] = smilies_userapitransform($extrainfo[$key]);
                }
            }
            return $extrainfo;
        }
        $transformed = array();
        foreach($extrainfo as $text) {
            $transformed[] = smilies_userapitransform($text);
        }
    } else {
        $transformed = smilies_userapitransform($extrainfo);
    }

    return $transformed;
}

function smilies_userapitransform($text)
{
    static $alsearch = array();
    static $alreplace = array();
    static $gotsmilies = 0;
    // Bug 3957:
    static $gottags = 0;
    static $skiptags = array();

    if (empty($gotsmilies)) {
        $gotsmilies = 1;
        $tmpsmilies = xarModAPIFunc('smilies', 'user', 'getall');
        // Bug 5271:
        if (!xarModGetVar('smilies', 'allowhookoverride')) {
          $image_folder = xarModGetVar('smilies', 'image_folder');
        } else {
          $modname = xarVarGetCached('Hooks.smilies', 'modname');
          $itemtype = xarVarGetCached('Hooks.smilies', 'itemtype');
          if (empty($itemtype)) $itemtype = 0;
          $image_folder = !empty($modname) ? xarModGetVar($modname, 'image_folder.'.$itemtype) : xarModGetVar('smilies', 'image_folder');
        }
        
        if (!empty($image_folder)) {
          $themedir = xarTplGetThemeDir();
          // make sure we have a folder somewhere by this name
          if (!file_exists('modules/smilies/xarimages/'.$image_folder) && !file_exists($themedir.'/modules/smilies/images/'.$image_folder)) {
            // and if not, use the default folder
            $image_folder = '';
          }
        }

        // Create search/replace array from autolinks information
        foreach ($tmpsmilies as $tmpsmiley) {
            // Munge word boundaries to stop autolinks from linking to
            // themselves or other autolinks in step 2
            // $tmpsmiley['icon'] = preg_replace('/(\b)/', '\\1ALSPACEHOLDER', $tmpsmiley['icon']);

            // Escape any special characters in the smile code right from the start.
            // It should not be necessary for the admin to put in preg escape codes.
            $tmpsmiley_code = preg_quote($tmpsmiley['code'], '/');

            // Allow matches for smiles with < and > entities (note > and < will have been escaped).
            $tmpsmiley_code = str_replace(array('\>', '\<'), array('(?:&gt;|>)', '(?:&lt;|<)'), $tmpsmiley_code);

            // Bug 5271:
            if (!empty($image_folder)) {
              // look for the smiley in the subfolder of the module and theme images folders
              if (file_exists('modules/smilies/xarimages/'.$image_folder.'/'.$tmpsmiley['icon']) || file_exists($themedir.'/modules/smilies/images/'.$image_folder.'/'.$tmpsmiley['icon'])) {
                // if we found one, use it
                $tmpsmiley['icon']= $image_folder . '/' . $tmpsmiley['icon'];
              }
            }
            // Note use of assertions here to only match specific words,
            // for instance ones that are not part of a hyphenated phrase
            // or (most) bits of an email address
            $alsearch[] = '/(?<![\w@.:-])(' . $tmpsmiley_code . ')(?![\w@:-])(?!\.\w)/i';
            $alreplace[] = '<img src="' . htmlspecialchars(xarTplGetImage($tmpsmiley['icon'], 'smilies')) .
                           '" alt="' . htmlspecialchars(xarML($tmpsmiley['emotion'])) .
                           '" title="' . htmlspecialchars(xarML($tmpsmiley['emotion'])) .
                           '" class="xar-smilies' . // Added for Bug 5829: 
                           '" />';
        }
    }
    
    // Bug 3957: 
    // just like getting the smilies, we only run this once in the lifetime of the script
    if (empty($gottags)) {
      $gottags = 1;
      $seentags = array();
      if (!xarModGetVar('smilies', 'allowhookoverride')) {
        $tagstring = xarModGetVar('smilies', 'skiptags');
      } else {
        $modname = xarVarGetCached('Hooks.smilies', 'modname');
        $itemtype = xarVarGetCached('Hooks.smilies', 'itemtype');
        if (empty($itemtype)) $itemtype = 0;
        $tagstring = !empty($modname) ? xarModGetVar($modname, 'skiptags.'.$itemtype) : xarModGetVar('smilies', 'skiptags');
      }
      if (!empty($tagstring) && is_string($tagstring)) {
        $tagstoskip = unserialize($tagstring);
      }
      if (!empty($tagstoskip)) {
        // modifyconfig should have already made sure we have valid tags, but we make sure anyway
        // TODO: make this list complete
        $alltags = array('div','p','b','a','blockquote','code','table','tr','td','thead','th','tfoot','span','textarea','input','label','fieldset','form','legend');
        foreach ($tagstoskip as $htmltag) {
          // skip invalid tags
          if (!in_array($htmltag, $alltags)) continue;
          $seentags[$htmltag] = 1;
        }
      }
      // add any valid tags to skip to the static array for subsequent use
      $skiptags = !empty($seentags) ? array_keys($seentags) : array();
    }

    // Bug 3957:
    // Step 1a - move all skiptags out of the text and replace with placeholders
    // this is our array of matched tags that we will replace
    $skipped = array();
    if (!empty($skiptags)) {
      foreach ($skiptags as $skiptag) {
        // match any occurences of this tag
        preg_match_all("!<{$skiptag}[^>]+>.*?</{$skiptag}>!is", $text, $tagmatches);
        // add what we found to the matched tags array, using the tag as key so we can put them back in Step 5
        $skipped[$skiptag] = $tagmatches;
        // count how many matches we found
        $skipnum = count($tagmatches[0]);
        for ($i = 0; $i <$skipnum; $i++) {
          // replace each occurence with a unique placeholder based on tag name and occurence
          $text = preg_replace('/' . preg_quote($tagmatches[0][$i], '/') . '/', "*{$skiptag}TAGHOLDER{$i}PH", $text, 1);
        }
      }
    }

    // Step 1b - move all other tags out of the text and replace them with placeholders
    preg_match_all('/(<\w[^>]+>)/i', $text, $matches);
    $matchnum = count($matches[1]);
    for ($i = 0; $i <$matchnum; $i++) {
        // Bug 4123: this replacement conflicts with lookahead regex, added * to the string
        //$text = preg_replace('/' . preg_quote($matches[1][$i], '/') . '/', "ALPLACEHOLDER{$i}PH", $text, 1);
        $text = preg_replace('/' . preg_quote($matches[1][$i], '/') . '/', "*ALPLACEHOLDER{$i}PH", $text, 1);
    }

    // Step 2 - put the smilies in.
    $text = preg_replace($alsearch, $alreplace, $text);

    // Step 3 - replace the spaces we munged in step 2
    // this isn't used, so no need to call it here
    //$text = preg_replace('/ALSPACEHOLDER/', '', $text);

    // Step 4 - replace the HTML tags that we removed in step 1b
    for ($i = 0; $i <$matchnum; $i++) {
        // Bug 4123: this replacement conflicts with the regex in the lookahead, added * to the string
        //$text = preg_replace("/ALPLACEHOLDER{$i}PH/", $matches[1][$i], $text, 1);
        $text = preg_replace("/\*ALPLACEHOLDER{$i}PH/", $matches[1][$i], $text, 1);
    }

    // Bug 3957:
    // Step 5 - replace the tags that we skipped in step 1a (if any)
    if (!empty($skipped)) {
      // get the matched tags we replaced in Step 1a
      foreach ($skipped as $skiptag => $tagmatches) {
        $skipnum = count($tagmatches[0]);
        for ($i = 0; $i <$skipnum; $i++) {
            // replace all occurrences of this tag
            $text = preg_replace("/\*{$skiptag}TAGHOLDER{$i}PH/", $tagmatches[0][$i], $text, 1);
        }
      }
    }
    return $text;
}
?>
