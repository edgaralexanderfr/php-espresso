build:
	go build -o lib/cthreads.so -buildmode=c-shared cthreads/cthreads.go

clean:
	rm lib/cthreads*
