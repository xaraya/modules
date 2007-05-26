extern zend_module_entry xapian_module_entry;
//#define xapian_module_ptr &xapian_module_entry
#define phpext_xapian_ptr &xapian_module_entry

#define XAPIAN_VERSION_ID "0.9.2"

PHP_MINIT_FUNCTION(xapian);
PHP_RINIT_FUNCTION(xapian);
PHP_MSHUTDOWN_FUNCTION(xapian);
PHP_MINFO_FUNCTION(xapian);

ZEND_FUNCTION(new_database);
ZEND_FUNCTION(database_add_database);
ZEND_FUNCTION(database_get_doccount);
ZEND_FUNCTION(new_writabledatabase);
ZEND_FUNCTION(writabledatabase_replace_document);
ZEND_FUNCTION(writabledatabase_flush);
ZEND_FUNCTION(new_stem);
ZEND_FUNCTION(stem_stem_word);
ZEND_FUNCTION(new_queryparser);
ZEND_FUNCTION(queryparser_add_prefix);
ZEND_FUNCTION(queryparser_set_stemmer);
ZEND_FUNCTION(queryparser_set_stemming_strategy);
ZEND_FUNCTION(queryparser_set_database);
ZEND_FUNCTION(queryparser_set_default_op);
ZEND_FUNCTION(queryparser_parse_query);
ZEND_FUNCTION(new_enquire);
ZEND_FUNCTION(enquire_set_query);
ZEND_FUNCTION(enquire_get_mset);
ZEND_FUNCTION(mset_begin);
ZEND_FUNCTION(mset_end);
ZEND_FUNCTION(mset_size);
ZEND_FUNCTION(mset_get_matches_estimated);
ZEND_FUNCTION(msetiterator_equals);
ZEND_FUNCTION(msetiterator_get_document);
ZEND_FUNCTION(msetiterator_get_percent);
ZEND_FUNCTION(msetiterator_next);
ZEND_FUNCTION(new_document);
ZEND_FUNCTION(document_add_posting);
ZEND_FUNCTION(document_add_value);
ZEND_FUNCTION(document_get_value);
