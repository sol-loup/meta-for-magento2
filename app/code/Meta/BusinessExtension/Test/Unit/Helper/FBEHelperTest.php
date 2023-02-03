<?php
/**
 * Copyright (c) Meta Platforms, Inc. and affiliates.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Meta\BusinessExtension\Test\Unit\Helper;

use Meta\BusinessExtension\Helper\FBEHelper;
use Meta\BusinessExtension\Logger\Logger;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Meta\BusinessExtension\Model\System\Config;
use PHPUnit\Framework\TestCase;

class FBEHelperTest extends TestCase
{
    private $fbeHelper;

    private $systemConfig;

    private $context;

    private $objectManagerInterface;

    private $logger;

    private $storeManager;

    private $curl;

    private $resourceConnection;

    private $moduleList;

    /**
     * Used to reset or change values after running a test
     *
     * @return void
     */
    public function tearDown(): void
    {
    }

    /**
     * Used to set the values before running a test
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->context = $this->createMock(Context::class);
        $this->objectManagerInterface = $this->createMock(ObjectManagerInterface::class);
        $this->logger = $this->createMock(Logger::class);
        $this->storeManager = $this->createMock(StoreManagerInterface::class);
        $this->curl = $this->createMock(Curl::class);
        $this->resourceConnection = $this->createMock(ResourceConnection::class);
        $this->moduleList = $this->createMock(ModuleListInterface::class);
        $this->systemConfig = $this->createMock(Config::class);

        $this->fbeHelper = new FBEHelper(
            $this->context,
            $this->objectManagerInterface,
            $this->logger,
            $this->storeManager,
            $this->curl,
            $this->resourceConnection,
            $this->moduleList,
            $this->systemConfig
        );
    }

    /**
     * Test partner agent is correct
     *
     * @return void
     */
    public function testCorrectPartnerAgent()
    {
        $magentoVersion = '2.3.5';
        $pluginVersion = '1.0.0';
        $this->moduleList->method('getOne')->willReturn(
            [
                'setup_version' => $pluginVersion
            ]
        );
        $source = $this->fbeHelper->getSource();
        $productMetadata = $this->getMockBuilder(ProductMetadataInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getVersion', 'getEdition', 'getName'])
            ->getMock();
        $productMetadata->method('getVersion')->willReturn($magentoVersion);
        $productMetadata->method('getVersion')->willReturn($magentoVersion);
        $this->objectManagerInterface->method('get')->willReturn($productMetadata);
        $this->assertEquals(
            sprintf('%s-%s-%s', $source, $magentoVersion, $pluginVersion),
            $this->fbeHelper->getPartnerAgent(true)
        );
    }
}