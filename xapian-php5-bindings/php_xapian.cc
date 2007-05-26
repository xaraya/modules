#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#ifdef __cplusplus
extern "C" {
#endif 
#include "php.h"
#include "ext/standard/info.h"
#include "php_xapian.h"
#ifdef __cplusplus
}
#endif


#include "xapian.h"
#include <string>
#include <vector>
#include <list>

using namespace std;

/*
    Xapian Functions
*/
static function_entry xapian_functions[] = {
	ZEND_FE(new_database, NULL)
	ZEND_FE(database_add_database, NULL)
	ZEND_FE(database_get_doccount, NULL)
	ZEND_FE(new_writabledatabase, NULL)
	ZEND_FE(writabledatabase_replace_document, NULL)
	ZEND_FE(writabledatabase_flush, NULL)
	ZEND_FE(new_stem, NULL)
	ZEND_FE(stem_stem_word, NULL)
	ZEND_FE(new_queryparser, NULL)
	ZEND_FE(queryparser_add_prefix, NULL)
	ZEND_FE(queryparser_set_stemmer, NULL)
	ZEND_FE(queryparser_set_stemming_strategy, NULL)
	ZEND_FE(queryparser_set_database, NULL)
	ZEND_FE(queryparser_set_default_op, NULL)
	ZEND_FE(queryparser_parse_query, NULL)
	ZEND_FE(new_enquire, NULL)
	ZEND_FE(enquire_set_query, NULL)
	ZEND_FE(enquire_get_mset, NULL)
	ZEND_FE(mset_begin, NULL)
	ZEND_FE(mset_end, NULL)
	ZEND_FE(mset_size, NULL)
	ZEND_FE(mset_get_matches_estimated, NULL)
	ZEND_FE(msetiterator_equals, NULL)
	ZEND_FE(msetiterator_get_document, NULL)
	ZEND_FE(msetiterator_get_percent, NULL)
	ZEND_FE(msetiterator_next, NULL)
	ZEND_FE(new_document, NULL)
	ZEND_FE(document_add_posting, NULL)
	ZEND_FE(document_add_value, NULL)
	ZEND_FE(document_get_value, NULL)
	
	{NULL, NULL, NULL}
};

static int le_Xapian_ESet=0; /* handle for ESet */
static int le_Xapian_Enquire=0; /* handle for Enquire */
static int le_Xapian_BoolWeight=0; /* handle for BoolWeight */
static int le_Xapian_TradWeight=0; /* handle for TradWeight */
static int le_Xapian_QueryParser=0; /* handle for QueryParser */
static int le_Xapian_MSet=0; /* handle for MSet */
static int le_Xapian_Stopper=0; /* handle for Stopper */
static int le_time_t=0; /* handle for Stopper */
static int le_Xapian_SimpleStopper=0; /* handle for SimpleStopper */
static int le_Xapian_MatchDecider=0; /* handle for SimpleStopper */
static int le_Xapian_Query=0; /* handle for Query */
static int le_Xapian_Database=0; /* handle for Database */
static int le_Xapian_WritableDatabase=0; /* handle for WritableDatabase */
static int le_Xapian_Document=0; /* handle for Document */
static int le_Xapian_Stem=0; /* handle for Stem */
static int le_int=0; /* handle for Stem */
static int le_Xapian_RSet=0; /* handle for RSet */
static int le_Xapian_Weight=0; /* handle for Weight */
static int le_Xapian_ESetIterator=0; /* handle for ESetIterator */
static int le_Xapian_MSetIterator=0; /* handle for MSetIterator */
static int le_Xapian_ValueIterator=0; /* handle for ValueIterator */
static int le_Xapian_TermIterator=0; /* handle for TermIterator */
static int le_Xapian_PostingIterator=0; /* handle for PostingIterator */
static int le_Xapian_PositionIterator=0; /* handle for PositionIterator */
static int le_Xapian_BM25Weight=0; /* handle for BM25Weight */

#define le_Xapian_Database_name "Xapian Database Resource"
#define le_Xapian_WritableDatabase_name "Xapian Writable Database Resource"
#define le_Xapian_Stem_name "Xapian Stem Resource"
#define le_Xapian_Query_name "Xapian Query Resource"
#define le_Xapian_QueryParser_name "Xapian Query Parser Resource"
#define le_Xapian_Enquire_name "Xapian Enquire Resource"
#define le_Xapian_MSet_name "Xapian MSet Resource"
#define le_Xapian_MSetIterator_name "Xapian MSet Iterator Resource"
#define le_Xapian_Document_name "Xapian Document Resource"


