
/*
This uses a blacklist approch.  This is NOT the most secure
way to do it, but is easier than figuring out all the syscalls
that the program actually needs :P  Also, I'd rather leave
creative options open for those golfers that may want/need to
use them.  Filters will be added on an as-needed basis.
*/

#include <seccomp.h>
#include <linux/seccomp.h>

void __attribute__((constructor)) init() {

	scmp_filter_ctx ctx;

	ctx = seccomp_init(SCMP_ACT_ALLOW);

	seccomp_rule_add(ctx, SCMP_ACT_KILL, SCMP_SYS(open), 0);
	seccomp_rule_add(ctx, SCMP_ACT_KILL, SCMP_SYS(fork), 0);
	seccomp_rule_add(ctx, SCMP_ACT_KILL, SCMP_SYS(execve), 0);
	seccomp_rule_add(ctx, SCMP_ACT_KILL, SCMP_SYS(unlink), 0);
	seccomp_rule_add(ctx, SCMP_ACT_KILL, SCMP_SYS(kill), 0);

	seccomp_load(ctx);

}