#ifndef ESPRESSO_H
#define ESPRESSO_H

struct espresso_http_server_request
{
    const char *request;
    const char *response;
    void (*free)();
};

struct espresso_http_server_call
{
    void (*callable)(struct espresso_http_server_request *);
};

void (*espresso_error_callable)(const char *);
void (*espresso_http_server_callable)(struct espresso_http_server_call *);

extern void setEspressoErrorCallable(void (*callable)(const char *));

extern void setEspressoHttpServerCallable(void (*callable)(struct espresso_http_server_call *));

extern void listenEspressoServer(unsigned short port);

#endif
