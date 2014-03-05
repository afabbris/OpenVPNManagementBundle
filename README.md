OpenVPNManagementBundle
=======================

This bundle will create a web interface to a telnet interface of an OpenVPN server, for Symfony Application. This interface will show all the user connected to a VPN server and you wil also have the possibility to log out user.

Installation
------------



You need to add a package to your dependency list :

	"yunai39/openvpn-management-bundle"

Then you will need a composer update:

	composer update "yunai39/openvpn-management-bundle"

Dont forget to add the Bundle to the kernel

	//app/AppKernel.php
	new Yunai39\Bundle\SimpleLdapBundle\SimpleLdapBundle(),


And also add the following routing:

	openvpn:
	    resource: "@OpenVPNManagementBundle/Resources/config/routing.yml"
        prefix:   /openvpn

Configuration
-------------

You can add multiple server, but you need to add a list one. Your OpenVPN server must be configured to use a telnet interface.

    openvpn.servers: 
    	1:
			ip: 192.168.0.149
			name: OpenVPN - 1
			telnet_port: 7050
			telnet_password: azerty
	openvpn.conf.reload: 5 # Every 5 minutes the page will be reloaded

You will also need to add openvpn.conf.reload as a twig global like this

	twig:
    	globals:
        	openvpn_conf_reload: %openvpn.conf.reload%

As for custom css, the main div containing the information about, every single serveur has the class openvpn_info_main, each table is openvpn_info_table and the information at the bottom is openvpn_info_bottom.
For the page to be relod every time to create a base.html.twig who has a meta block as the index extends  '::base.html.twig'

The to access you server info go to the page /openvpn/infoS. 
