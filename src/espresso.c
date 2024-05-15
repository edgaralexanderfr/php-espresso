#ifndef ESPRESSO_C
#define ESPRESSO_C

#include "espresso.h"
#include "types.h"
#include "http.h"

#include "http.c"

extern void Listen(uint16_t port)
{
    listen(port);
}

extern void listen(uint16_t port)
{
    espresso_http_server_listen(port);
}

#endif
