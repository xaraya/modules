;<? die("No direct access!") ?>
; $Id: phpOpenTracker.ini.dist,v 1.5 2003/02/14 06:43:43 bergmann Exp $

; Database Connection

db_type     = "mysql"
db_host     = "localhost"
db_port     = "default"
db_socket   = "default"
db_user     = ""
db_password = ""
db_database = "Xaraya"

; Database Tables

additional_data_table   = "xar_pot_add_data"
accesslog_table         = "xar_pot_accesslog"
documents_table         = "xar_pot_documents"
exit_targets_table      = "xar_pot_exit_targets"
hostnames_table         = "xar_pot_hostnames"
operating_systems_table = "xar_pot_operating_systems"
referers_table          = "xar_pot_referers"
user_agents_table       = "xar_pot_user_agents"
visitors_table          = "xar_pot_visitors"

; Request Information Parsing

document_env_var       = "REQUEST_URI"
clean_referer_string   = Off
clean_query_string     = Off
get_parameter_filter   = ""
resolve_hostname       = On
group_hostnames        = On
group_user_agents      = On

; Returning Visitors Handling

track_returning_visitors           = Off
returning_visitors_cookie          = "pot_visitor_id"
returning_visitors_cookie_lifetime = 365

; Locking

locking       = Off
log_reload    = On

; Miscellaneous Settings

jpgraph_path           = "./jpgraph/"

; Plugins

logging_engine_plugins = "search_engines"

; Query Cache

query_cache          = Off
query_cache_dir      = "/tmp/"
query_cache_lifetime = 3600

; Debugging
debug_level          = 1
exit_on_fatal_errors = Off
log_errors           = Off
