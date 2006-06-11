  // the menuitems and their URLs
  var menu = new Array();
menu['http://cpaint.booleansystems.com/']                    = 'cpaint website';
menu['http://sf.net/projects/cpaint']                    = 'cpaint @ sourceforge.net';
menu['undef00'] = 'NIL';
  menu['http://cpaint.booleansystems.com/search.php'] = 'Search Documentation';
 menu['undef001'] = 'NIL';

menu['index.html']                    = 'documentation index';
  menu['changelog.html']                = 'changelog';
  menu['todo.html']                     = 'todo list';
  menu['whoiswho.html']                 = 'project members';
  menu['support.html']                  = 'getting support';
  menu['updates.html']                  = 'getting updates and versions';
  menu['license.html']					        = 'license information';
  
  menu['undef0'] = 'NIL';
  menu['#']                                 = '<strong>Introduction</strong>';
  menu['intro.ajax.html']                   = 'what is AJAX?';
  menu['intro.cpaint.html']                 = 'what is CPAINT?';
  menu['intro.features.html']               = 'features';
  menu['intro.responsetypes.html']          = 'supported response types';

  menu['undef1'] = 'NIL';

  menu['usage.html']                      = '<strong>Developer\'s Guide</strong>';
  menu['usage.backend.html']		          = 'backend';
  menu['usage.frontend.html']		          = 'frontend';
  menu['undef2'] = 'NIL';
	
	menu['debugging.html']							= '<strong>Debugging</strong>';
  
 menu['undef2a'] = 'NIL';
	 
  menu['upgrade.html']                = '<strong>Upgrade to v2.x</strong>';
  menu['upgrade.backend.html']        = 'backend changes';
  menu['upgrade.frontend.html']       = 'frontend changes';
  
  menu['undef3'] = 'NIL';
  
  menu['backend.intro.html'] = '<strong>Backend API</strong>';
  menu['backend.class.cpaint.html']               = 'cpaint';
  menu['backend.class.cpaint.register.html']      = '  register()';
  menu['backend.class.cpaint.unregister.html']    = '  unregister()';
  menu['backend.class.cpaint.start.html']         = '  start()';
  menu['backend.class.cpaint.return_data.html']   = '  return_data()';
  menu['backend.class.cpaint.add_node.html']      = '  add_node()';
  menu['backend.class.cpaint.set_data.html']      = '  set_data()';
  menu['backend.class.cpaint.get_data.html']      = '  get_data()';
  menu['backend.class.cpaint.set_id.html']        = '  set_id()';
  menu['backend.class.cpaint.get_id.html']        = '  get_id()';
  menu['backend.class.cpaint.set_attribute.html'] = '  set_attribute()';
  menu['backend.class.cpaint.get_attribute.html'] = '  get_attribute()';
  menu['backend.class.cpaint.set_name.html']      = '  set_name()';
  menu['backend.class.cpaint.get_name.html']      = '  get_name()';
  menu['backend.class.cpaint.get_response_type.html']       = '  get_response_type()';

  menu['backend.class.cpaint_node.html']                = 'cpaint_node';
  menu['backend.class.cpaint_node.add_node.html']       = '  add_node()';
  menu['backend.class.cpaint_node.set_data.html']       = '  set_data()';
  menu['backend.class.cpaint_node.get_data.html']       = '  get_data()';
  menu['backend.class.cpaint_node.set_id.html']         = '  set_id()';
  menu['backend.class.cpaint_node.get_id.html']         = '  get_id()';
  menu['backend.class.cpaint_node.set_attribute.html']  = '  set_attribute()';
  menu['backend.class.cpaint_node.get_attribute.html']  = '  get_attribute()';
  menu['backend.class.cpaint_node.set_name.html']       = '  set_name()';
  menu['backend.class.cpaint_node.get_name.html']       = '  get_name()';
  menu['undef4']  = 'NIL';

  menu['backend.proxy.html']                = '<strong>Proxy Utility</strong>';
	menu['backend.proxy.security.html'] 			= '  security';
  menu['backend.proxy.considerations.html'] = '  considerations';
  menu['backend.proxy.enhanced-usage.html'] = '  enhanced usage';

  menu['undef5']  = 'NIL';

  menu['frontend.intro.html']                                   = '<strong>Frontend API</strong>';
  menu['frontend.class.cpaint.html']                            = 'cpaint';
  menu['frontend.class.cpaint.set_debug.html']                  = '  set_debug()';
  menu['frontend.class.cpaint.set_proxy_url.html']              = '  set_proxy_url()';
  menu['frontend.class.cpaint.set_transfer_mode.html']          = '  set_transfer_mode()';
  menu['frontend.class.cpaint.set_async.html']                  = '  set_async()';
  menu['frontend.class.cpaint.set_response_type.html']          = '  set_response_type()';
  menu['frontend.class.cpaint.set_persistent_connection.html']  = '  set_persistent_connection()';
  menu['frontend.class.cpaint.set_use_cpaint_api.html']         = '  set_use_cpaint_api()';
  menu['frontend.class.cpaint.call.html']                       = '  call()';
  menu['frontend.class.cpaint.capable.html']                    = '  capable';
  
  menu['frontend.class.cpaint_result_object.html']                    = 'cpaint_result_object';
  menu['frontend.class.cpaint_result_object.find_item_by_type.html']  = '  find_item_by_type()';
  menu['frontend.class.cpaint_result_object.set_attribute.html']      = '  set_attribute()';
  menu['frontend.class.cpaint_result_object.get_attribute.html']      = '  get_attribute()';
  menu['frontend.class.cpaint_result_object.data.html']      = '  data';
  
  menu['/search.php'] = 'Search Documentation';
  
  function generate_menu() {

    if (document.location.href.match(/\/([^\/]+)$/)) {
      var site_url = document.location.href.match(/\/([^\/]+)$/)[1];

    } else {
      var site_url = 'index.html';
    }
    
    for (var url in menu) {
      
      if (menu[url] != 'NIL') {
        var linktext    = '';
        var whitespace  = '';
  
        linktext    = menu[url].match(/^[\s]*(.*)/)[1];
        whitespace  = menu[url].match(/^([\s]*)/)[1];
        whitespace  = whitespace.replace(/\s/g, '&nbsp;');
        
        document.write(whitespace);
        document.write('<a href="' + url + '"'); 
        
        if (site_url == url) {
          document.write(' class="menuactive"');

        } else {
          document.write(' class="menu"');
        }
        
        document.write('>' + linktext);
        
        document.writeln('</a><br />');

      } else {
        document.writeln('<br />');
      }
    }
  }