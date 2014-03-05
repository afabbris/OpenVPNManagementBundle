<?php

namespace Yunai39\Bundle\OpenVPNManagementBundle\Service;


/**
 * ConnectTelnetService
 * 
 * This service will connect to the VPN server to get information
 * 
 */
class ConnectTelnetService{

	/**
	 * OpenVPN configuration information
	 * @var array $openvpn_conf
	 */
	private $openvpn_conf;

	
	/**
	 * __construct
	 * 

	 * @method void 
	 * 
	 * @param array $openvpn_conf	openVPN Configuration
	 * 
	 */
	function __construct($openvpn_conf){
		$this->openvpn_conf = $openvpn_conf;
	}
	
	
	public function killUser($numS,$cn){
		if(!isset($this->openvpn_conf[$numS]))
			return false;
		$vpn_name = $this->openvpn_conf[$numS]['name'];
		$vpn_host = $this->openvpn_conf[$numS]['ip'];
		$vpn_port = $this->openvpn_conf[$numS]['telnet_port'];
		$vpn_password = $this->openvpn_conf[$numS]['telnet_password'];
		// -----------------------------
		$fp = fsockopen($vpn_host, $vpn_port, $errno, $errstr, 30);
		if (!$fp) {
		    echo "$errstr ($errno)<br />\n";
		    exit;
		}
		
		fwrite($fp, $vpn_password."\n\n\n");
		usleep(250000);
		fwrite($fp, "kill ".$cn."\n\n\n");
		usleep(250000);
		fwrite($fp, "quit\n\n\n");
		usleep(250000);
		while (!feof($fp)) {
		    $line = fgets($fp, 128);
			if(strpos($line,$cn) !== false){
				if(strpos($line,'SUCCESS') === false){
					return false;
				}else{
					return true;
				}
			}
		}
		
	}
	
	
	public function getInfoServeur($numS){
		if(!isset($this->openvpn_conf[$numS]))
			return false;
		$vpn_name = $this->openvpn_conf[$numS]['name'];
		$vpn_host = $this->openvpn_conf[$numS]['ip'];
		$vpn_port = $this->openvpn_conf[$numS]['telnet_port'];
		$vpn_password = $this->openvpn_conf[$numS]['telnet_password'];

		
		$fp = fsockopen($vpn_host, $vpn_port, $errno, $errstr, 30);
		if (!$fp) {
		    echo "$errstr ($errno)<br />\n";
		    exit;
		}
		
		fwrite($fp, $vpn_password."\n\n\n");
		usleep(250000);
		fwrite($fp, "status\n\n\n");
		usleep(250000);
		fwrite($fp, "quit\n\n\n");
		usleep(250000);
		$clients = array();
		$inclients = $inrouting = false;
		while (!feof($fp)) {
		    $line = fgets($fp, 128);
		    if (substr($line, 0, 13) == "ROUTING TABLE") {
		        $inclients = false;
		    }
		    if ($inclients) {
		        $cdata = explode (',', $line);
		        $clines[$cdata[1]] = array($cdata[2], $cdata[3], $cdata[4]);
		    }
		    if (substr($line, 0, 11) == "Common Name") {
		        $inclients = true;
		    }
		
		    if (substr($line, 0, 12) == "GLOBAL STATS") {
		        $inrouting = false;
		    }
		    if ($inrouting) {
		        $routedata = explode (',', $line);
		        array_push($clients, array_merge($routedata, 
		$clines[$routedata[2]]));
		    }
		    if (substr($line, 0, 15) == "Virtual Address") {
		        $inrouting = true;
		    }
		}
		
		$headers = array('VPN Address', 'Name', 'Real Address', 'Last Act', 
		'Recv', 'Sent', 'Connected Since');
		$tdalign = array('left', 'left', 'left', 'left', 'right', 'right', 
		'left');
		
		fclose($fp);
		return array('header' =>$headers,'clients' => $clients, 'name' => $vpn_name, 'key' => $numS);
	}

	public function getInfoAllServeur(){
		$retour = array();
		foreach($this->openvpn_conf as $key => $conf){
			$retour[$key] = $this->getInfoServeur($key);
		}
		return $retour;
	}
}