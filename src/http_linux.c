#ifndef ESPRESSO_HTTP_LINUX_C
#define ESPRESSO_HTTP_LINUX_C

#include <arpa/inet.h>
#include <stdlib.h>
#include <string.h>
// #include <sys/ioctl.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <unistd.h>

#include "espresso.h"
// #include "debug.h"
#include "thread.h"
#include "types.h"

// #include "debug.c"
#include "thread.c"

void espresso_http_server_listen(uint16 port)
{
    global int32 errno;
    struct sockaddr_in address;
    socklen_t address_length = sizeof(address);
    int32 result;

    int32 server = socket(AF_INET, SOCK_STREAM, 0);

    if (server == -1)
    {
        espresso_http_server_handle_error(1, errno);

        return;
    }

    int32 opt = 1;

    result = setsockopt(server, SOL_SOCKET, ESPRESSO_HTTP_LINUX_OPT_NAME, &opt, sizeof(opt));

    if (result == -1)
    {
        espresso_http_server_handle_error(2, errno);

        return;
    }

    address.sin_family = AF_INET;
    address.sin_port = htons(port);
    address.sin_addr.s_addr = INADDR_ANY;

    result = bind(server, (struct sockaddr *)&address, sizeof(address));

    if (result == -1)
    {
        espresso_http_server_handle_error(3, errno);

        return;
    }

    result = listen(server, 4);

    if (result == -1)
    {
        espresso_http_server_handle_error(4, errno);

        return;
    }

    while (true)
    {
        int32 *client = (int32 *)malloc(sizeof(int32));
        *client = accept(server, (struct sockaddr *)&address, &address_length);

        if (*client == -1)
        {
            espresso_http_server_handle_error(5, errno);

            continue;
        }

        espresso_thread_start(&espresso_http_server_thread, client, 1);
    }

    close(server);
}

void espresso_http_server_thread(mixed *args, uint32 args_count)
{
    global int32 errno;

    int32 *client = (int32 *)args;
    const uint32 buffer_size = 65536;
    char buffer[buffer_size];

    memset(buffer, 0, buffer_size);
    recv(*client, buffer, buffer_size - 1, 0);

    uint32 response_length;

    if (espresso_http_server_callable)
    {
        struct espresso_http_server_call *call = (struct espresso_http_server_call *)malloc(sizeof(struct espresso_http_server_call));
        espresso_http_server_callable(call);

        if (call->callable)
        {
            struct espresso_http_server_request *request = (struct espresso_http_server_request *)malloc(sizeof(struct espresso_http_server_request));
            request->request = buffer;
            call->callable(request);

            const char *response = request->response;
            response_length = strlen(response);

            send(*client, response, response_length, 0);

            if (request->free)
            {
                request->free();
            }

            free(request);
        }
        else
        {
            const char *response = "";
            response_length = strlen(response);
            send(*client, response, response_length, 0);
        }

        free(call);
    }
    else
    {
        const char *response = "";
        response_length = strlen(response);
        send(*client, response, response_length, 0);
    }

    if (*client == -1)
    {
        espresso_http_server_handle_error(6, errno);
    }

    close(*client);
    free(client);
}

void espresso_http_server_handle_error(byte type, int32 errno)
{
    if (espresso_error_callable)
    {
        espresso_error_callable("Unable to start a new espresso server using the provided port.");
    }
}

#endif
