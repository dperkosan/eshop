<?php

namespace App\Command\Datapump;

use App\Command\Datapump\Es;

class ProductIndex
{
    private $em;
    private $mapping;

    public function __construct($em)
    {
        $this->em = $em;
        $this->mapping = file_get_contents(__DIR__ . '/Mappings/product.json');
    }

    public function createIndex()
    {
        $result = Es::qryES('GET', 'vue_storefront_catalog_product?pretty=true');
        if($result == 200){
            return "index 'vue_storefront_catalog_product'   already exists";
        }else{
            $result = Es::qryES('PUT', 'vue_storefront_catalog_product', $this->mapping);
            if($result == 200){
                return "index 'vue_storefront_catalog_product'   created";
            }else{
                return "index 'vue_storefront_catalog_product'   NOT created";
            }
        }
    }
}