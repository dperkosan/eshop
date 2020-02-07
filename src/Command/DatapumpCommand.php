<?php

// import for different stores (locale)
// import for more product types then just configurable and simple
// import simple products that are not visible, but are part of configurable products?

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\EntityManagerInterface;

use App\Command\Datapump\TaxruleIndex;
use App\Command\Datapump\CategoryIndex;
use App\Command\Datapump\AttributeIndex;
use App\Command\Datapump\ProductIndex;

class DatapumpCommand extends Command
{
    protected static $defaultName = 'app:datapump';
    private $em;
    private $childrenCount;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this ->setDescription('Populate ES.')->setHelp('This command allows you to populate ES');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '',
            'Create indexes',
            '============',
            '',
        ]);

        $taxRuleIndex = new TaxruleIndex($this->em);
        $response = $taxRuleIndex->createIndex();
        $output->writeln($response);

        $categoryIndex = new CategoryIndex($this->em);
        $response = $categoryIndex->createIndex();
        $output->writeln($response);

        $attributeIndex = new AttributeIndex($this->em);
        $response = $attributeIndex->createIndex();
        $output->writeln($response);

        $productIndex = new ProductIndex($this->em);
        $response = $productIndex->createIndex();
        $output->writeln($response);
        
        $output->writeln([
            '',
            'Import data into ES',
            '============',
            '',
        ]);

        $categoryIndex->importData();
        $attributeIndex->importData();
        $productIndex->importData();
    }
}