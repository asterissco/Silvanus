Silvanus v 0.5
========================

Welcome to Silvanus iptables firewall web administrator to set up custom chains for advance 
Firewall

1) Requeriments 
----------------------------------

Linux Like operating system
php 5.3+ with cli module
mysql 5+ (or other Database Doctrine and PDO suppport)
PDO php extensions
Web Server (Apache 2+ recommended with mod rewrite)
git
cron (recommended)

2) Previous installation

Install Mysql (or other server) and create a database.
Create a user to connect with all privileges

3) Installation (with git)

### Database and configuration

Create your homework directory

Clone the project with git	
	
	administrador@lab2:/var/www/silvanustest$ git clone https://github.com/asterissco/Silvanus.git

Enter in the Silvanus directory

	administrador@lab2:/var/www/silvanustest$ cd Silvanus/

Comment the DoctrineFixturesBundle (Bug for next version)

	administrador@lab2:/var/www/silvanustest/Silvanus$ vim app/AppKernel.php 
	//          new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),

Option, updete with composer

	administrador@lab2:/var/www/silvanustest/Silvanus$ php composer.phar update

Set database (Mysql in this case) connection

	administrador@lab2:/var/www/silvanustest/Silvanus$ vim app/config/parameters.yml

Test database connection

	administrador@lab2:/var/www/silvanustest/Silvanus$ php app/console doctrine:query:sql "select 1"
	array(1) {
	  [0]=>
	  array(1) {
		[1]=>
		string(1) "1"
	  }
	}

Generate database schema

	administrador@lab2:/var/www/silvanustest/Silvanus$ php app/console doctrine:schema:create

### Publish Symfony2 web directory in your Web Server

Follow the official instructions for Symfony2 project

	http://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html
	
4) How to use (example)

Create a new chain 

	name: test_chain
	host: 192.168.100.50
	
Create a firewall rule

	Go to Admin Firewall Rules
	New rule
	
		rule: -s 0.0.0.0/0 -d /host/ -p TCP --dport 21 -j ACCEPT
		priority:
		Append: select
		
Run sync script (In the console like root)

	root@lab2:/var/www/silvanustest/Silvanus# php app/console silvanus:sync
	#2 test_chain
	[ok]		iptables -I test_chain 1 -s 0.0.0.0/0 -d  192.168.100.50  -p TCP --dport 21 -j ACCEPT 2>&1 

Show the magic effect

	root@lab2:/var/www/silvanustest/Silvanus# iptables -L -x -n
	Chain INPUT (policy ACCEPT)
	target     prot opt source               destination         
	test_chain  all  --  0.0.0.0/0            0.0.0.0/0           

	Chain FORWARD (policy ACCEPT)
	target     prot opt source               destination         

	Chain OUTPUT (policy ACCEPT)
	target     prot opt source               destination         

	Chain test_chain (1 references)
	target     prot opt source               destination         
	ACCEPT     tcp  --  0.0.0.0/0            192.168.100.50       tcp dpt:21
	


5) Insert sync script in Cron

Like root

	root@lab2:/var/www/silvanustest/Silvanus# crontab -e

Add 5 minutes execute

	*/5 * * * * php /var/www/silvanustest/Silvanus/app/console silvanus:sync
