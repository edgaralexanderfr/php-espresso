build: build-cthreads
	go build -o lib/gthreads.so -buildmode=c-shared gthreads/gthreads.go

build-cthreads:
	gcc -c -Wall -Werror -fpic cthreads/cthreads.c -o lib/cthreads.o && gcc -shared -o lib/cthreads.so lib/cthreads.o

clean:
	rm lib/gthreads* && rm lib/cthreads*

php-zts:
	docker-compose run --service-ports --rm php-8.1.18-zts bash
