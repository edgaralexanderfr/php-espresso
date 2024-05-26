build-dev:
	gcc -c -Wall -Werror -fpic -Iinclude src/espresso.c -o lib/espresso.o -D'ESPRESSO_DEBUG'
	gcc -shared -o lib/espresso.so lib/espresso.o -D'ESPRESSO_DEBUG'

run-test:
	gcc -Wall -Werror -fpic -Iinclude src/test.c -o bin/test -D'ESPRESSO_DEBUG'
	chmod +x bin/test
	./bin/test

build:
	gcc -c -Wall -Werror -fpic -Iinclude src/espresso.c -o lib/espresso.o
	gcc -shared -o lib/espresso.so lib/espresso.o

clean:
	rm lib/*.o
	rm lib/*.so