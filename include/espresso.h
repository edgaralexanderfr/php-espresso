#ifndef ESPRESSO_H
#define ESPRESSO_H

void (*espresso_error_callable)(const char *);

extern void setEspressoErrorCallable(void (*callable)(const char *));

extern void listenEspressoServer(unsigned short port);

#endif
