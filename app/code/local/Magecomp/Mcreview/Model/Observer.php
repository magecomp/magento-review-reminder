<?php
class Magecomp_Mcreview_Model_Observer
{
    public function sendReviewmail($observer)
    {
		Mage::log('sendReviewmail call');
		
		$dayDiff=Mage::getStoreConfig('mcreview/general/daydiff');

		$time = time();
		$lastTime = $time - (60*60*24*$dayDiff); // 60*60*24
		$from = date('Y-m-d 0:0:1', $lastTime);
		$to = date('Y-m-d 23:59:59', $lastTime);
		$orders = Mage::getModel('sales/order')->getCollection()
			->addAttributeToFilter('created_at', array('from'=>$from, 'to'=>$to))
			->addAttributeToFilter('status', array('eq' => Mage_Sales_Model_Order::STATE_COMPLETE));
		
		foreach($orders as $order)
		{
			$name=$order->getCustomerFirstname().' '.$order->getCustomerLastname();
			$email=$order->getCustomerEmail();
			
			$items = $order->getAllVisibleItems();
			foreach($items as $i)
			{
				   $_product = Mage::getModel('catalog/product')->load($i->getProductId());
					$ProductName=$_product->getName();
					$ProductUrl=$_product->getProductUrl();
			}
			
			$translate = Mage::getSingleton('core/translate');						
			$translate->setTranslateInline(false);
			$emailTemp = Mage::getModel('core/email_template');
			$storeId = Mage::app()->getStore()->getId();
			
			
			$sender = Mage::getStoreConfig('mcreview/general/adminemailsender');
			
			$emailTempVariables = array();					
			$emailTempVariables['customername'] = $name;
			$emailTempVariables['pname'] = $ProductName;
			$emailTempVariables['purl'] = $ProductUrl;
			$emailTempVariables['uemail'] = $email;
			
			
			$admintemplate = Mage::getStoreConfig('mcreview/general/adminemailtemplate');
				
			try
			{
				$emailTemp->setDesignConfig(array('area'=>'frontend'))
					->sendTransactional(
					$admintemplate,
					$sender,
					$email,
					$name,
					$emailTempVariables,
					$storeId
					);
					
				$translate->setTranslateInline(true);
				
				Mage::log('revie mail send');
			}
			catch(Exception $error)
			{
				 Mage::log('error mail review mail send');
			}	
			
		}
	}
}
?>  