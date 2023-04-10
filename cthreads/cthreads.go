package main

// typedef void (*cb)();
// static void helper(cb f) { f(); }
import "C"
import (
	"time"
)

func main() {}

//export ExecuteGoRoutine
func ExecuteGoRoutine(callback C.cb) C.int {
	go myGoRoutine(callback)

	return 0
}

func myGoRoutine(callback C.cb) {
	time.Sleep(3 * time.Second)

	C.helper(callback)
}