/*
    Xapian Info
*/
zend_module_entry xapian_module_entry = {
	STANDARD_MODULE_HEADER,
	"xapian", 
	xapian_functions, 
	PHP_MINIT(xapian), 
	PHP_MSHUTDOWN(xapian), 
	PHP_RINIT(xapian), 
	NULL,
	PHP_MINFO(xapian), 
    NO_VERSION_YET,
	STANDARD_MODULE_PROPERTIES
};

#ifdef COMPILE_DL_XAPIAN
ZEND_GET_MODULE(xapian)
#endif


/*
    Define the destructors for the xapian resources
*/
void delete_Database(zend_rsrc_list_entry *rsrc TSRMLS_DC) 
{
    Xapian::Database *my_rsrc = (Xapian::Database *) rsrc->ptr;
    delete my_rsrc;
}
void delete_WritableDatabase(zend_rsrc_list_entry *rsrc TSRMLS_DC) 
{
    Xapian::WritableDatabase *my_rsrc = (Xapian::WritableDatabase *) rsrc->ptr;
    delete my_rsrc;
}
void delete_Stem(zend_rsrc_list_entry *rsrc TSRMLS_DC) 
{
    Xapian::Stem *my_rsrc = (Xapian::Stem *) rsrc->ptr;
    delete my_rsrc;
}
void delete_Query(zend_rsrc_list_entry *rsrc TSRMLS_DC) 
{
    Xapian::Query *my_rsrc = (Xapian::Query *) rsrc->ptr;
    delete my_rsrc;
}
void delete_QueryParser(zend_rsrc_list_entry *rsrc TSRMLS_DC) 
{
    Xapian::QueryParser *my_rsrc = (Xapian::QueryParser *) rsrc->ptr;
    delete my_rsrc;
}
void delete_Enquire(zend_rsrc_list_entry *rsrc TSRMLS_DC) 
{
    Xapian::Enquire *my_rsrc = (Xapian::Enquire *) rsrc->ptr;
    delete my_rsrc;
}
void delete_MSet(zend_rsrc_list_entry *rsrc TSRMLS_DC) 
{
    Xapian::MSet *my_rsrc = (Xapian::MSet *) rsrc->ptr;
    delete my_rsrc;
}
void delete_MSetIterator(zend_rsrc_list_entry *rsrc TSRMLS_DC) 
{
    Xapian::MSetIterator *my_rsrc = (Xapian::MSetIterator *) rsrc->ptr;
    delete my_rsrc;
}
void delete_Document(zend_rsrc_list_entry *rsrc TSRMLS_DC) 
{
    Xapian::Document *my_rsrc = (Xapian::Document *) rsrc->ptr;
    delete my_rsrc;
}

