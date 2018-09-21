<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Controller;

/**
 * Location controller test
 */
class LocationTest extends \Magento\TestFramework\TestCase\AbstractController
{
    public function testViewAction()
    {
        $this->dispatch('aw_store_locator/index/index');
        $body = $this->getResponse()->getBody();

        $this->assertContains('<title>Store Locations</title>', $body);
        $this->assertRegExp('/<li class\="item aw_store_locator">[\s\S]*Store Locations[\s\S]*<\/li>/', $body);
        $this->assertRegExp('/<h1 class\="page-title"[^>]*>[\s\S]*Store Locations[\s\S]*<\/h1>/', $body);
    }
}
