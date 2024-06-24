#ifndef ESPRESSO_HTTP_LINUX_H
#define ESPRESSO_HTTP_LINUX_H

#include "types.h"

#if defined __APPLE__
#define ESPRESSO_HTTP_LINUX_OPT_NAME SO_REUSEADDR
#else
#define ESPRESSO_HTTP_LINUX_OPT_NAME SO_REUSEADDR | SO_REUSEPORT
#endif

void espresso_http_server_thread(mixed *, uint32);

void espresso_http_server_handle_error(byte type, int32 errno);

#endif
