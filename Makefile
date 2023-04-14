build:
	go build -o lib/gthreads.so -buildmode=c-shared gthreads/gthreads.go

clean:
	rm lib/gthreads*