/*
    Basic Module Info Functions
*/
PHP_MINIT_FUNCTION(xapian)
{

    le_Xapian_Database = zend_register_list_destructors_ex(
        delete_Database, 
        NULL, 
        le_Xapian_Database_name, 
        module_number
    );
    
    le_Xapian_WritableDatabase = zend_register_list_destructors_ex(
        delete_WritableDatabase, 
        NULL, 
        le_Xapian_WritableDatabase_name, 
        module_number
    );

    le_Xapian_Stem = zend_register_list_destructors_ex(
        delete_Stem, 
        NULL, 
        le_Xapian_Stem_name, 
        module_number
    );
    le_Xapian_Query = zend_register_list_destructors_ex(
        delete_Query, 
        NULL, 
        le_Xapian_Query_name, 
        module_number
    );
    le_Xapian_QueryParser = zend_register_list_destructors_ex(
        delete_QueryParser, 
        NULL, 
        le_Xapian_QueryParser_name, 
        module_number
    );
    le_Xapian_Enquire = zend_register_list_destructors_ex(
        delete_Enquire, 
        NULL, 
        le_Xapian_Enquire_name, 
        module_number
    );
    le_Xapian_MSet = zend_register_list_destructors_ex(
        delete_MSet, 
        NULL, 
        le_Xapian_MSet_name, 
        module_number
    );
    le_Xapian_MSetIterator = zend_register_list_destructors_ex(
        delete_MSetIterator, 
        NULL, 
        le_Xapian_MSetIterator_name, 
        module_number
    );
    le_Xapian_Document = zend_register_list_destructors_ex(
        delete_Document, 
        NULL, 
        le_Xapian_Document_name, 
        module_number
    );

    /* cinit subsection */
    REGISTER_LONG_CONSTANT( "ASCENDING", Xapian::Enquire::ASCENDING, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "DESCENDING", Xapian::Enquire::DESCENDING, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "DONT_CARE", Xapian::Enquire::DONT_CARE, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "DB_CREATE_OR_OPEN", 1, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "DB_CREATE", 2, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "DB_CREATE_OR_OVERWRITE", 3, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "DB_OPEN", 4, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "OP_AND", Xapian::Query::OP_AND, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "OP_OR", Xapian::Query::OP_OR, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "OP_AND_NOT", Xapian::Query::OP_AND_NOT, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "OP_XOR", Xapian::Query::OP_XOR, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "OP_AND_MAYBE", Xapian::Query::OP_AND_MAYBE, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "OP_FILTER", Xapian::Query::OP_FILTER, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "OP_NEAR", Xapian::Query::OP_NEAR, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "OP_PHRASE", Xapian::Query::OP_PHRASE, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "OP_ELITE_SET", Xapian::Query::OP_ELITE_SET, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "FLAG_BOOLEAN", Xapian::QueryParser::FLAG_BOOLEAN, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "FLAG_PHRASE", Xapian::QueryParser::FLAG_PHRASE, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "FLAG_LOVEHATE", Xapian::QueryParser::FLAG_LOVEHATE, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "FLAG_BOOLEAN_ANY_CASE", Xapian::QueryParser::FLAG_BOOLEAN_ANY_CASE, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "FLAG_WILDCARD", Xapian::QueryParser::FLAG_WILDCARD, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "STEM_NONE", Xapian::QueryParser::STEM_NONE, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "STEM_SOME", Xapian::QueryParser::STEM_SOME, CONST_CS | CONST_PERSISTENT);
    REGISTER_LONG_CONSTANT( "STEM_ALL", Xapian::QueryParser::STEM_ALL, CONST_CS | CONST_PERSISTENT);
    /* end cinit subsection */
    return SUCCESS;
}

PHP_MSHUTDOWN_FUNCTION(xapian)
{
	return SUCCESS;
}


PHP_RINIT_FUNCTION(xapian)
{
        
	return SUCCESS;
}


PHP_MINFO_FUNCTION(xapian)
{
	char buf[32];
	
	php_info_print_table_start();
	php_info_print_table_row(2, "Xapian Support", "enabled" );
	
	sprintf(buf,"%d", XAPIAN_VERSION_ID);
	php_info_print_table_row(2, "Xapian library version", buf );
	php_info_print_table_end();
}

/*
    Xapian Declared Functions
*/

/**

    DATABASE FUNCTIONS

*
/*
    Opens a read only connection to a database
*/
ZEND_FUNCTION(new_database)
{
    char *s;
    int s_len;
    std::string arg1;
    Xapian::Database *result;

    if( ZEND_NUM_ARGS() != 1 ){ WRONG_PARAM_COUNT; }

    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &s, &s_len) == FAILURE ){ return; }

    // Convert a char pointer to a string
    arg1 = std::string(s);
    
    try 
    {
        result = (Xapian::Database *)new Xapian::Database(arg1);
    }
    catch ( const Xapian::Error &e ) 
    {
        zend_error(E_ERROR, e.get_msg().c_str());            
    }
    
    ZEND_REGISTER_RESOURCE(return_value, result, le_Xapian_Database);
}
/*
*/
ZEND_FUNCTION(database_add_database)
{
    zval *r1;
    zval *r2;
    Xapian::Database *arg1 = (Xapian::Database *)0;
    Xapian::Database *arg2 = 0;

    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rr", &r1, &r2) == FAILURE ){ return; }
    
    ZEND_FETCH_RESOURCE(arg1, Xapian::Database*, &r1, -1, le_Xapian_Database_name, le_Xapian_Database)
    ZEND_FETCH_RESOURCE(arg2, Xapian::Database*, &r2, -1, le_Xapian_Database_name, le_Xapian_Database)
        
    try 
    {
        arg1->add_database(*arg2);
    }
    catch ( const Xapian::Error &e ) 
    {
        zend_error(E_ERROR, e.get_msg().c_str());            
    }
}

