package main

// typedef void (*callable)();
// static void run(callable c) { c(); }
import "C"

func main() {}

//export GT_Exec
func GT_Exec(callback C.callable) C.int {
	go call(callback)

	return 0
}

func call(callback C.callable) {
	C.run(callback)
}
