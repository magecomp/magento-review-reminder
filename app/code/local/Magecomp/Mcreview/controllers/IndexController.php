<?php
class Magecomp_Mcreview_IndexController extends Mage_Core_Controller_Front_Action
{
    public function unsubscribeAction()
    {
		echo 'call';
		$id=$this->getRequest()->getParam('email');
		echo $id;
		$session = Mage::getSingleton('core/session');
		try
		{
			if($id!= '')
			{
				Mage::getModel('newsletter/subscriber')->loadByEmail($id)->unsubscribe();
				$session->addSuccess($this->__('You have been unsubscribed.'));
				echo 'redirect';
			}
		}
		catch(Exception $e)
		{
			$session->addException($e, $this->__('There was a problem with the un-subscription.'));
		}
		$this->_redirect("/");
	}
}