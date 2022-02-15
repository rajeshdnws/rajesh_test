<?php
/**
 * Copyright © Rajesh All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Rajesh\Test\Helper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;
use Magento\Catalog\Model\Attribute\ScopeOverriddenValue;
use Magento\Store\Model\Store;
use Magento\Catalog\Api\Data\ProductInterface;
class Data extends AbstractHelper

{
     /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var ScopeOverriddenValue
     */
    private $scopeOverriddenValue;

    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        ScopeOverriddenValue $scopeOverriddenValue,
        MetadataPool $metadataPool
    )
    {

        $this->productFactory = $productFactory;
        $this->scopeOverriddenValue = $scopeOverriddenValue;
        $this->metadataPool = $metadataPool;
        parent::__construct($context);
    }
    public function SaveWithCondition($product,$copyto)
    {
        try
        {
            $product->getWebsiteIds();
            $product->getCategoryIds();
            $websiteIds = $product->getWebsiteIds();
		    $categoryIds = $product->getCategoryIds();
		    $duplicate = null;
            $metadata = $this->metadataPool->getMetadata(ProductInterface::class);            
            /** @var Product $duplicate */
            $duplicate = $this->productFactory->create();
            $productData = $product->getData();
            //$productData = $this->removeStockItem($productData);
            $duplicate->setOptions([]);
            $duplicate->setIsDuplicate(true);
            $duplicate->setData($productData);
            $duplicate->setCreatedAt(null);
		    $duplicate->setUpdatedAt(null);
		    $duplicate->setId(null);
			$duplicate->setUrlKey($this->checkWord($product->getUrlKey(),1,$copyto));
			$newSku=$product->getSku().$product->getId();
			$duplicate->setSku($this->checkWord($product->getSku(),1,$copyto));
			$duplicate->setCustomProductType($this->getOptionIdByLabel('custom_product_type',$copyto));
			$duplicate->setName($this->checkWord($product->getName(),0,$copyto));    
			$duplicate->setData('meta_title',$this->checkWord($product->getMetaTitle()?$product->getMetaTitle():$product->getName(),0,$copyto));
			$duplicate->setData('meta_description',$this->checkWord($product->getMetaDescription()?$product->getMetaDescription():$product->getName(),0,$copyto));
			$duplicate->setWebsiteIds($websiteIds);
			$duplicate->setCategoryIds($categoryIds);
			$duplicate->save();       
            return true;
        }
      catch (\Exception $e) {
         $e->getMessage();
        return false;  
    }


    }
	
	private function checkWord($mystring,$flag,$val)
	{
	$word='new';	
	if(strpos(strtolower($mystring), $word) !== false){
	 return str_replace($word,$val,$mystring);
	} else{
	if($flag==1){
	return $mystring.'-'.$val;
	} else
	{
	return  $mystring.' '.$val;	
	}
	}
		
		
	}
    /* Get Option id by Option Label */
    public function getOptionIdByLabel($attributeCode,$optionLabel)
    {
        $product = $this->productFactory->create();
        $isAttributeExist = $product->getResource()->getAttribute($attributeCode);
        $optionId = '';
        if ($isAttributeExist && $isAttributeExist->usesSource()) {
            $optionId = $isAttributeExist->getSource()->getOptionId($optionLabel);
        }
        return $optionId;
    }
}