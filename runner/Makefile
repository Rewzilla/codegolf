
CC		= gcc
CFLAGS	= -O3

all:
	$(CC) $(CFLAGS) -o runner runner.c

install:
	cp runner /usr/local/bin/runner
	chmod +s /usr/local/bin/runner

cleanup:
	rm -rf runner