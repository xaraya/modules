<?php
/**
 * Translations Module
 *
 * @package modules
 * @subpackage translations module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/77.html
 * @author Marc Lutolf <mfl@netspan.ch>
 */

function translations_adminapi_get_object_properties($args)
{
    // Get arguments
    extract($args);

    // Disable any properties that re not translatable
    $translatable = [];
    foreach ($object->properties as $name => $property) {
        if ($property->translatable) {
            $translatable[] = $name;
        }
    }
    sort($translatable);
    return $translatable;
}
