<?php
/**
 * Dyode_Core Magento2 Module.
 *
 * Parent module for all Dyode modules
 *
 * @package Dyode
 * @module  Dyode_Core
 * @author  Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Dyode_Core',
    __DIR__
);