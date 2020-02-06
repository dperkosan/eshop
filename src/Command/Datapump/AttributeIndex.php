<?php

namespace App\Command\Datapump;

use App\Command\Datapump\Es;

class AttributeIndex
{
    private $em;
    private $mapping;

    public function __construct($em)
    {
        $this->em = $em;
        $this->mapping = file_get_contents(__DIR__ . '/Mappings/attribute.json');
    }

    public function createIndex()
    {
        $result = Es::qryES('GET', 'vue_storefront_catalog_attribute?pretty=true');
        if($result == 200){
            return "index 'vue_storefront_catalog_attribute' already exists";
        }else{
            $result = Es::qryES('PUT', 'vue_storefront_catalog_attribute', $this->mapping);
            if($result == 200){
                return "index 'vue_storefront_catalog_attribute' created";
            }else{
                return "index 'vue_storefront_catalog_attribute' NOT created";
            }
        }
    }
}