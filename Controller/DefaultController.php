<?php

namespace Yunai39\Bundle\OpenVPNManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Yunai39\Bundle\OpenVPNManagementBundle\Service\ConnectTelnetService;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	
		$conf = $this->container->getParameter('openvpn.servers');
    	$connectS = new ConnectTelnetService($conf);
		$var = $connectS->getInfoAllServeur();

        return $this->render('OpenVPNManagementBundle:Default:index.html.twig',array('serveurs' =>$var ));
    }
	
	public function killAction(Request $request,$numSer, $cn){
		$conf = $this->container->getParameter('openvpn.servers');
    	$connectS = new ConnectTelnetService($conf);
		$var = $connectS->killUser($numSer, $cn);
		if($var == true){
			$this->get('session')->getFlashBag()->add(
				'success' , 'SUCCESS: La connection de '.$cn.' a été interrompu'
			);
		}else{
			$this->get('session')->getFlashBag()->add(
				'error' , 'ERROR: Lors de la suppression de la connection de '.$cn
			);
		}
        return $this->redirect($this->generateUrl('openvpn_web_int_index'));
		
	}
}