ZEND_FUNCTION(database_get_doccount)
{
    zval *r1;
    Xapian::Database *arg1 = (Xapian::Database *)0;
    Xapian::doccount result;

    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "r", &r1) == FAILURE ){ return; }
    
    ZEND_FETCH_RESOURCE(arg1, Xapian::Database*, &r1, -1, le_Xapian_Database_name, le_Xapian_Database)
        
    try 
    {
        result = (Xapian::doccount)arg1->get_doccount();    
    }
    catch ( const Xapian::Error &e ) 
    {
        zend_error(E_ERROR, e.get_msg().c_str());            
    }
    
    ZVAL_LONG(return_value, result);
}


/*

    WRITABLE DATABSSE FUNCTIONS

*/
ZEND_FUNCTION(new_writabledatabase)
{
    char *s;
    int s_len;
    std::string arg1;
    int arg2 = 1;
    Xapian::WritableDatabase *result;
    
    if( ZEND_NUM_ARGS() != 2 ){ WRONG_PARAM_COUNT; }

    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "sl", &s, &s_len, &arg2) == FAILURE ){ return; }
    
    arg1 = std::string(s);
    
    try {
        result = (Xapian::WritableDatabase *)new Xapian::WritableDatabase(arg1,arg2);
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }

    ZEND_REGISTER_RESOURCE(return_value, result, le_Xapian_WritableDatabase);   
}

ZEND_FUNCTION(writabledatabase_replace_document)
{
	zval *z_database;
	zval *z_document;
	Xapian::WritableDatabase *database = 0;
	Xapian::docid docid;
	const Xapian::Document *document;
	
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rlr", &z_database, &docid, &z_document) == FAILURE ){ return; }

    ZEND_FETCH_RESOURCE(database, Xapian::WritableDatabase*, &z_database, -1, le_Xapian_WritableDatabase_name, le_Xapian_WritableDatabase)
    ZEND_FETCH_RESOURCE(document, Xapian::Document*, &z_document, -1, le_Xapian_Document_name, le_Xapian_Document)
    
    try {
        database->replace_document(docid, *document);
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }
}

ZEND_FUNCTION(writabledatabase_flush)
{
	zval *z_database;
	Xapian::WritableDatabase *database = 0;

    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "r", &z_database) == FAILURE ){ return; }

    ZEND_FETCH_RESOURCE(database, Xapian::WritableDatabase*, &z_database, -1, le_Xapian_WritableDatabase_name, le_Xapian_WritableDatabase)
    
    try {
        database->flush();
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }
}


/*

    STEM FUNCTIONS

*/
ZEND_FUNCTION(new_stem)
{
    char *s;
    int s_len;
    std::string arg1;
    Xapian::Stem *result;
    
    if( ZEND_NUM_ARGS() != 1 ){ WRONG_PARAM_COUNT; }

    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &s, &s_len) == FAILURE ){ return; }
    
    arg1 = std::string(s);
    
    try {
        result = (Xapian::Stem *)new Xapian::Stem(arg1);
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }

    ZEND_REGISTER_RESOURCE(return_value, result, le_Xapian_Stem);   
}

ZEND_FUNCTION(stem_stem_word)
{
	zval *stemmer;
    Xapian::Stem *arg1;
    char *term;
    int term_len;
    std::string arg2;
    std::string result;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rs", &stemmer, &term, &term_len) == FAILURE ){ return; }
    ZEND_FETCH_RESOURCE(arg1, Xapian::Stem*, &stemmer, -1, le_Xapian_Stem_name, le_Xapian_Stem)
    
    arg2 = std::string(term);
    
    try {
        result = arg1->stem_word(arg2);
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }
	
	RETURN_STRING((char *)result.c_str(), 1);
}



/*

    QUERY PARSER FUNCTIONS

*/
ZEND_FUNCTION(new_queryparser)
{
    Xapian::QueryParser *result;
    
    if( ZEND_NUM_ARGS() != 0 ){ WRONG_PARAM_COUNT; }

    try {
        result = (Xapian::QueryParser *)new Xapian::QueryParser();
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }

    ZEND_REGISTER_RESOURCE(return_value, result, le_Xapian_QueryParser);   
}

