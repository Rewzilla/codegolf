#include <stdlib.h>
#include <unistd.h>

#define SU_UID 1000
#define SU_GID 1000

#define TIMEOUT "3"

int main() {

//	chroot(getenv("PWD"));

	setgroups(0);
	setgid(SU_GID);
	setuid(SU_UID);

	execl("/usr/bin/timeout", "/usr/bin/timeout", "-s", "SIGKILL", TIMEOUT, "./code", NULL);

}