#ifndef ESPRESSO_HTTP_C
#define ESPRESSO_HTTP_C

#include "debug.h"
#include "types.h"

#include "debug.c"

void espresso_http_server_listen(uint16 port)
{
    debug("Espresso server created at port:");
    debug_int(port);
}

#endif
