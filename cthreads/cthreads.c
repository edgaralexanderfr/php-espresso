#include <pthread.h>

#include "cthreads.h"

extern int CT_Exec(void (*callable)())
{
    pthread_t thread_id;

    pthread_create(&thread_id, NULL, call, callable);

    return 0;
}

void *call(void *vargp)
{
    void (*callable)() = vargp;

    callable();

    return NULL;
}