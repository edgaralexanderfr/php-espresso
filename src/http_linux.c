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
#include "types.h"

// #include "debug.c"

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

#ifdef __APPLE__
    result = setsockopt(server, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt));
#else
    result = setsockopt(server, SOL_SOCKET, SO_REUSEADDR | SO_REUSEPORT, &opt, sizeof(opt));
#endif

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

    const uint32 buffer_size = 65536;
    int32 client;
    char buffer[buffer_size];

    while (true)
    {
        client = accept(server, (struct sockaddr *)&address, &address_length);

        if (client == -1)
        {
            espresso_http_server_handle_error(5, errno);

            continue;
        }

        memset(buffer, 0, buffer_size);
        result = recv(client, buffer, buffer_size - 1, 0);

        uint32 response_length;

        if (espresso_http_server_callable)
        {
            struct espresso_http_server_request *request = (struct espresso_http_server_request *)malloc(sizeof(struct espresso_http_server_request));
            request->request = buffer;
            espresso_http_server_callable(request);

            const char *response = request->response;
            response_length = strlen(response);

            result = send(client, response, response_length, 0);

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
            result = send(client, response, response_length, 0);
        }

        if (client == -1)
        {
            espresso_http_server_handle_error(6, errno);
        }

        close(client);
    }

    close(server);
}

void espresso_http_server_handle_error(byte type, int32 errno)
{
    if (espresso_error_callable)
    {
        espresso_error_callable("Unable to start a new espresso server using the provided port.");
    }
}

#endif
