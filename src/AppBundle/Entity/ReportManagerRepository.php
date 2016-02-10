<?php
namespace AppBundle\Entity;
/**
* To get invoice related information
*/
class ReportManagerRepository 
{
	private $entityManager;

	public function __construct(\Doctrine\ORM\EntityManager $entityManager){
		$this->entityManager = $entityManager;

	}

	/**
	* Get cliet information
	*
	* @return array Client Information
	*/
	public function getClientData() {
		$query = "SELECT * FROM clients";
		
		return $this->entityManager->getConnection()->executeQuery($query)->fetchAll();
	}

	/**
	* Get client's product information 
	* 
	* @param string $clientId Client ID  
	* @return array Client's Product Information
	*/
	public function getProductDataFromClientId($clientId){
		$query = "SELECT * FROM products WHERE client_id=:bClientId order by product_description";

		$params = array('bClientId'=>$clientId);
		return $this->entityManager->getConnection()->executeQuery($query,$params)->fetchAll();	
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
	public function getIvoiceData($clientId,$productId,$relativeDate){
		$query = "SELECT inv.invoice_num,inv.invoice_date,pro.product_id,invi.product_id,pro.product_description,invi.qty,invi.price,(invi.qty*invi.price) as total FROM invoices inv
					INNER JOIN invoicelineitems invi on inv.invoice_num = invi.invoice_num
					INNER JOIN products pro on pro.product_id = invi.product_id and inv.invoice_num = invi.invoice_num";

		$where = array();			
		$params = array();
		if($clientId){
			$where[] = "inv.client_id=:bClientId";
			$params['bClientId'] = $clientId;
		}

		if($productId){
			$where[] = "pro.product_id=:bProductId";
			$params['bProductId'] = $productId;
		}

		if($relativeDate){
			  switch($relativeDate){
			  	case 'LAST_MONTH_DATE':
			  		$datestring='first day of last month';
					$dateObect=date_create($datestring);		

					$where[] = "inv.invoice_date between :bdateStart and :bdateEnd";
			  		
					$params['bdateEnd'] = date("Y-m-d");
					$params['bdateStart'] = $dateObect->format('Y-m-d');
			  		break;
			  	case 'THIS_MONTH':
			  		$thisYearAndDate = date('Y-m');

			  		$where[] = "inv.invoice_date between :bdateStart and :bdateEnd";
			  		$params['bdateStart'] = $thisYearAndDate."-01";
					$params['bdateEnd'] = $thisYearAndDate."-31";
			  		break;
			  	case 'THIS_YEAR':
			  		$thisYear = date('Y');
			  		$where[] = "inv.invoice_date between :bdateStart and :bdateEnd";
			  		
					$params['bdateEnd'] = $thisYear."-12-31";
					$params['bdateStart'] = $thisYear."-01-01";
			  		break;
			  	case 'LAST_YEAR':
			  		$lastYear = date('Y')-1;
			  		$where[] = "inv.invoice_date between :bdateStart and :bdateEnd";

					$params['bdateEnd'] = $lastYear."-12-31";
					$params['bdateStart'] = $lastYear."-01-01";
			  		break;
			  } 
			
		}

		if(count($where)){
			$query .= " WHERE ".implode(" AND ",$where);
		}
		$query .= " order by inv.invoice_date,inv.invoice_num";

		return $this->entityManager->getConnection()->executeQuery($query,$params)->fetchAll();
	}
}
