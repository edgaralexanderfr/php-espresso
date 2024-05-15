#ifndef ESPRESSO_HTTP_C
#define ESPRESSO_HTTP_C

#include "debug.h"
#include "types.h"

#include "debug.c"

void espresso_server_listen(uint16_t port)
{
    debug("Espresso server created at port:");
    debug_int(port);
}

#endif
