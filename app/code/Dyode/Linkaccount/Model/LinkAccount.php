<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       17/09/2018
 */
 
namespace Dyode\Linkaccount\Model;
use Dyode\AuditLog\Model\ResourceModel\AuditLog;

class LinkAccount extends \Magento\Framework\Model\AbstractModel
{
	/**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

	/**
     *
     * @var type \Magento\Framework\Message\ManagerInterface
     */
	protected $auditLog;
	
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Customer\Model\CustomerFactory $customerFactory
	 * @param  AuditLog $auditLog,
     */
	public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		AuditLog $auditLog,
		\Magento\Framework\Registry $data
	) {
		$this->storeManager     = $storeManager;
		$this->auditLog = $auditLog;
		$this->customerFactory  = $customerFactory;
		return parent::__construct($context, $data);
	}

	public function create($curacaoCustId, $email, $fName, $lName, $password)
	{
		$returnArray = array();
		try {
			// Get Website ID
			$websiteId  = $this->storeManager->getWebsite()->getWebsiteId();
			// Instantiate object (this is the most important part)
			$customer = $this->customerFactory->create();
			$customer->setWebsiteId($websiteId);
			// Preparing data for new customer
			$customer->setEmail($email);
			$customer->setFirstname($fName);
			$customer->setLastname($lName);
			$customer->setPassword($password);
			// Save Curacao Customer Id
            if (!empty($curacaoCustId)) {
                $customerData = $customer->getDataModel();
                $customerData->setCustomAttribute('curacaocustid', $curacaoCustId);
                $customer->updateData($customerData);
            }
			// $customer->setCustomAttribute("curacaocustid", "12463468");
			// Save data
			$customer->save();
		} catch (\Exception $e) {}

		if (empty($e)) {
			$returnArray = array(
				'INFO' => 'Account created Successfully',
				'OK' => true
			);
		} else {
			$returnArray = array(
				'ERROR' => $e->getMessage(),
				'INFO' => 'Account Not Created',
				'OK' => false
			);
		}
		//logging audit log
		$this->auditLog->saveAuditLog([
			'user_id'     => "",
			'action'      => 'API call',
			'description' => "Fail to create customer".$curacaoCustId,
			'client_ip'   => "",
			'module_name' => "Dyode_ARWebservice",
		]);

		return json_encode($returnArray);
	}
}