ZEND_FUNCTION(queryparser_add_prefix)
{
    zval *queryparser;
    Xapian::QueryParser *arg1 = 0;
    char *term;
    int term_len;
    char *prefix;
    int prefix_len;
    std::string arg2;
    std::string arg3;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rss", &queryparser, &term, &term_len, &prefix, &prefix_len) == FAILURE ){ return; }
    
    ZEND_FETCH_RESOURCE(arg1, Xapian::QueryParser*, &queryparser, -1, le_Xapian_QueryParser_name, le_Xapian_QueryParser)

    arg2 = std::string(term);
    arg3 = std::string(prefix);
    
    try {
        arg1->add_prefix(arg2, arg3);
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }
}

ZEND_FUNCTION(queryparser_set_stemmer)
{
    zval *queryparser;
    zval *stemmer;
    Xapian::QueryParser *arg1 = 0;
    Xapian::Stem *arg2 = 0;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rr", &queryparser, &stemmer) == FAILURE ){ return; }
    
    ZEND_FETCH_RESOURCE(arg1, Xapian::QueryParser*, &queryparser, -1, le_Xapian_QueryParser_name, le_Xapian_QueryParser)
    ZEND_FETCH_RESOURCE(arg2, Xapian::Stem*, &stemmer, -1, le_Xapian_Stem_name, le_Xapian_Stem)

    try {
        arg1->set_stemmer(*arg2);
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }
}


ZEND_FUNCTION(queryparser_set_stemming_strategy)
{
    zval *queryparser;
    Xapian::QueryParser *arg1;
    int arg2;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rl", &queryparser, &arg2) == FAILURE ){ return; }
    
    ZEND_FETCH_RESOURCE(arg1, Xapian::QueryParser*, &queryparser, -1, le_Xapian_QueryParser_name, le_Xapian_QueryParser)

    try {
        arg1->set_stemming_strategy((Xapian::QueryParser::stem_strategy)arg2);
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }
}


ZEND_FUNCTION(queryparser_set_database)
{
    zval *queryparser;
    zval *database;
    Xapian::QueryParser *arg1;
    Xapian::Database *arg2;
    
    if( ZEND_NUM_ARGS() != 2 ){ WRONG_PARAM_COUNT; }

    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rr", &queryparser, &database) == FAILURE ){ return; }
    
    ZEND_FETCH_RESOURCE(arg1, Xapian::QueryParser*, &queryparser, -1, le_Xapian_QueryParser_name, le_Xapian_QueryParser)
    ZEND_FETCH_RESOURCE(arg2, Xapian::Database*, &database, -1, le_Xapian_Database_name, le_Xapian_Database)

    try {
        arg1->set_database(*arg2);
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }
}


ZEND_FUNCTION(queryparser_set_default_op)
{
    zval *queryparser;
    Xapian::QueryParser *arg1;
    int arg2;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rl", &queryparser, &arg2) == FAILURE ){ return; }
    
    ZEND_FETCH_RESOURCE(arg1, Xapian::QueryParser*, &queryparser, -1, le_Xapian_QueryParser_name, le_Xapian_QueryParser)

    try {
        arg1->set_default_op((Xapian::Query::op)arg2);
    } catch (const Xapian::Error &e) {
        zend_error(E_ERROR, e.get_msg().c_str());             
    }
}


ZEND_FUNCTION(queryparser_parse_query)
{
    zval *queryparser;
    Xapian::QueryParser *arg1;
    char *query;
    int query_len;
    std::string arg2;
    Xapian::Query result;
    int flag;
     
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rs|l", &queryparser, &query, &query_len, &flag) == FAILURE ){ return; }
    
    ZEND_FETCH_RESOURCE(arg1, Xapian::QueryParser*, &queryparser, -1, le_Xapian_QueryParser_name, le_Xapian_QueryParser)

    arg2 = std::string(query);
    
    try {
        result = arg1->parse_query((std::string const &)arg2, flag);
    } catch (const Xapian::Error &e) {
        zend_error(E_NOTICE, e.get_msg().c_str());             
//        zend_printf("%s", e.get_msg().c_str());             
    }
    
    Xapian::Query * resultobj = new Xapian::Query((Xapian::Query &) result);    
    ZEND_REGISTER_RESOURCE(return_value, resultobj, le_Xapian_Query);   
}

