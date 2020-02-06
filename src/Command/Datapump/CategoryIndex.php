<?php

namespace App\Command\Datapump;

use App\Command\Datapump\Es;

class CategoryIndex
{
    private $em;
    private $mapping;

    public function __construct($em)
    {
        $this->em = $em;
        $this->mapping = file_get_contents(__DIR__ . '/Mappings/category.json');
    }

    public function createIndex()
    {
        $result = Es::qryES('GET', 'vue_storefront_catalog_category?pretty=true');
        if($result == 200){
            return "index 'vue_storefront_catalog_category'  already exists";
        }else{
            $result = Es::qryES('PUT', 'vue_storefront_catalog_category', $this->mapping);
            if($result == 200){
                return "index 'vue_storefront_catalog_category'  created";
            }else{
                return "index 'vue_storefront_catalog_category'  NOT created";
            }
        }
    }
}