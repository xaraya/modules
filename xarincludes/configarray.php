<?php
/**
 * Configuration setup
 *
 * @package modules
 * @copyright (C) 2004-2010 2skies.com
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html} 
 * @link http://xarigami.com/project/xartinymce
 *
 * @subpackage xartinymce module
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/* 
Config name - the name of the configuration option
Always - this configuration setting will always be included in the javascript
DD Field value - this is the type of input field used in the form for instance definition
Validation - used in xarvarfetch
Label - label used for form page
Description - used for form page
Advanced - only show in advanced mode

*/
/*                          Config name          Always   Advanced   DD Field val        Validation Default     Label      Description */
$configs = array(
    'Instance'    =>  array( 'iname'        => array(1,  'text',     'str:1:48',     '' ,       xarML('Short name'),        'A Unique Short name for this editor instance configuration',    'simpform'),
                             'desc'         => array(1,  'text',     'str:0:128',    '',                xarML('Description'),       'Description of this editor configuration instance',    'simpform'),
                             'active'      => array(1,  'boolean',  'checkbox',     'false',    xarML('Active?'),           'Activate this configuration?',                         'simpform'),
                             'useswitch'   => array(1,  'boolean',  'checkbox',     'true',     xarML('Use on/off toggle?'), 'Use an on/off toggle switch to turn on the editor',   'simpform'),
                             'usegzp'      => array(1,  'boolean',  'checkbox',     'false',    xarML('Use js compressor?'),     'Use the compressor for javascript?',              'simpform'),
                             'autoload'    => array(1,  'boolean',  'checkbox',     'true',     xarML('Autoload?'),        'Start TinyMCE automatically when the page loads?',     'simpform'),
                        ),

    'General'     =>  array('browsers'                => array(1,   'text',     'strlist:,:pre:lower:trim:passthru:enum:msie:gecko:safari:opera', 'msie,gecko,safari,opera', xarML('Supported browsers'),'Supported browsers for this instance', 'simpform'),
                              'editor_selector'         => array(0, 'text',     'str:0:48',     'mceEditor',    xarML('CSS selector class'),'Unique CSS class to act as selector to turn on editor instance. For multiconfigs and specific textareas, must be same name as the name of this instance configuration. Use default mceEditor for automatic all textarea loading.', 'simpform'),
                              'editor_deselector'       => array(1, 'text',     'str:0:48',     'mceNoEditor',  xarML('CSS deselector class'), 'CSS class to act as deselector for editor instance - also the name of the editor instance', 'simpform'),
                              'mode'                    => array(1, 'dropdown', 'str',          'specific_textareas',    xarML('Conversion method'), 'Specifies how elements are converted into editor instances. For All Textareas and autoloading the CSS Selector must be the default - mceEditor', 'simpform'),
                              'theme'                   => array(1, 'dropdown',  'str:1:',      'advanced',     xarML('Theme'),     'Theme to use for the tinymce instance', 'simpform'),
                              'language'                => array(1, 'dropdown', 'str:1:',       'en',           xarML('Language'), 'Language code of language pack to use', 'simpform'),
                              'directionality'          => array(0, 'dropdown', 'str:0:3',      'ltr',          xarML('Writing direction'),       'Writing direction for the language', 'advform'),
                              'accessibility_warnings'  => array(0, 'boolean',  'checkbox', 'true',    xarML('Use accessibilty warning?'), 'Warn when some accessibility options are not specified','advform'),
                              'auto_focus'              => array(0, 'text',     'str',      '',    xarML('Auto focus?'),     'Auto focus an editor instance of specific div or textarea ID','advform'),
                              'class_filter'            => array(0, 'text',     'str',      '',     xarML('Class filter function'), 'Specify a function that all classes will be passed through when auto import class feature is used','advform'),
                              'custom_shortcuts'        => array(0, 'boolean',  'checkbox', 'true', xarML('Use custom keyboard shortcuts?'), 'Disable or enable custom keyboard shortcuts','advform'),
                              'dialog_type'             => array(0, 'dropdown', 'str',      'window',   xarML('Popup opening method'), 'Specify whether dialogs and popus should use window or modal opening','advform'),
                              'elements'                => array(0, 'text',     'str',      '',      xarML('Element IDs'), 'Comma separated list of element IDs to convert to editor instances, only used when "mode" is set to "exact" ','advform'),
                              'gecko_spellcheck'        => array(0, 'boolean',  'checkbox', 'false',    xarML('Use Gecko/Firefox spellchecker?'), 'Toggle internal Gecko/Firefox spellchecker logic','advform'),
                              'keep_styles'             => array(0, 'boolean',  'checkbox', 'true',     xarML('Retain style on ENTER?'), 'Retain current text style when pressing enter on non-IE browsers','advform'),
                              'nowrap'                  => array(0, 'boolean',  'checkbox', 'false',    xarML('Turn off wordwrap?'),    'Control how whitespace is wordwrapped','advform'),
                              'object_resizing'         => array(0, 'boolean',  'checkbox', 'true',     xarML('Use object resizing?'), 'Turn on and off inline resizing controls of tables, images in Firefox/Mozilla','advform'),
                              'plugins'                 => array(0, 'checkboxlist', 'isset',       '',      xarML('List of plugins to use'),      'List of plugins used in an editor instance. You must also add any relevant buttons to the button lists.', 'simpform'),
                              'readonly'                => array(0, 'boolean',  'checkbox', 'false',    xarML('Read only editor text?'),    'Instance is read only','advform'),
                              'skin'                    => array(0, 'text',     'str',      'default',  xarML('Skin'), 'Specify the skin to use for a theme','advform'),
                              'skin_variant'            => array(0, 'text',     'str',      '',        xarML('Skin variant'),    'Name of variant for a skin','advform'),
                              'strict_loading_mode'     => array(0, 'boolean',  'checkbox', 'false',    xarML('Use DOM insert method?'),    'Force loading of scripts using DOM insert method instead of document.write on Gecko browsers','advform'),
                              'table_inline_editing'    => array(0, 'boolean',  'checkbox', 'false',    xarML('Use inline table editing?'),    'Turn inline table editing on or off in Firefox/Mozilla',1),
                        ),

    'URL'         =>    array(
                               'relative_urls'           => array(0, 'boolean',  'checkbox', 'true',    xarML('Allow relative URLS from baseurl'),    'All URLs returned from the MCFileManager will be relative from the specified document_base_url','advform'),
                               'convert_urls'            => array(0, 'boolean',  'checkbox', 'true',    xarML('Use relative URLS (instead of absolute)?'),    'URLs will be forced to be either absolute or relative depending on the state of relative_urls','advform'),
                               'remove_script_host'      => array(0, 'boolean',  'checkbox', 'true',    xarML('Remove protocol and host part of URL'),    'The protocol and host part of the URLs returned from the MCFileManager will be removed. This option is only used if the relative_urls option is set to false','advform'),
                               'document_base_url'       => array(1, 'text',     'str',      xarServerGetBaseURL(),   xarML('Website Base URL'),    'Specifies the base URL for all relative URLs in the document','simpform'),
                        ),
                        
    'Undo/Redo'   =>    array('custom_undo_redo'                    => array(0, 'boolean',  'checkbox', 'true',  xarML('Enable custom undo/redo logic'),    'Enable the custom undo/redo logic within TinyMCE','advform'),
                              'custom_undo_redo_levels'             => array(0, 'numeric',  'str',      '-1',    xarML('# of undo levels to keep'),  'The number of undo levels to keep in memory where -1 is unlimited','advform'),
                              'custom_undo_redo_keyboard_shortcuts' => array(0, 'boolean',  'checkbox',  'true',  xarML('Allow keyboard shorcuts for undo/redo?'),    'Enable the usage of keyboard shortcuts for undo/redo','advform'),
                              'custom_undo_redo_restore_selection'  => array(0, 'boolean',  'checkbox',  'true',  xarML('Restore cursor/selection on undo/redo?'),    'Turn on/off the restoration of the cursor/selection when a undo/redo event occurs','advform'),
                        ), 
                        
      'Layout'      => array('body_id'              => array(0, 'text',     'str',      '',         xarML('ID for editor body'), 'An id for the body of the editor instance which can then be used to do TinyMCE specific overrides in your content_css.','advform'),
                              'body_class'          => array(0, 'text',     'str',      '',         xarML('CSS class for editor body'), 'A class for the body of an instance that can be used to do TinyMCE specific overrides in your content_css.','advform'),
                              'constrain_menus'     => array(0, 'boolean',  'checkbox',   'true',   xarML('Constrain all menus to viewport?'),   'Force all menus to be constrained to the current view port','advform'),
                              'content_css'         => array(0, 'text',     'str',      '',         xarML('CSS file for editor content'), 'A custom CSS file that extends the theme content CSS used within the editable area','simpform'),
                              'popup_css'           => array(0, 'text',     'str',      '',         xarML('CSS for popups'), 'CSS to be used in all popup/dialog windows and set to a CSS file found in the currently used theme by default.','simpform'),
                              'popup_css_add'       => array(0, 'text',     'str',      '',         xarML('CSS to add to current popup CSS'), 'Add a CSS to be used in all popup/dialog windows within TinyMCE','advform'),
                              'editor_css'          => array(0, 'text',     'str',      '',         xarML('CSS file for editor toolbars/UI'), 'Specify the CSS file to be used for the editor toolbars/user interface of TinyMCE. By default, this option is set to a CSS file found in the currently used theme','advform'),
                              'width'               => array(0, 'text',     'str',      '',         xarML('Editor width (px or %)'), 'Specify the width of the editor in pixels or percent','simpform'),
                              'height'              => array(0, 'text',     'str',      '',         xarML('Editor height (px or %)'),    'Specify the height of the editor in pixels or percent','simpform'),
                    ),
                            
      'Output'      =>  array('apply_source_formatting'     => array(0, 'boolean',  'checkbox',   'true',   xarML('Format source in HTML code?'),   'Use source formatting in output HTML code','simpform'),
                              'cleanup'                     => array(0, 'boolean',  'checkbox',   'true',   xarML('Enable cleanup on saving?'),   'Disable or enable built in clean up function that filters code on saving input','advform'),
                              'cleanup_on_startup'          => array(0, 'boolean',  'checkbox',   'false',  xarML('Enable cleanup on load?'),   'Perform a HTML cleanup call when the editor loads','advform'),
                              'convert_fonts_to_spans'      => array(0, 'boolean',  'checkbox',   'true',   xarML('Convert all fonts to spans?'),   'Convert all font elements to span elements and generate span instead of font elements','simpform'),
                              'convert_newlines_to_brs'     => array(0, 'boolean',  'checkbox',   'false',  xarML('Convert newline chars to br?'),   'Convert newline character codes to br elements','advform'),
                              'custom_elements'            => array(0, 'text',  'str',         '',          xarML('non-HTML elements for conversion to divs/spans'),   'Specify comma delimited non-HTML elements for the editor that are converted into divs and spans','advform'),
                              'doctype'                    => array(0, 'text',  'str',         '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',       xarML('Default doc type for Editor'),  'Doctype used while editing content within TinyMCE','advform'),
                              'element_format'              => array(0, 'text',  'str',         'xhtml',    xarML('Validation mode'),   'Specify xhtml or html mode','advform'),
                              'encoding'                   => array(0, 'text',  'str',         '',          xarML('Enable xml output'),         'Enables xml escaped contents out of TinyMCE if "xml" is set as value','advform'),
                              'entities'                    => array(0, 'text', 'str',          '',         xarML('Entities'),         'There is an existing default for this. You can change it by providing a comma separated list of entity names used instead of characters if encoding is set as named. Odd items are the character code and even items are the name of the character code','advform'),
                              'entity_encoding'             => array(0, 'dropdown',  'str',         'raw',  xarML('Type of entity encoding'),      'Control entity encoding as named, numeric or raw, ','advform'),
                              'extended_valid_elements'     => array(0, 'textarea',  'str',         '',     xarML('Valid elements added to inbuilt default'),     'Rulset with valid elements to be added to the default set','simpform'),
                              'valid_elements'              => array(0, 'textarea', 'str',          '',     xarML('Define valid elements (replaces inbuilt defaults)'),   'Defines a regex which specifies the elements that will remain in the edited text when the editor saves','advform'),
                              'fix_content_duplication'     => array(0, 'boolean',  'checkbox',   'true',   xarML('Remove duplicate content in IE?'),     'Removes duplicated content due to DOM bug in IE','advform'),
                              'fix_list_elements'           => array(0, 'boolean',  'checkbox',   'false',  xarML('Force XHTML list format?'),    'Convert lists to comply with XHTML format','advform'),
                              'fix_table_elements'           => array(0, 'boolean',  'checkbox',   'false', xarML('Move table elements outside block elements?'),    'Specify that table elements should be moved outside paragraphs or other block elements','advform'),
                              'fix_nesting'                 => array(0, 'boolean',  'checkbox',   'false',  xarML('Convert newline chars to br?'),  'Invalid contents should be corrected before insertion in IE','advform'),
                              'font_size_classes'           => array(0, 'text', 'str',          '',     xarML('Convert newline chars to br?'),     'Specification of a comma separated list of 7 class names that are to be used when the user selects font sizes. This option is only used when the convert_fonts_to_spans option is enabled','advform'),
                              'font_size_style_values'      => array(0, 'text', 'str',          'xx-small,x-small,small,medium,large,x-large,xx-large',       xarML('Fone size list'),   'specification of a comma separated list of style values that is to be used when the user selects font sizes. This option is only used when the convert_fonts_to_spans option is enabled. This list of style values should be 7 items','advform'),
                              'force_p_newlines'            => array(0, 'boolean',  'checkbox',   'true',     xarML('Create paragraph on RETURN?'),     'Disable/enable the creation of paragraphs on return/enter in Mozilla/Firefox','simpform'),
                              'force_br_newlines'           => array(0, 'boolean',  'checkbox',   'false',    xarML('Use br instead of p tags?'),   'Force BR elements on newlines instead of inserting paragraphs','simpform'),
                              'force_hex_style_colors'      => array(0, 'boolean',  'checkbox',   'true',     xarML('Force hex colour format?'),     'Force the color format to use hexadecimal instead of rgb strings','advform'),
                              'forced_root_block'           => array(0, 'boolean',  'checkbox',   'true',     xarML('Force wrapped non-block elements?'),     'Make sure that any non block elements or text nodes are wrapped in block elements','advform'),
                              'indentation'                 => array(0, 'text', 'str',          '30px',       xarML('Indentation size (px)'),    'Specification of the indentation level for indent/outdent buttons in the UI','advform'),
                              'inline_styles'                => array(0,'boolean',  'checkbox',   'true',    xarML('Convert attributes to inline CSS styles?'),    'Attributes get converted into CSS style attributes','simpform'),
                              'invalid_elements'            => array(1, 'text', 'str',          '',           xarML('List of elements to remove on cleanup'),   'A comma separated list of element names to exclude from the content on cleanup','simpform'),
                              'merge_styles_invalid_parents' => array(0,'text','str',          '',           xarML('Elements to exclude from parent merging'),   'Specify a regular expression with elements you want to exclude from parent merging','advform'),
                              'remove_linebreaks'           => array(1, 'boolean',  'checkbox',   'true',     xarML('Remove line break chars?'),    'Line break characters should be removed from output HTML','advform'),
                              'remove_redundant_brs'        => array(0, 'boolean',  'checkbox',   'true',     xarML('Remove redundant BRs?'),    'Output of trailing BR elements at the end of block elements','advform'),
                              'preformatted'                => array(0, 'boolean',  'checkbox',   'false',    xarML('Preserve whitespace?'),    'Whitespace such as tabs and spaces will be preserved','advform'),
                              'valid_child_elements'        => array(0, 'text', 'str',          '',           xarML('Specify valid child elments'),   'Specify what elements are valid inside different parent elements','advform'),
                              'verify_css_classes'          => array(0, 'text',  'str',   '',     xarML('List of valid CSS classes'),    'Class names will be verified against this list and those that do not exist in the CSS will be removed','advform'),
                              'verify_html'                 => array(0, 'boolean',  'checkbox',   'true',      xarML('Toggle HTML tag cleanup on/off'),    'Cleanup HTML elements in addition to other cleanup functionality such as URL conversion will still be executed','advform'),
                              'removeformat_selector'       => array(0, 'boolean',  'checkbox',   'true',      xarML('Remove format elementlist'),   'Specification of which elements should be removed when you press the removeformat button','advform'),
                ),
   
    'Advanced-Theme'=>array('theme_advanced_layout_manager'       => array(0, 'dropdown',  'str',      'SimpleLayout',        xarML('Layout method for editor'),  'Switch button and panel layout functionality between SimpleLayout, RowLayout, and CustomLayout','advform'),
                              'theme_advanced_blockformats'        => array(0, 'text',  'str',      'p,address,pre,h1,h2,h3,h4,h5,h6',       xarML('Block formats in format selector'),  'A comma separated list of formats that will be available in the format drop down list','simpform'),
                              'theme_advanced_styles'               => array(0, 'text', 'str',       '',        xarML('Styles in style selector'),   'A semicolon separated list of class titles and class names separated by = used in the styles dropdown list or imported from content_css if empty','simpform'),
                             'theme_advanced_source_editor_width'  => array(0, 'text',  'str',      '400',      xarML('Source editor width'),     'Define the width of the source editor dialog','advform'),
                              'theme_advanced_source_editor_height' => array(0, 'text', 'str',       '400',     xarML('Source editor height'),    'Define the height of the source editor dialog','advform'),
                              'theme_advanced_source_editor_wrap'   => array(0, 'boolean',  'checkbox',   'true',     xarML('Use wordwrap in source editor'),    'Force wordwrap for the source editor','advform'),
                              'theme_advanced_toolbar_location'     => array(0, 'dropdown', 'str',       'bottom',     xarML('Toolbar location') ,    'Specify where the toolbar should be located either top, bottom or external','simpform'),
                              'theme_advanced_toolbar_align'        => array(0, 'dropdown', 'str',       'center',     xarML('Toolbar alignment') ,    'This value can be left, right or center (the default).','simpform'),
                              'theme_advanced_statusbar_location'   => array(0, 'dropdown',  'str',     'none',       xarML('Status bar location') ,   'Specify where the element statusbar with the path and resize tool should be located - top, bottom or none','simpform'),
                              'theme_advanced_path'                 => array(0, 'boolean',  'checkbox',   'true',     xarML('Enable element path'),   'Enable/disable the element path','simpform'),
                              'theme_advanced_buttons1'             => array(0, 'text', 'str',       'separator,insertdate,inserttime,preview,zoom,separator,forecolor,backcolor',    xarML('Row 1 button/control names (replaces default)') ,  'A comma separated list of button/control names to insert into the toolbar','advform'),
                              'theme_advanced_buttons2'             => array(0, 'text', 'str',       'bullist,numlist,separator,outdent,indent,separator,undo,redo,separator',       xarML('Row 2 button/control names (replaces default)')  ,   'A comma separated list of button/control names to insert into the toolbar','advform'),
                              'theme_advanced_buttons3'             => array(0, 'text', 'str',       'hr,removeformat,visualaid,separator,sub,sup,separator,charmap',    xarML('Row 3 button/control names (replaces default)') ,    'A comma separated list of button/control names to insert into the toolbar','advform'),
                              'theme_advanced_buttons1_add'         => array(0, 'text', 'str',       '',        xarML('Row 1 button/control names (added after)') ,  'A comma separated list of button/control names to add to the end of the specified toolbar row','simpform'),
                              'theme_advanced_buttons2_add'         => array(0, 'text', 'str',       '',       xarML('Row 2 button/control names (added after)') ,   'A comma separated list of button/control names to add to the end of the specified toolbar row','simpform'),
                              'theme_advanced_buttons3_add'         => array(0, 'text', 'str',       '',      xarML('Row 3 button/control names (added after)') ,    'A comma separated list of button/control names to add to the end of the specified toolbar row','simpform'),
                              'theme_advanced_buttons1_add_before'  => array(0, 'text', 'str',       '',      xarML('Row 1 button/control names (added before)') ,   'A comma separated list of button/control names to add to the beginning of the specified toolbar row','advform'),
                              'theme_advanced_buttons2_add_before'  => array(0, 'text', 'str',       '',      xarML('Row 2 button/control names (added before)') ,   'A comma separated list of button/control names to add to the beginning of the specified toolbar row','advform'),
                              'theme_advanced_buttons3_add_before'  => array(0, 'text', 'str',       '',      xarML('Row 3 button/control names (added before)') ,    'A comma separated list of button/control names to add to the beginning of the specified toolbar row','advform'),
                              'theme_advanced_disable'              => array(0, 'text', 'str',       '',      xarML('List of controls to disable') ,    'A comma separated list of controls to disable from any toolbar row/panel in TinyMCE','simpform'),
                        
                              'theme_advanced_containers'           => array(0, 'text', 'str',       '',        xarML('List of container names') ,   'A comma separated list of container names - only available with RowLayout or Custom','advform'),
                              'theme_advanced_containers_default_class'     => array(0, 'text', 'str',       '',       xarML('Conatiner class'),   'The default container/panel class name','advform'),
                              'theme_advanced_containers_default_align'     => array(0, 'dropdown', 'str',   'left',       xarML('Container alignment'),   'Container/panel alignment and can be a value of "left", "center" or "right"','advform'),
                        
                              'theme_advanced_custom_layout'        => array(0, 'text', 'str',       '',       xarML('Custom layout function'),  'Specify a custom layout manager function - only available with Custom Layout','advform'),
                              'theme_advanced_link_targets'         => array(0, 'text', 'str',       '',        xarML('Link drop down list targets'),  'A comma separated list of link target titles and target names separated by =. The titles are the ones that get presented to the user in the link target drop down list','advform'),
                              'theme_advanced_resizing'             => array(0, 'boolean',  'checkbox',   'false',        xarML('Enable resizing'),   'Enable/disable the resizing button','simpform'),
                              'theme_advanced_resizing_min_width'   => array(0, 'text', 'str',       '',         xarML('Editor minimum width'), 'Specify a minimum width for the editor','advform'),
                              'theme_advanced_resizing_min_height'  => array(0, 'text', 'str',       '',         xarML('Editor minimum height'),  'Specify a minimum height for the editor.','advform'),
                              'theme_advanced_resizing_max_width'   => array(0, 'text', 'str',       '',        xarML('Editor maximum width'),   'Specify a maximum width for the editor','simpform'),
                              'theme_advanced_resizing_max_height'  => array(0, 'text', 'str',       '',        xarML('Editor minimum height'),   'Specify a maximum height for the editor in pixels','simpform'),
                              'theme_advanced_resizing_use_cookie'  => array(0, 'boolean',  'checkbox',   'true',    xarML('Store size in cookie?'), 'Storage of editor size in a cookie','advform'),
                              'theme_advanced_resize_horizontal'    => array(0, 'boolean',  'checkbox',   'true',     xarML('Enable horizonal resizing?'),  'Enable/disable the horizontal resizing','simpform'),

                              'theme_advanced_fonts'                => array(0, 'text', 'str',       '',        xarML('List of fonts for font selector'),  'A semicolon separated list of font titles and font families separated by =. The titles are the ones that get presented to the user in the fonts drop down list ','simpform'),
                              'theme_advanced_font_sizes'           => array(0, 'text', 'str',       '',       xarML('List of font sizes'),   'A semicolon separated list of font sizes to include','simpform'),
                              'theme_advanced_text_colors'          => array(0, 'text', 'str',       '',      xarML('Colors for text color selector (override)'),    'The colors shown in the palette of colors displayed by the text color button. The default is a palette of 40 colors. It should contain a comma separated list of color values to be presented with no #','advform'),
                              'theme_advanced_background_colors'    => array(0, 'text', 'str',       '',      xarML('Colors for text background selector (override)'),    'The colors shown in the palette of background colors displayed by the background color button. The default is a palette of 40 colors. It should contain a comma separated list of color values to be presented with no #','advform'),
                              'theme_advanced_default_foreground_color' => array(0, 'text', 'str',       '',  xarML('Default foreground (#hex)'),   'Specify the default foreground color in hex format with #','advform'),
                              'theme_advanced_default_background_color'   => array(0, 'text', 'str',       '', xarML('Default background (#hex)'),    'Specify the default backgroundcolor in hex format with #','advform'),
                              'theme_advanced_more_colors'          => array(0, 'boolean',  'checkbox',   'true',        xarML('Disable more colors link'),   'Disable the "more colors" link for the text and background color menus','advform'),
                              'visual'                              => array(0, 'boolean',  'checkbox',   'true',        xarML('Use visial aid for borderless tables'), 'Turn on/off the visual aid for borderless tables','simpform'),
                              'visual_table_class'                   => array(0, 'text',  'str',      'mceVisualAid',     xarML('CSS class for borderless'), 'Specify what CSS class to use for presenting visual aids for borderless tables','advform'),
                    ),

    'File Lists'  =>   array('external_link_list_url'              => array(0, 'text',  'str',      '',       xarML('External link list (or function)'),   'An external list of links, which can be generated by a server side page and then inserted into the link dialog windows of TinyMCE. The links can be to internal site documents or external URLs','advform'),
                            'external_image_list_url'             => array(0, 'text',  'str',      '',     xarML('External image list (or function)'),  'An external list of images, which can be generated by a server side page and then inserted into the link dialog windows of TinyMCE. The links can be to internal site documents or external URLs','advform'),
                    ),
                    
    'Triggers/Patches'=>  array('add_form_submit_trigger'         => array(0, 'boolean',  'checkbox',   'true',     xarML('Use onsubmit event listener?'),  'Turn on/off the onsubmit event listener','advform'),
                             'add_unload_trigger'              => array(0, 'boolean',  'checkbox',   'true',      xarML('Store page contents on unload'),    'Turn on/off page ability for contents to be stored away if the page is unloaded','advform'),
                             'submit_patch'                    => array(0, 'boolean',  'checkbox',   'true',     xarML('Use auto patching of submit in forms'),    'Turn on/off the auto patching of the submit function in forms','advform'),
                    ),
                    
    'Callbacks'   =>   array('cleanup_callback'                    => array(0, 'text',  'str',      '',       xarML('Custom cleanup'),  'Add custom cleanup logic to TinyMCE in format of customCleanup(type, value)','advform'),
                         'execcommand_callback'                => array(0, 'text',  'str',      '',       xarML('execCommand callback function'),   'Add custom callback function for execCommand handling','advform'),
                         'file_browser_callback'               => array(0, 'text',  'str',      '',      xarML('Image/file browser callback function'),   'Add add your own file browser/image browse function','advform'),
                         'handle_event_callback'               => array(0, 'text',  'str',      '',       xarML('Event interceptor call back'),   'A function name to be executed each time TinyMCE intercepts and handles an event such as keydown, mousedown and so forth','advform'),
                         'handle_node_change_callback'         => array(0, 'text',  'str',      '',       xarML('Cursor selector callback'),  'Gets called once the cursor/selection in a TinyMCE instance changes. This is useful to enable/disable button controls depending on where the users are and what they have selected. This method gets executed a lot and should be as performance tuned as possible','advform'),
                         'init_instance_callback'              => array(0, 'text',  'str',      '',       xarML('Callback on initialize'),   'A function name to be executed each time a editor instance is initialized','advform'),
                         'onchange_callback'                   => array(0, 'text',  'str',      '',      xarML('Modify callback function'),    'A function name to be executed each time content is modified by TinyMCE','advform'),
                         'oninit'                              => array(0, 'text',  'str',      '',      xarML('Callback on initialization completion'),    'A function name to be executed when all editor instances have finished their initialization','advform'),
                         'onpageload'                          => array(0, 'text',  'str',      '',      xarML('Callback on pageload'),    'A function name to be executed when the page is loaded but before the TinyMCE instances are created','advform'),
                         'remove_instance_callback'            => array(0, 'text',  'str',      '',      xarML('Callback on editor removal'),   'A function name to be executed each time an editor instance is removed','advform'),
                         'save_callback'                       => array(0,  'text', 'str',       '',     xarML('Callback on save'),    'Add custom function to be executed when the contents are extracted/saved','advform'),
                         'setup'                               => array(0, 'text',  'str',      '',      xarML('Custom function for event addition'),    'Add custom function to add events to editor instances before they get rendered','advform'),
                         'setupcontent_callback'               => array(0, 'text',  'str',      '',      xarML('Custom function for logic on initialization'),   'Add custom function to execute custom content set up logic when the editor initializes. The format of this callback is: setupContent(editor_id, body, doc)','advform'),
                         'urlconverter_callback'               => array(0, 'text',  'str',      '',     xarML('Custom URL converter'),   'Add your own URL converter logic. This option should contain a JavaScript function name. The format of this converter function is: URLConverter(url, node, on_save)','advform'),
                    ),
    
    'Custom'    =>      array('custom'      => array(1, 'textarea',  'str',      '',         xarML('Freeform custom configurations'), 'Add each option per line as per normal javascript config entry, and ensure a comma is on the end of the line: option: &#8220;value&#8221;,','simpform'),   
                    ),
);

?>