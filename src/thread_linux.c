#ifndef ESPRESSO_THREAD_LINUX_C
#define ESPRESSO_THREAD_LINUX_C

#include <pthread.h>

#include "types.h"

void espresso_thread_start(void (*callable)(mixed *, uint32), mixed *args, uint32 args_count)
{
    pthread_t thread_id;
    struct espresso_thread_params *params = (struct espresso_thread_params *)malloc(sizeof(struct espresso_thread_params));

    params->callable = callable;
    params->args = args;
    params->args_count = args_count;

    pthread_create(&thread_id, null, espresso_thread_run, (mixed *)params);
}

void *espresso_thread_run(void *args)
{
    struct espresso_thread_params *params = (struct espresso_thread_params *)args;

    params->callable(params->args, params->args_count);

    free(params);

    return null;
}

#endif
