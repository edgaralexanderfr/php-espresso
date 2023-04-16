#ifndef __CTHREADS_H__
#define __CTHREADS_H__

int CT_Exec(void (*callable)());

void *call(void *vargp);

#endif