/*

    STEM FUNCTIONS

*/
ZEND_FUNCTION(new_enquire)
{
    zval *database;    
    Xapian::Database *arg1;
    Xapian::Enquire *result;
    
    if( ZEND_NUM_ARGS() != 1 ){ WRONG_PARAM_COUNT; }

    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "r", &database) == FAILURE ){ return; }
    ZEND_FETCH_RESOURCE(arg1, Xapian::Database*, &database, -1, le_Xapian_Database_name, le_Xapian_Database)
    
    try {
        result = (Xapian::Enquire *)new Xapian::Enquire(*arg1);
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }

    ZEND_REGISTER_RESOURCE(return_value, result, le_Xapian_Enquire);   
}

ZEND_FUNCTION(enquire_set_query)
{
    zval *enquire;
    zval *query;    
    Xapian::Enquire *arg1;
    Xapian::Query *arg2;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rr", &enquire, &query) == FAILURE ){ return; }
    ZEND_FETCH_RESOURCE(arg1, Xapian::Enquire*, &enquire, -1, le_Xapian_Enquire_name, le_Xapian_Enquire)
    ZEND_FETCH_RESOURCE(arg2, Xapian::Query*, &query, -1, le_Xapian_Query_name, le_Xapian_Query)

    try {
        arg1->set_query(*arg2);
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }
}


ZEND_FUNCTION(enquire_get_mset)
{
    zval *enquire;
    Xapian::Enquire *arg1;
    Xapian::doccount arg2;
    Xapian::doccount arg3;
    Xapian::MSet result;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rll", &enquire, &arg2, &arg3) == FAILURE ){ return; }
    ZEND_FETCH_RESOURCE(arg1, Xapian::Enquire*, &enquire, -1, le_Xapian_Enquire_name, le_Xapian_Enquire)
 
    try {
        result = arg1->get_mset(arg2,arg3);
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }

    Xapian::MSet * resultobj = new Xapian::MSet((Xapian::MSet &) result);
    
    ZEND_REGISTER_RESOURCE(return_value, resultobj, le_Xapian_MSet);   
}

/*

    MSET FUNCTIONS

*/
ZEND_FUNCTION(mset_begin)
{
    zval *mset;
    Xapian::MSet *arg1;
    Xapian::MSetIterator result;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "r", &mset) == FAILURE ){ return; }
    ZEND_FETCH_RESOURCE(arg1, Xapian::MSet*, &mset, -1, le_Xapian_MSet_name, le_Xapian_MSet)
 
    try {
        result = arg1->begin();
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }

    Xapian::MSetIterator * resultobj = new Xapian::MSetIterator(result);
    
    ZEND_REGISTER_RESOURCE(return_value, resultobj, le_Xapian_MSetIterator);   
}

ZEND_FUNCTION(mset_end)
{
    zval *mset;
    Xapian::MSet *arg1;
    Xapian::MSetIterator result;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "r", &mset) == FAILURE ){ return; }
    ZEND_FETCH_RESOURCE(arg1, Xapian::MSet*, &mset, -1, le_Xapian_MSet_name, le_Xapian_MSet)
 
    try {
        result = arg1->end();
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }

    Xapian::MSetIterator * resultobj = new Xapian::MSetIterator(result);
    
    ZEND_REGISTER_RESOURCE(return_value, resultobj, le_Xapian_MSetIterator);   
}

ZEND_FUNCTION(mset_size)
{
    zval *mset;
    Xapian::MSet *arg1;
    Xapian::doccount result;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "r", &mset) == FAILURE ){ return; }
    ZEND_FETCH_RESOURCE(arg1, Xapian::MSet*, &mset, -1, le_Xapian_MSet_name, le_Xapian_MSet)
 
    try {
        result = arg1->size();
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }
    
    ZVAL_LONG(return_value, result);
}

ZEND_FUNCTION(mset_get_matches_estimated)
{
    zval *mset;
    Xapian::MSet *arg1;
    Xapian::doccount result;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "r", &mset) == FAILURE ){ return; }
    ZEND_FETCH_RESOURCE(arg1, Xapian::MSet*, &mset, -1, le_Xapian_MSet_name, le_Xapian_MSet)
 
    try {
        result = arg1->get_matches_estimated();
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }

    ZVAL_LONG(return_value, result);
}

ZEND_FUNCTION(msetiterator_next)
{
    zval *msetiterator;
    Xapian::MSetIterator *arg1;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "r", &msetiterator) == FAILURE ){ return; }
    ZEND_FETCH_RESOURCE(arg1, Xapian::MSetIterator*, &msetiterator, -1, le_Xapian_MSetIterator_name, le_Xapian_MSetIterator)
 
    try {
        ++(*arg1);
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }

}

