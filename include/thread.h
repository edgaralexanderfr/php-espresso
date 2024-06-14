#ifndef ESPRESSO_THREAD_H
#define ESPRESSO_THREAD_H

#include "types.h"

struct espresso_thread_params
{
    void (*callable)(mixed *, uint32);
    mixed *args;
    uint32 args_count;
};

void espresso_thread_start(void (*callable)(mixed *, uint32), mixed *args, uint32 args_count);

#endif
