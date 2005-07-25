<?php

/**
 *  Returns the attachments settings for the specified modid/itemtype pair or the 
 *  hardwired defaults if there are no current settings for the specified pair
 *  <pre>
 *  Settings returned are:
 *      integer mimetype        the allowed main mime type ( 0 == all)
 *      integer mimesubtype     the allowed sub mime type  ( 0 == all)
 *      string  filenamewith    Only files with this string in them will be allowed
 *      integer maxattachments  Max allowed attachments per item for this modid/itemtype pairing ( -1 = unlimited)
 *      string  displayas       Either List, Icons, or Raw
 *  </pre>
 *
 *  @author  Carl P. Corliss
 *  @access  public
 *  @param   integer modid      Id of the module
 *  @param   integer itemtype   Id of the ItemType 
 *  @return  mixed  Array representing the attachment settings for the current modid/itemtype pairing
 */

function uploads_adminapi_get_attachment_settings( $args )
{
    $modid    = 0;
    $itemtype = 0;
    
    extract($args);
    
    $settings = @unserialize(xarModGetVar('uploads', "settings.attachment.$modid.$itemtype"));
    
    if (!isset($settings) || !is_array($settings) || empty($settings) ) {
        $settings['mimetype']       = 0;
        $settings['mimesubtype']    = 0;
        $settings['filenamewith']   = '';
        $settings['maxattachments'] = (int) -1;
        $settings['displayas']      = 'list';
    }
        
    return $settings;
}

?>