ZEND_FUNCTION(msetiterator_equals)
{
    zval *msetiterator1;
    zval *msetiterator2;
    Xapian::MSetIterator *arg1;
    Xapian::MSetIterator *arg2;
    bool result;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rr", &msetiterator1, &msetiterator2) == FAILURE ){ return; }
    ZEND_FETCH_RESOURCE(arg1, Xapian::MSetIterator*, &msetiterator1, -1, le_Xapian_MSetIterator_name, le_Xapian_MSetIterator)
    ZEND_FETCH_RESOURCE(arg2, Xapian::MSetIterator*, &msetiterator2, -1, le_Xapian_MSetIterator_name, le_Xapian_MSetIterator)
 
    try {
        result = (bool)(*arg1 == *arg2);
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }

    ZVAL_BOOL(return_value,(result)?1:0);
}

ZEND_FUNCTION(msetiterator_get_document)
{
    zval *msetiterator1;
    Xapian::MSetIterator *arg1;
    Xapian::Document result;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "r", &msetiterator1) == FAILURE ){ return; }
    ZEND_FETCH_RESOURCE(arg1, Xapian::MSetIterator*, &msetiterator1, -1, le_Xapian_MSetIterator_name, le_Xapian_MSetIterator)
 
    try {
        result = arg1->get_document();
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }
    
    Xapian::Document * resultobj = new Xapian::Document(result);
    ZEND_REGISTER_RESOURCE(return_value, resultobj, le_Xapian_Document);    
}

ZEND_FUNCTION(msetiterator_get_percent)
{
    zval *msetiterator1;
    Xapian::MSetIterator *arg1;
    Xapian::percent result;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "r", &msetiterator1) == FAILURE ){ return; }
    ZEND_FETCH_RESOURCE(arg1, Xapian::MSetIterator*, &msetiterator1, -1, le_Xapian_MSetIterator_name, le_Xapian_MSetIterator)
 
    try {
        result = arg1->get_percent();
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }
    
    ZVAL_LONG(return_value, result);
}


/*

    DOCUMENT FUNCTIONS

*/
ZEND_FUNCTION(new_document)
{
    Xapian::Document *result;
    
    try {
        result = (Xapian::Document *)new Xapian::Document();
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }

    ZEND_REGISTER_RESOURCE(return_value, result, le_Xapian_Document);   
}

ZEND_FUNCTION(document_add_posting)
{
    zval *document;
    Xapian::Document *arg1;
	char *term;
	int term_len;
	std::string arg2;
    Xapian::termpos arg3;
    Xapian::termcount arg4;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rsll", 
		&document, &term, &term_len, &arg3, &arg4) == FAILURE ){ return; }
    ZEND_FETCH_RESOURCE(arg1, Xapian::Document*, &document, -1, le_Xapian_Document_name, le_Xapian_Document)
 
    arg2 = std::string(term);
		
    try {
        arg1->add_posting(arg2, arg3, arg4);
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }

    //ZVAL_STRINGL(return_value, (char*)result.c_str(), result.length(), 1);
	RETURN_TRUE;
}

ZEND_FUNCTION(document_add_value)
{
    zval *document;
    Xapian::Document *arg1;
	Xapian::valueno valueno;
	char *value;
	int value_len;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rls", 
		&document, &valueno, &value, &value_len) == FAILURE ){ return; }
    ZEND_FETCH_RESOURCE(arg1, Xapian::Document*, &document, -1, le_Xapian_Document_name, le_Xapian_Document)
 
    try {
        arg1->add_value(valueno, std::string(value));
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }
}

ZEND_FUNCTION(document_get_value)
{
    zval *document;
    Xapian::Document *arg1;
    Xapian::valueno arg2;
    std::string result;
    
    if( zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "rl", &document, &arg2) == FAILURE ){ return; }
    ZEND_FETCH_RESOURCE(arg1, Xapian::Document*, &document, -1, le_Xapian_Document_name, le_Xapian_Document)
 
    try {
        result = arg1->get_value(arg2);
    } catch (const Xapian::Error &e) {
        zend_printf("%s", e.get_msg().c_str());             
    }

    ZVAL_STRINGL(return_value, (char*)result.c_str(), result.length(), 1);
}
