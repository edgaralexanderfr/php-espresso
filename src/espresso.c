#ifndef ESPRESSO_C
#define ESPRESSO_C

#include "debug.h"
#include "espresso.h"
#include "types.h"

#include "debug.c"

extern void Listen(uint16_t port)
{
    debug("Listening...");
}

extern void listen(uint16_t port)
{
    debug("listening...");
}

#endif
