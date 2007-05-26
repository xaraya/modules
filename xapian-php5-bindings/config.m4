
PHP_ARG_WITH(xapian, for Xapian support,
[  --with-xapian[=DIR]        Include Xapian support])

if test "$PHP_XAPIAN" != "no"; then
  if test -r $PHP_XAPIAN/include/xapian.h; then
    XAPIAN_DIR=$PHP_XAPIAN
  else
    AC_MSG_CHECKING(for Xapian in default path)
    for i in /usr/local /usr; do
      if test -r $i/include/xapian.h; then
        XAPIAN_DIR=$i
        AC_MSG_RESULT(found in $i)
        break
      fi
    done
  fi

  if test -z "$XAPIAN_DIR"; then
    AC_MSG_RESULT(not found)
    AC_MSG_ERROR(Please reinstall the Xapian distribution)
  fi

  PHP_NEW_EXTENSION(xapian, php_xapian.cc, $ext_shared)
  PHP_SUBST(XAPIAN_SHARED_LIBADD)
fi
