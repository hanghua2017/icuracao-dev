<?php
namespace Magento\SalesRule\Api\Data;

/**
 * ExtensionInterface class for @see \Magento\SalesRule\Api\Data\RuleInterface
 */
interface RuleExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return int|null
     */
    public function getRewardPointsDelta();

    /**
     * @param int $rewardPointsDelta
     * @return $this
     */
    public function setRewardPointsDelta($rewardPointsDelta);
}
