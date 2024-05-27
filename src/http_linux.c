#ifndef ESPRESSO_HTTP_LINUX_C
#define ESPRESSO_HTTP_LINUX_C

#include <arpa/inet.h>
#include <stdlib.h>
#include <string.h>
// #include <sys/ioctl.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <unistd.h>

// #include "debug.h"
#include "types.h"

// #include "debug.c"

void espresso_http_server_listen(uint16 port)
{
    extern int32 errno;
    struct sockaddr_in address;
    socklen_t address_length = sizeof(address);
    int32 result;

    int32 server = socket(AF_INET, SOCK_STREAM, 0);

    if (server == -1)
    {
        printf("%s\n", "Error while creating the socket.");

        exit(1);
    }

    int32 opt = 1;

#ifdef __APPLE__
    result = setsockopt(server, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt));
#else
    result = setsockopt(server, SOL_SOCKET, SO_REUSEADDR | SO_REUSEPORT, &opt, sizeof(opt));
#endif

    if (result == -1)
    {
        printf("`setsockopt` error #%i.\n", errno);

        exit(2);
    }

    address.sin_family = AF_INET;
    address.sin_port = htons(port);
    address.sin_addr.s_addr = INADDR_ANY;

    result = bind(server, (struct sockaddr *)&address, sizeof(address));

    if (result == -1)
    {
        printf("Binding error #%i.\n", errno);

        exit(3);
    }

    result = listen(server, 4);

    if (result == -1)
    {
        printf("%s\n", "Error while listening the server.");

        exit(4);
    }

    const uint32 buffer_size = 65536;
    int32 client;
    char buffer[buffer_size];

    while (true)
    {
        client = accept(server, (struct sockaddr *)&address, &address_length);

        if (client == -1)
        {
            printf("%s\n", "Error while accepting the connection.");

            exit(5);
        }

        memset(buffer, 0, buffer_size);
        result = recv(client, buffer, buffer_size - 1, 0);

        const string response = "HTTP/1.1 200 OK\n\n";
        uint32 response_length = strlen(response);

        send(client, response, response_length, 0);

        close(client);
    }

    close(server);
}

#endif
