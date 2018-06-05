<?php
/**
* Copyright © 2018 Codazon. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Codazon\Lookbookpro\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface {
    
    private $setupFactory;

    public function __construct(
        \Codazon\Lookbookpro\Setup\LookbookproSetupFactory $setupFactory
    ) {
        $this->setupFactory = $setupFactory;
    }
    
    
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $moduleSetup = $this->setupFactory->create(['setup' => $setup]);
        $moduleSetup->installEntities();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $collection = $objectManager->create('Codazon\Lookbookpro\Model\ResourceModel\LookbookCategory\Collection')
            ->addFieldToFilter('entity_id', 1);
        if (!$collection->count()) {
            $model = $objectManager->create('Codazon\Lookbookpro\Model\LookbookCategory');
            $model->addData([
                'entity_id'         => 1,
                'name'              => 'Root Cateory',
                'parent_id'         => '0',
                'path'              => '1',
                'position'          => '0',
                'level'             => '0',
                'children_count'    => '1',
                'url_key'           => 'root',
                'url_path'          => 'root.html'
            ]);
            $model->save();
        }
        
        $collection = $objectManager->create('Codazon\Lookbookpro\Model\ResourceModel\LookbookCategory\Collection')
            ->addFieldToFilter('entity_id', 2);
        if (!$collection->count()) {
            $model = $objectManager->create('Codazon\Lookbookpro\Model\LookbookCategory');
            $model->addData([
                'entity_id'         => 2,
                'name'              => 'All Lookbooks',
                'parent_id'         => '1',
                'path'              => '1/2',
                'position'          => '0',
                'level'             => '1',
                'children_count'    => '0',
                'url_key'           => 'all-lookbook',
                'url_path'           => 'all-lookbook.html'
            ]);
            $model->save();
        }
        
        $setup->endSetup();
    }
    
}
