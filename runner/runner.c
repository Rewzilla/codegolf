#include <stdlib.h>
#include <unistd.h>

#define SU_UID 1000
#define SU_GID 1000

#define TIMEOUT "3"

int main() {

	setgroups(0);
	setgid(SU_GID);
	setuid(SU_UID);

//	chroot(getenv("PWD"));

	execl("/usr/bin/timeout", "/usr/bin/timeout", "-s", "SIGKILL", TIMEOUT, "./code", NULL);

}