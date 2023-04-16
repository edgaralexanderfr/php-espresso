build:
	go build -o lib/gthreads.so -buildmode=c-shared gthreads/gthreads.go

clean:
	rm lib/gthreads*

php-zts:
	docker-compose run --service-ports --rm php-8.1.18-zts bash
