<?php

namespace App\Command\Datapump;

use App\Command\Datapump\Es;

class TaxruleIndex
{
    private $em;
    private $mapping;

    public function __construct($em)
    {
        $this->em = $em;
        $this->mapping = file_get_contents(__DIR__ . '/Mappings/taxrule.json');
    }

    public function createIndex()
    {
        $result = Es::qryES('GET', 'vue_storefront_catalog_taxrule?pretty=true');
        if($result == 200){
            return "index 'vue_storefront_catalog_taxrule'   already exists";
        }else{
            $result = Es::qryES('PUT', 'vue_storefront_catalog_taxrule', $this->mapping);
            if($result == 200){
                return "index 'vue_storefront_catalog_taxrule'   created";
            }else{
                return "index 'vue_storefront_catalog_taxrule'   NOT created";
            }
        }
    }
}