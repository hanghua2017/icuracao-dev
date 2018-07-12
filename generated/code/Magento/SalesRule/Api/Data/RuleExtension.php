<?php
namespace Magento\SalesRule\Api\Data;

/**
 * Extension class for @see \Magento\SalesRule\Api\Data\RuleInterface
 */
class RuleExtension extends \Magento\Framework\Api\AbstractSimpleObject implements RuleExtensionInterface
{
    /**
     * @return int|null
     */
    public function getRewardPointsDelta()
    {
        return $this->_get('reward_points_delta');
    }

    /**
     * @param int $rewardPointsDelta
     * @return $this
     */
    public function setRewardPointsDelta($rewardPointsDelta)
    {
        $this->setData('reward_points_delta', $rewardPointsDelta);
        return $this;
    }
}
