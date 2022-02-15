<?php
/**
 * Copyright © Rajesh All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Rajesh\Test\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Rajesh\Test\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
class Copy extends Command
{

    const NAME_ARGUMENT = "name";
    const NAME_OPTION = "option";

    /**
     * {@inheritdoc}
     */
	 public function __construct(
		Data $data,
		CollectionFactory $prodCollFactory,
		ProductRepositoryInterface $productRepository,
		LoggerInterface $logger = null,
		State $state
	 )
	 {
		$this->prodCollFactory = $prodCollFactory;
		$this->productRepository = $productRepository;
		$this->logger = $logger;
		$this->data = $data; 
	    $this->state = $state;
		parent::__construct();
	 
	 }
    protected function execute(	   
        InputInterface $input,
        OutputInterface $output
    ) {
		$this->state->setAreaCode(Area::AREA_ADMINHTML); 
        $name = strtolower($input->getArgument(self::NAME_ARGUMENT));
        $option = $input->getOption(self::NAME_OPTION);
		if($name=='used')
		{
		$copyto=$name;
		}
		elseif($name=='refurbished')
		{
			
		$copyto=$name;
			
		}
		else
		{
			
		$output->writeln("Set Valid arugment like used,refurbished ");	
		 exit();
		}
		$collection = $this->prodCollFactory->create();
		$collection->addAttributeToSelect('*')
        ->addAttributeToFilter(
            array(
                array('attribute'=>'custom_product_type','eq'=>$this->data->getOptionIdByLabel('custom_product_type','new'))
            )
        );
		$str='';
		$count=0;
		if($collection->getSize()>0){
		foreach ($collection->getAllIds() as $productId)
        {			
		$productDataObject = $this->productRepository->getById($productId);
		if($this->data->SaveWithCondition($productDataObject,$copyto))
		{
		$count++;	
		}
		}
		}
		$error=$collection->getSize()-$count;
        $output->writeln("Total product found ".$collection->getSize()." and converted  " .$collection->getSize()." error ".$error);
    }



    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("rajesh_test:copy");
        $this->setDescription("Copy condition based product");
        $this->setDefinition([
            new InputArgument(self::NAME_ARGUMENT, InputArgument::OPTIONAL, "Name"),
            new InputOption(self::NAME_OPTION, "-a", InputOption::VALUE_NONE, "Option functionality")
        ]);
        parent::configure();
    }
}

