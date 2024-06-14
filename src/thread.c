#ifndef ESPRESSO_THREAD_C
#define ESPRESSO_THREAD_C

#if defined __linux__ || defined __APPLE__
#include "thread_linux.h"

#include "thread_linux.c"
#else
#include "thread_default.c"
#endif

#endif
