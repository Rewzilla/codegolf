#!/bin/sh

echo "Flushing all chains"
iptables -F

echo "Setting default policy to DROP"
iptables -P FORWARD DROP
iptables -P OUTPUT DROP
iptables -P INPUT DROP

echo "Allowing anything marked RELATED/ESTABLISHED"
iptables -A INPUT -m conntrack --ctstate RELATED,ESTABLISHED -j ACCEPT -m comment --comment "ACCEPT incoming RELATED/ESTABLISHED"
iptables -A OUTPUT -m conntrack --ctstate RELATED,ESTABLISHED -j ACCEPT -m comment --comment "ACCEPT outgoing RELATED/ESTABLISHED"

echo "Allowing everything on loopback"
iptables -A INPUT -s 127.0.0.1 -j ACCEPT -m comment --comment "ACCEPT all incoming on loopback"
iptables -A OUTPUT -d 127.0.0.1 -j ACCEPT -m comment --comment "ACCEPT all outgoing on loopback"

echo "Dropping anything marked INVALID"
iptables -A INPUT -m conntrack --ctstate INVALID -j DROP -m comment --comment "REJECT anything marked INVALID"

#echo "Allowing ping"
#iptables -A INPUT -p icmp --icmp-type 8 -m conntrack --ctstate NEW -j ACCEPT -m comment --comment "ACCEPT incoming ping request"
#iptables -A OUTPUT -p icmp --icmp-type 0 -m state --state ESTABLISHED,RELATED -j ACCEPT -m comment --comment "ACCEPT outgoing ping reply"

echo "Allowing anything from root (uid 0)"
iptables -A OUTPUT -m owner --uid-owner 0 -j ACCEPT

echo "Dropping anything from runner (uid 1000)"
iptables -A OUTPUT -m owner --uid-owner 1000 -j DROP

echo "Allowing services"

	echo " - ssh       (IN)"
	iptables -A INPUT -p tcp --dport 22 -m conntrack --ctstate NEW -j ACCEPT -m comment --comment "ACCEPT incoming ssh"

	echo " - dns       (OUT)"
	iptables -A OUTPUT -p udp --dport 53 -m conntrack --ctstate NEW -j ACCEPT -m comment --comment "ACCEPT outgoing dns"

	echo " - http      (IN)"
	iptables -A INPUT -p tcp --dport 80 -m conntrack --ctstate NEW -j ACCEPT -m comment --comment "ACCEPT incoming http"

	echo " - https     (IN)"
	iptables -A INPUT -p tcp --dport 443 -m conntrack --ctstate NEW -j ACCEPT -m comment --comment "ACCEPT incoming https"

echo "Gracefully rejecting everything else"
iptables -A INPUT -p udp -j REJECT --reject-with icmp-port-unreachable -m comment --comment "Graceful UDP REJECTs"
iptables -A INPUT -p tcp -j REJECT --reject-with tcp-rst -m comment --comment "Graceful TCP REJECTS"
iptables -A INPUT -j REJECT --reject-with icmp-proto-unreachable -m comment --comment "Graceful UNKNOWN REJECTs"

echo "Saving configuration"
iptables-save > /etc/iptables/iptables.rules