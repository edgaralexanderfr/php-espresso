#ifndef ESPRESSO_DEBUG_C
#define ESPRESSO_DEBUG_C

#ifdef ESPRESSO_DEBUG
#include <stdio.h>
#endif

#include "types.h"

void debug(const char *output)
{
#ifdef ESPRESSO_DEBUG
    printf("%s\n", output);
#endif
}

void debug_int(int output)
{
#ifdef ESPRESSO_DEBUG
    printf("%i\n", output);
#endif
}

#endif
