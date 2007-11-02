<?php

/**
 * Transforms image into thumbnail with JS Popup
 * @param text string Body of text to filter out all the images
 * @param template string Template to use for the thumbnail wrapper; overrides the default
 */

function mag_userapi_imgfilter($args, $template = 'thumbnail-wrapper')
{
    extract($args);
    
    // Get Module Paramaters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,image_article_embedded_thumb_vpath,image_article_thumb_classes'
        )
    ));

    // Include the parser class
    require_once(dirname(__FILE__) . '/../xarincludes/htmlparser.inc.php');

    // Filter out all the images
    preg_match_all('/<img[^>]+>/i', $text, $matches, PREG_PATTERN_ORDER);
    
    // Initilise the array tags we'll be using as replacement for the original images
    $searchTags = array();
    $replacementTags = array();

    if (isset($matches[0]) && is_array($matches[0])) {
        foreach ($matches[0] as $image_tag) {
            // We create a new parser each time, since there is no way to reset
            // the parser with a new input string (there ought to be).
            $htmlparser = new HtmlParser($image_tag);

            // Parse the image tag.
            // There will be only one tag, so we don't need to loop over the
            // input to parse.
            if (!$htmlparser->parse()) continue;

            // Validation Checks
            // Is it an element?
            if ($htmlparser->iNodeType != NODE_TYPE_ELEMENT) continue;

            // Is it an img element?
            if ($htmlparser->iNodeName != 'img') continue;
            
            // An img node doesn't contain a value
            if (!empty($htmlparser->iNodeValue)) continue;

            // Set an array of the img attributes
            $imgAttr = ($htmlparser->iNodeAttributes);

            if (empty($imgAttr)) continue;

            // Get an array of classes for this image
            $imgClasses = explode(' ', $imgAttr['class']);
            
            // Class - "thumbnailed" is a flag to make it a thumbnail
            if (array_intersect($image_article_thumb_classes, $imgClasses)) {

                // Fetch the path to the thumbnail
                $thumbnail_path = xarModAPIfunc('mag', 'user', 'imagepaths',
                    array(
                        'path' => $image_article_embedded_thumb_vpath,
                        'fields' => array('image' => $imgAttr['src'],)
                    )
                );

                // Check if the thumbnail image exists
                if (empty($thumbnail_path)) continue;

                // Manipulation
                // Change the image source to it's thumbnail
                $oldImg = $imgAttr;
                $newImg = $imgAttr;
                $newImg['src'] = str_replace('.', '.thumb.', $oldImg['src']);

                // Defaults
                if (empty($newImg['title'])) $newImg['title'] = '';
                if (empty($newImg['alt'])) $newImg['alt'] = '';
                if (empty($newImg['style'])) $newImg['style'] = '';

                // Create new image tag
                $newTag = xarTpl_includeModuleTemplate("mag", $template, 
                    array(
                        'oldImg' => $oldImg,
                        'newImg' => $newImg,
                        'imgClasses' => $imgClasses,
                    )
                );

                // Add to the array of replacement tags
                $searchTags[] = $image_tag;
                $replacementTags[] = $newTag;
            }
        }
    }

    // Do the string replace, if we have anything to replace.
    if (!empty($searchTags)) $text = str_replace($searchTags, $replacementTags, $text);

    return $text;
}

?>