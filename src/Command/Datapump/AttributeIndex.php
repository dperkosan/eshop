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

    public function importData()
    {
        $attributes = $this->getAttributes();

        foreach($attributes as $attribute){
            $options = $this->getOptions($attribute['id']);
            $qry = [
                "is_wysiwyg_enabled" => false,
                "is_html_allowed_on_front" => true,
                "used_for_sort_by" => false,
                "is_filterable" => true,
                "is_filterable_in_search" => false,
                "is_used_in_grid" => true,
                "is_visible_in_grid" => false,
                "is_filterable_in_grid" => true,
                "position" => (int)0,
                "apply_to" => [
                    "simple","virtual","bundle","downloadable","configurable"
                ],
                "is_searchable" => 0,
                "is_visible_in_advanced_search" => 0,
                "is_comparable" => 0,
                "is_used_for_promo_rules" => 1,
                "is_visible_on_front" => 0,
                "used_in_product_listing" => 1,
                "is_visible" => true,
                "scope" => "global",
                "attribute_id" => (int)$attribute['id'],
                "attribute_code" => $attribute['code'],
                "frontend_input" => "select",
                "entity_type_id" => null,
                "is_required" => false,
                "options" => $options,
                "is_user_defined" => true,
                "default_frontend_label" => $attribute['name'],
                "frontend_labels" => null,
                "backend_type" => "int",
                "source_model" => null,
                "default_value" => $attribute['value'],
                "is_unique" => "0",
                "validation_rules" => [],
                "id" => (int)$attribute['id']
            ];
            
            $result = Es::qryES('POST', 'vue_storefront_catalog_attribute/_doc/'.$attribute['id'], json_encode($qry));
        }
    }

    private function getAttributes(){
        $conn = $this->em->getConnection();

        $sql = 'SELECT o.id, o.code, t.name, v.id AS value
                FROM sylius_product_option o 
                LEFT JOIN sylius_product_option_translation t ON t.translatable_id = o.id AND t.locale = "en_US" 
                LEFT JOIN sylius_product_option_value v ON v.option_id = o.id GROUP BY o.id';
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $attributes = $stmt->fetchAll();

        return $attributes;
    }

    private function getOptions($attributeId){
        $options = [];
        $conn = $this->em->getConnection();

        $sql = 'SELECT v.id, t.value 
        FROM sylius_product_option_value v
        LEFT JOIN sylius_product_option_value_translation t ON t.translatable_id = v.id AND t.locale = "en_US"
        WHERE option_id = '.$attributeId;
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        foreach($results as $key => $result){
            $options[] = [
                "label" => $result['value'],
                "value" => $result['id']
            ];
        }

        return $options;
    }
}