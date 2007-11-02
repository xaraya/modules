<?php

/**
 * Transform image path templates into virtual directories.
 * Will also test whether the image exists, and if not, either
 * falls back onto alternative images or returns an empty string.
 *
 * - Paths can have separate sections, separated by a semi-colon.
 * - The first section to match will be returned.
 * - Standard replacement fields are defined here.
 * - Additional replacement fields in context can be passed in.
 * - Continuous runs of '/' will be compressed to a single '/'.
 * - Tags passed in do not need '/' prefixes or suffixes.
 * - A custom tag name can be suffixed with '_fileext' to match just the extension (includes the '.').
 * - A custom tag name can be suffixed with '_filename' to match just the filename (body and extension).
 * - A custom tag name can be suffixed with '_filebody' to match just the filebody.
 * - A custom tag name can be suffixed with '_dirname' to match just the directory name.
 * - Tags are names surrounded by {curly_braces}
 * - Standard tags are: {base_image_vpath}
 *
 * @param path string The image template path.
 * @param fields array Array of name->value pairs, to be used as tags.
 * @return string The virtual directory, relative to the entry point.
 *
 */

function mag_userapi_imagepaths($args)
{
    extract($args);

    // A path is needed.
    if (empty($path) || !is_string($path)) return '';

    extract(xarModAPIfunc('mag', 'user', 'params',
        array('knames' => 'module,base_image_vpath')
    ));

    // Get standard tags together.
    // TODO: include current {theme}
    $tags = array(
        '{base_image_vpath}' => $base_image_vpath,
        '{module_images}' => dirname(__FILE__) . '/../xarimages',
    );

    // Add the user-defined tags.
    if (!empty($fields) && is_array($fields)) {
        foreach($fields as $key => $field) {
            $tags['{' . $key . '}'] = $field;
            $tags['{' . $key . '_filedir' . '}'] = dirname($field);
            $filename = basename($field);
            $tags['{' . $key . '_filename' . '}'] = $filename;
            if (strpos($filename, '.')) {
                $tags['{' . $key . '_filebody' . '}'] = substr($filename, 0, strrpos($filename, '.'));
                $tags['{' . $key . '_fileext' . '}'] = substr($filename, strrpos($filename, '.'));
            }
        }
    }

    // Do the substitution on the unexploded string, all in one go.
    $path = str_replace(array_keys($tags), array_values($tags), $path);
    $path = str_replace(array('//', '/./'), array('/', '/'), $path);

    $paths = explode(';', $path);
    foreach($paths as $path) {
        // Test the image exists, and return the path if it does.
        // Make sure file is readable and is not a special file (e.g. a directory)
        if (file_exists($path) && is_readable($path) && !is_dir($path)) return $path;
    }

    // No files were found.
    return '';
}

?>