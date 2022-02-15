<?php
/**
 * Copyright © Rajesh All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Rajesh\Test\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Rajesh\Test\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
class MassCopy extends Action
{

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $prodCollFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param Context                                         $context
     * @param Filter                                          $filter
     * @param CollectionFactory                               $prodCollFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $prodCollFactory,
        Data  $data,
        ProductRepositoryInterface $productRepository
        )
    {
        $this->filter = $filter;
        $this->prodCollFactory = $prodCollFactory;
        $this->productRepository = $productRepository;
        $this->data = $data;
        parent::__construct($context);
    }
    
	
	 
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException | \Exception
     */
    public function execute()
    {
        $copyto = $this->getRequest()->getParam('iscopy'); 
        $collection = $this->filter->getCollection($this->prodCollFactory->create());
	     //$collection->addAttributeToSelect('*')->addAttributeToFilter('color', array('eq' => '5'));
        $count=0;
        foreach ($collection->getAllIds() as $productId)
        {
         $productDataObject = $this->productRepository->getById($productId);
    	if($this->data->SaveWithCondition($productDataObject,$copyto))
        {
            $count++;
        }
      
		         
        }
       $error=$collection->getSize()-$count;
       
        $this->messageManager->addSuccess(__("Total product found ".$collection->getSize()." and converted  " .$collection->getSize()." error ".$error));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('catalog/product/index');
    }
}