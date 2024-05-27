#ifndef ESPRESSO_HTTP_C
#define ESPRESSO_HTTP_C

#if defined __linux__ || defined __APPLE__
#include "http_linux.c"
#else
#include "http_default.c"
#endif

#endif
