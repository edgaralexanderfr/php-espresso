#ifndef ESPRESSO_H
#define ESPRESSO_H

void (*espresso_error_callable)(const char *);
const char *(*espresso_http_server_callable)(const char *);

extern void setEspressoErrorCallable(void (*callable)(const char *));

extern void setEspressoHttpServerCallable(const char *(*callable)(const char *));

extern void listenEspressoServer(unsigned short port);

#endif
