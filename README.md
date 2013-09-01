Silvanus v0.6
========================

Current version:

 - Support multiple chains add to trusted iptables chains (INPUT, FORDWARD, OUTPUT)
 - Support multiple Rules for chains
 - Test the rules previus apply the rules by chains
 - Debug information of synchronization in webpage
 - Synchronization status list
 - Support Magic Word "/host/" (see How To Use)
<br>

**This is no tested version, use with careful**

<br>

Welcome to Silvanus iptables firewall web administrator to set up custom chains for large and advance configuration.

Silvanus is Symfony2 project.

This version is functional Beta, dont support security (recommended use htpassword). Use with careful

1) Requeriments 
----------------------------------

 - Linux Like operating system
 - php 5.3+ with cli module
 - mysql 5+ (or other Database Doctrine and PDO suppport)
 - PDO php extensions
 - Web Server (Apache 2+ recommended with mod rewrite)
 - Composer
 - git
 - cron (recommended)

2) Previous installation
----------------------------------

Install Mysql (or other server) and create a database.
Create a user to connect with all privileges

3) Installation (with git)
----------------------------------

### Database and configuration

Create your homework directory

Clone the project with git	
	
	administrador@lab2:/var/www/silvanustest$ git clone https://github.com/asterissco/Silvanus.git

Enter in the Silvanus directory

	administrador@lab2:/var/www/silvanustest$ cd Silvanus/


Update with composer

	administrador@lab2:/var/www/silvanustest/Silvanus$ php composer.phar update

Set database (Mysql in this case) configuration

	administrador@lab2:/var/www/silvanustest/Silvanus$ vim app/config/parameters.yml

Set enviroment configuration:

	administrador@lab2:/var/www/silvanus/Symfony$ vim app/config/config.yml 

	Inside the document 

	# Silvanus config
		parameters:
			test_chain: 'silvanus_test_chain' #chain to test rules before apply
			iptables_path: '/sbin/iptables'	  #regular path to iptables (Needed for CRON)

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

Generate fixtures

	administrador@lab2:/var/www/silvanustest/Silvanus$ php app/console doctrine:fixtures:load


### Publish Symfony2 web directory in your Web Server

Follow the official instructions for Symfony2 project

	http://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html
	
4) How to use (example)
----------------------------------
Create a new chain (host is optionally)

	name: test_chain
	host: 192.168.100.50
	truested: {INPUT,FORDWARD,OUTPUT} least an option

 

	
Create a firewall rule ("/host/" is a "Magic Word" to refered Chains Host value, 192.168.100.50 in this case )

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
	


5) Insert sync script in Cron (Recommended)

Like root

	root@lab2:/var/www/silvanustest/Silvanus# crontab -e

Add 1 minutes execute

	*/1 * * * * php /var/www/silvanustest/Silvanus/app/console silvanus:sync >> /var/log/silvanus.log

6) This project use

 - Symfony 2
 - iptables
 - mysql 5
 - doctrine 2
 - twig 3
 - jquery 2
 - PHP 5.3.3

### Thanks you for use!

Alejandro Cabrera.
