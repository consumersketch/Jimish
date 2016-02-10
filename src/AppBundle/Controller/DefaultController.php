<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;  

class DefaultController extends Controller
{
    private $entityManager;
    private function init(){
        $this->reportManager = $this->get('report_manager');
    }

    /**
    *  Genrates search form
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $this->init();

        $data = array();
        $form = $this->createFormBuilder($data)            
            ->add('client', 'choice',
                array('choices' => $this->getClientData(),
                    'placeholder'=>""))
            ->add('product', 'choice',
                array('choices' => array(),
                    'placeholder'=>""))
            ->add('relative_date', 'choice',
                array('choices' => array(
                    'LAST_MONTH_DATE'   => 'Last Month to Date',
                    'THIS_MONTH' => 'This Month',
                    'THIS_YEAR'   => 'This Year',
                    'LAST_YEAR'   => 'Last Year',
                ),'placeholder'=>""))
            ->getForm();

            return $this->render('AppBundle::index.html.twig', array(
                     'form' => $form->createView(),
            ));
    }

    /**
     * Get client's product information 
     * @Route("/get_product_data", name="get_product_data")
     */
    public function getProductDataAction(Request $request){
        $this->init();
        $clientId = $request->query->get('client_id');
        
        $response = new Response(json_encode($this->getProductDataFromClientId($clientId)));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Get invoice data based on criteria 
     * @Route("/get_report_data", name="get_report_data")
     */
    public function getReportDataAction(Request $request)
    {
        $this->init();
        $clientId = $request->query->get('client_id');
        $productId = $request->query->get('product_id');
        $relativeDate = $request->query->get('relative_date');
        

        $response = new Response(json_encode($this->getIvoiceData($clientId,$productId,$relativeDate)));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }


    /**
    * Get client information 
    * 
    * @return array $clientDataToReturn Client Information    
    */

    private function getClientData(){
        $clientsInformation = $this->reportManager->getClientData();        
        $clientDataToReturn = array();
        foreach($clientsInformation as $clientInformation){
               $clientDataToReturn[$clientInformation['client_id']] =  $clientInformation['client_name'];
        }

        return $clientDataToReturn;
    }

    /**
    * Get client's product information 
    * 
    * @param string $clientId Client ID  
    * @return array $productDataToReturn Client's Product Information
    */
    private function getProductDataFromClientId($clientId){
        $clientProuctsInformation = $this->reportManager->getProductDataFromClientId($clientId);        

        $productDataToReturn = array();
        foreach($clientProuctsInformation as $clientProuctInformation){
               $productDataToReturn[$clientProuctInformation['product_id']] =  $clientProuctInformation['product_description'];
        }

        return $productDataToReturn;        
    }

    /**
    * Get invoice data based on criteria 
    * 
    * @param string $clientId Client ID  
    * @param integer $productId Product ID  
    * @param string $relativeDate Relative date
    *
    * @return array  Invoice data
    */
    private function getIvoiceData($clientId,$productId,$relativeDate){   
        return $this->reportManager->getIvoiceData($clientId,$productId,$relativeDate);
    }

}
