#include <stdlib.h>
#include <unistd.h>

//#include <seccomp.h>
//#include <linux/seccomp.h>

#define SU_UID 1000
#define SU_GID 1000

#define TIMEOUT "3"

int main() {
/*
	scmp_filter_ctx ctx;

	ctx = seccomp_init(SCMP_ACT_KILL); // default action: kill

	seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(rt_sigreturn), 0);
	seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(exit), 0);
	seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(read), 0);
	seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(write), 0);
	seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(exit_group), 0);
	seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(open), 0);
	seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(close), 0);
	seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(execve), 0);
	seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(setgroups), 0);
	seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(setuid), 0);
	seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(setgid), 0);

	seccomp_load(ctx);
*/
	setgroups(0);
	setgid(SU_GID);
	setuid(SU_UID);

//	chroot(getenv("PWD"));

	execl("/usr/bin/timeout", "/usr/bin/timeout", "-s", "SIGKILL", TIMEOUT, "./code", NULL);

}