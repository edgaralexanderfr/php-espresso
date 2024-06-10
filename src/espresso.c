#ifndef ESPRESSO_C
#define ESPRESSO_C

#include "espresso.h"
#include "http.h"

#include "http.c"

extern void setEspressoErrorCallable(void (*callable)(const char *))
{
    espresso_error_callable = callable;
}

extern void setEspressoHttpServerCallable(void (*callable)(struct espresso_http_server_call *))
{
    espresso_http_server_callable = callable;
}

extern void listenEspressoServer(unsigned short port)
{
    espresso_http_server_listen(port);
}

#endif
