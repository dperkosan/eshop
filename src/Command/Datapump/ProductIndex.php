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

    public function importData()
    {
        $products = $this->getProducts();
        // attribute_set_id ?
        // product_links ?
        // custom_attributes ?
        // small_image ?
        // thumbnail ?
        // different types of pricing ?
        // required_options ?
        // msrp_display_actual_price_type ?
        // tax_class_id ?
        // material
        // eco_collection
        // performance_fabric
        // new
        // sale
        // style_general
        // pattern
        // climate
        // fields in stock
        foreach($products as $product){
            $type_id = $product['configurable'] > 0 ? "configurable" : "simple";
            $has_options = $product['configurable'] > 0 ? "1" : "0";
            $enabled = $product['enabled'] == 1 ? 1 : 2;
            $visibility = $product['enabled'] == 1 ? 4 : 1;
            $categories = $this->getCategories($product['id']);
            $configurableOptions = $this->getConfigurableOptions($product['id']);
            $media = $this->getMedia($product['id']);
            $configurableChildren = $this->getConfigurableChildren($product['id']);
            $categoriesArr = [];
            foreach($categories as $category){
                $categoriesArr[] = [
                    "category_id" => (int)$category['id'],
                    "name" => $category['name'],
                    "slug" => $this->getUrlKey($category['slug']),
                    "path" => $category['slug']
                ];
            }
            
            $qry = [
                "id" => (int)$product['id'],
                "sku" => $product['sku'],
                "name" => $product['name'],
                "price" => (float)$product['price'],
                "status" => (int)$enabled,
                "visibility" => (int)$visibility,
                "type_id" => $type_id,
                "created_at" => $product['created_at'],
                "updated_at" => $product['updated_at'],
                "product_links" => [],
                "tier_prices" => [],
                "custom_attributes" => null,
                "final_price" => (float)$product['price'],
                "max_price" => (float)$product['price'],
                "max_regular_price" => (float)$product['price'],
                "minimal_regular_price" => (float)$product['price'],
                "special_price" => null,
                "minimal_price" => (float)$product['price'],
                "regular_price" => (float)$product['price'],
                "description" => trim(preg_replace('/\s+/', ' ', strip_tags($product['description']))),
                "image" => '/'.$product['image'],
                "small_image" => '/'.$product['image'],
                "thumbnail" => '/'.$product['image'],
                "category_ids" => explode(",",$product['category_ids']),
                "options_container" => "",
                "required_options" => "0",
                "has_options" => $has_options,
                "url_key" => $product['url_key'],
                "msrp_display_actual_price_type" => "0",
                "tax_class_id" => null,
                "material" => [],
                "eco_collection" => "0",
                "performance_fabric" => "0",
                "new" => "0",
                "sale" => "0",
                "style_general" => null,
                "pattern" => null,
                "climate" => [],
                "slug" => $product['url_key'],
                "links" => null,
                "stock" => [
                    "item_id" => (int)$product['id'],
                    "product_id" => (int)$product['id'],
                    "stock_id" => (int)1,
                    "qty" => (int)$product['qty'],
                    "is_in_stock" => true,
                    "is_qty_decimal" => false,
                    "show_default_notification_message" => false,
                    "use_config_min_qty" => true,
                    "min_qty" => (int)0,
                    "use_config_min_sale_qty" => (int)1,
                    "min_sale_qty" => (int)1,
                    "use_config_max_sale_qty" => true,
                    "max_sale_qty" => (int)10000,
                    "use_config_backorders" => true,
                    "backorders" => (int)0,
                    "use_config_notify_stock_qty" => true,
                    "notify_stock_qty" => (int)1,
                    "use_config_qty_increments" => true,
                    "qty_increments" => (int)0,
                    "use_config_enable_qty_inc" => true,
                    "enable_qty_increments" => false,
                    "use_config_manage_stock" => true,
                    "manage_stock" => true,
                    "low_stock_date" => null,
                    "is_decimal_divided" => false,
                    "stock_status_changed_auto" => (int)0
                ],
                "media_gallery" => $media,
                "configurable_children" => $configurableChildren,
                "configurable_options" => $configurableOptions['configurableOptions'],
                "url_path" => 'products/'.$product['url_key'],
                "price_incl_tax" => null,
                "special_price_incl_tax" => null,
                "special_to_date" => null,
                "special_from_date" => null,
                "category" => $categoriesArr
            ];

            foreach($configurableOptions['filterValuesGrouped'] as $key => $i) {
                $qry[$key] = $i;
            }
            
            $result = Es::qryES('POST', 'vue_storefront_catalog_product/_doc/'.$product['id'], json_encode($qry));
        }
/*
        $variantProducts = $this->getVariantProducts();
        foreach($variantProducts as $variantProduct){
            $categories = $this->getCategories($variantProduct['product_id']);
            $media = $this->getVariantMedia($variantProduct['id']);
            $categoriesArr = [];
            foreach($categories as $category){
                $categoriesArr[] = [
                    "category_id" => (int)$category['id'],
                    "name" => $category['name'],
                    "slug" => $this->getUrlKey($category['slug']),
                    "path" => $category['slug']
                ];
            }
            
            $qry = [
                "id" => (int)$variantProduct['id'],
                "sku" => $variantProduct['sku'],
                "name" => $variantProduct['name'],
                "price" => (float)$variantProduct['price'],
                "status" => (int)1,
                "visibility" => (int)1,
                "type_id" => "simple",
                "created_at" => $variantProduct['created_at'],
                "updated_at" => $variantProduct['updated_at'],
                "product_links" => [],
                "tier_prices" => [],
                "custom_attributes" => null,
                "final_price" => (float)$variantProduct['price'],
                "max_price" => (float)$variantProduct['price'],
                "max_regular_price" => (float)$variantProduct['price'],
                "minimal_regular_price" => (float)$variantProduct['price'],
                "special_price" => null,
                "minimal_price" => (float)$variantProduct['price'],
                "regular_price" => (float)$variantProduct['price'],
                "description" => trim(preg_replace('/\s+/', ' ', strip_tags($variantProduct['description']))),
                "image" => !empty($variantProduct['image']) ? '/'.$variantProduct['image'] : $variantProduct['image'],
                "small_image" => !empty($variantProduct['image']) ? '/'.$variantProduct['image'] : $variantProduct['image'],
                "thumbnail" => !empty($variantProduct['image']) ? '/'.$variantProduct['image'] : $variantProduct['image'],
                "category_ids" => explode(",",$variantProduct['category_ids']),
                "options_container" => "",
                "required_options" => "0",
                "has_options" => "0",
                "url_key" => $variantProduct['url_key'],
                "msrp_display_actual_price_type" => "0",
                "tax_class_id" => null,
                "slug" => $product['url_key'],
                "links" => null,
                "stock" => [
                    "item_id" => (int)$variantProduct['id'],
                    "product_id" => (int)$variantProduct['id'],
                    "stock_id" => (int)1,
                    "qty" => (int)$variantProduct['qty'],
                    "is_in_stock" => true,
                    "is_qty_decimal" => false,
                    "show_default_notification_message" => false,
                    "use_config_min_qty" => true,
                    "min_qty" => (int)0,
                    "use_config_min_sale_qty" => (int)1,
                    "min_sale_qty" => (int)1,
                    "use_config_max_sale_qty" => true,
                    "max_sale_qty" => (int)10000,
                    "use_config_backorders" => true,
                    "backorders" => (int)0,
                    "use_config_notify_stock_qty" => true,
                    "notify_stock_qty" => (int)1,
                    "use_config_qty_increments" => true,
                    "qty_increments" => (int)0,
                    "use_config_enable_qty_inc" => true,
                    "enable_qty_increments" => false,
                    "use_config_manage_stock" => true,
                    "manage_stock" => true,
                    "low_stock_date" => null,
                    "is_decimal_divided" => false,
                    "stock_status_changed_auto" => (int)0
                ],
                "media_gallery" => $media,
                "url_path" => 'products/'.$variantProduct['url_key'],
                "category" => $categoriesArr
            ];

            $result = Es::qryES('POST', 'vue_storefront_catalog_product/_doc/v-'.$variantProduct['id'], json_encode($qry));
        }
        */
    }

    private function getProducts(){
        $conn = $this->em->getConnection();
        $sql = 'SELECT p.id, t.name, i.path AS image, p.code AS sku, t.slug as url_key, 
                count(o.product_id) as configurable, inventory.price/100 AS price, pt.category_ids, p.enabled,
                p.created_at, p.updated_at, t.description, IF(count(o.product_id) > 0, 0, inventory.qty) AS qty
                FROM sylius_product p
                LEFT JOIN sylius_product_image i ON p.id = i.owner_id 
                LEFT JOIN sylius_product_translation t ON p.id = t.translatable_id
                LEFT JOIN sylius_product_options o ON p.id = o.product_id
                LEFT JOIN (SELECT product_id, price as price, on_hand - on_hold AS qty
                            FROM sylius_channel_pricing p
                            LEFT JOIN sylius_product_variant v ON p.product_variant_id = v.id
                            GROUP BY product_id) inventory ON p.id = inventory.product_id
                LEFT JOIN (SELECT product_id, GROUP_CONCAT(taxon_id) AS category_ids
                            FROM sylius_product_taxon
                            GROUP BY product_id) pt ON p.id = pt.product_id
                WHERE t.locale = "en_US"
                GROUP BY p.id';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetchAll();

        return $products;
    }

    private function getVariantProducts(){
        $conn = $this->em->getConnection();
        $sql = 'SELECT v.id, p.id AS product_id, v.code AS sku, CONCAT(t.name, " - ", vt.name) AS name, pr.price/100 AS price, v.created_at, v.updated_at,
                t.description, im.path AS image, t.slug AS url_key, v.id, pt.category_ids, v.on_hand - v.on_hold AS qty
                FROM sylius_product_variant v
                    LEFT JOIN sylius_product_image_product_variants iv ON iv.variant_id = v.id
                    LEFT JOIN sylius_product_image im ON im.id = iv.image_id
                    LEFT JOIN sylius_product p ON p.id = v.product_id
                    LEFT JOIN sylius_product_translation t ON t.translatable_id = v.product_id AND t.locale = "en_US"
                    LEFT JOIN sylius_product_variant_translation vt ON vt.translatable_id = v.id AND vt.locale = "en_US"
                    LEFT JOIN sylius_channel_pricing pr ON pr.product_variant_id = v.id
                    LEFT JOIN (SELECT product_id, GROUP_CONCAT(taxon_id) AS category_ids
                                FROM sylius_product_taxon
                                GROUP BY product_id) pt ON pt.product_id = v.product_id
                    GROUP BY v.id';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetchAll();

        return $products;
    }

    private function getCategories($product=false){
        $conn = $this->em->getConnection();
        
        $sql = 'SELECT c.id, t.name, t.slug
                FROM sylius_taxon c
                LEFT JOIN sylius_taxon_translation t ON c.id = t.translatable_id 
                LEFT JOIN sylius_product_taxon p ON c.id = p.taxon_id
                LEFT JOIN (SELECT GROUP_CONCAT(id) AS children, parent_id 
                            FROM sylius_taxon 
                            GROUP BY parent_id) ch ON c.id = ch.parent_id
                WHERE c.parent_id is not null AND t.locale = "en_US" AND p.product_id = '.$product .
                ' GROUP BY c.id';
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $categories = $stmt->fetchAll();

        return $categories;
    }

    private function getConfigurableOptions($productId){
        $options = [];
        $values = [];
        $configurableOptions = [];
        $filterValuesGrouped = [];
        $conn = $this->em->getConnection();

        $sql = 'SELECT po.id AS attributeId, pot.name AS attributeLabel, po.position, po.code AS attributeCode, pv.product_id, povt.value AS valueLabel, pov.id AS valueIndex
                FROM sylius_product_variant pv
                    INNER JOIN sylius_product_variant_option_value pvov ON pvov.variant_id = pv.id
                    INNER JOIN sylius_product_option_value pov ON pov.id = pvov.option_value_id
                    INNER JOIN sylius_product_option_value_translation povt ON povt.translatable_id = pov.id AND povt.locale = "en_US"
                    INNER JOIN sylius_product_option po ON po.id = pov.option_id
                    INNER JOIN sylius_product_option_translation pot ON pot.translatable_id = po.id AND pot.locale = "en_US"
                WHERE pv.product_id = '.$productId.'
                GROUP BY po.id, pot.name, po.position, po.code, pv.product_id, povt.value, pov.id';
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        foreach($results as $key => $result){
            $configurableOptions[$result['attributeId']]['product_id'] = $result['product_id'];
            $configurableOptions[$result['attributeId']]['id'] = $result['attributeId'];
            $configurableOptions[$result['attributeId']]['label'] = $result['attributeLabel'];
            $configurableOptions[$result['attributeId']]['position'] = $result['position'];
            $configurableOptions[$result['attributeId']]['attribute_code'] = $result['attributeCode'];
            $configurableOptions[$result['attributeId']]['values'][$result['valueIndex']] = [
                'label' => $result['valueLabel']
            ];
        }

        foreach($configurableOptions as $attributeId => $configurableOption){
            $values = [];
            $filterValues = [];
            foreach($configurableOptions[$attributeId]['values'] as $valueIndex => $value){
                $values[] = [
                    "value_index" => (int)$valueIndex,
                    "label" => $value['label']
                ];
                $filterValues[] = $valueIndex;
            }
            
            $filterValuesGrouped[$configurableOption['attribute_code'].'_options'] = $filterValues;

            $options[] = [
                "attribute_id" => $attributeId,
                "values" => $values,
                "product_id" => (int)$configurableOption['product_id'],
                "id" => (int)$configurableOption['id'],
                "label" => $configurableOption['label'],
                "position" => (int)$configurableOption['position'],
                "attribute_code" => $configurableOption['attribute_code']
            ];
        }

        return [
            "configurableOptions" => $options,
            "filterValuesGrouped" => $filterValuesGrouped
        ];
        
    }

    private function getMedia($productId){
        $media = [];
        $conn = $this->em->getConnection();

        $sql = 'SELECT path 
                FROM sylius_product_image 
                WHERE owner_id = '.$productId;
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        foreach($results as $key => $result){
            $media[] = [
                "vid" => null,
                "image" => '/'.$result['path'],
                "pos" => (int)$key,
                "typ" => "image",
                "lab" => ""
            ];
        }

        return $media;
    }

    private function getVariantMedia($productId){
        $media = [];
        $conn = $this->em->getConnection();

        $sql = 'SELECT path 
                FROM sylius_product_image_product_variants ipv
                LEFT JOIN sylius_product_image pi ON pi.id = ipv.image_id
                WHERE ipv.variant_id = '.$productId;
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        foreach($results as $key => $result){
            $media[] = [
                "vid" => null,
                "image" => '/'.$result['path'],
                "pos" => (int)$key,
                "typ" => "image",
                "lab" => ""
            ];
        }

        return $media;
    }

    private function getConfigurableChildren($productId){
        $children = [];
        $conn = $this->em->getConnection();

        $sql = 'SELECT IFNULL(im.path, imp.path) AS image, t.slug AS url_key, v.code AS sku, v.id, pr.price/100 AS price, 
                CONCAT(t.name, " - ", vt.name) AS name, pt.category_ids, p.enabled AS status
                FROM sylius_product_variant v
                    LEFT JOIN sylius_product_image_product_variants iv ON iv.variant_id = v.id
                    LEFT JOIN sylius_product_image im ON im.id = iv.image_id
                    LEFT JOIN sylius_product p ON p.id = v.product_id
                    LEFT JOIN sylius_product_image imp ON imp.owner_id = p.id and imp.type = "main"
                    LEFT JOIN sylius_product_translation t ON t.translatable_id = v.product_id AND t.locale = "en_US"
                    LEFT JOIN sylius_product_variant_translation vt ON vt.translatable_id = v.id AND vt.locale = "en_US"
                    LEFT JOIN sylius_channel_pricing pr ON pr.product_variant_id = v.id
                    LEFT JOIN (SELECT product_id, GROUP_CONCAT(taxon_id) AS category_ids
                                FROM sylius_product_taxon
                                GROUP BY product_id) pt ON pt.product_id = v.product_id
                    WHERE v.product_id = '.$productId;
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        foreach($results as $key => $result){
            $status = $result['status'] == 1 ? 1 : 2;
            $optionValues = $this->getOptionValues($result['id']);
            $children[$key] = [
                "image" => '/'.$result['image'],
                "thumbnail" => '/'.$result['image'],
                "small_image" => '/'.$result['image'],
                "tax_class_id" => null,
                "has_options" => "0",
                "tier_prices" => [],
                "url_key" => $result['url_key'],
                "required_options" => "0",
                "msrp_display_actual_price_type" => "0",
                "price" => (float)$result['price'],
                "name" => $result['name'],
                "id" => (int)$result['id'],
                "category_ids" => explode(",",$result['category_ids']),
                "sku" => $result['sku'],
                "status" => (int)$status
            ];
            $children[$key] = array_merge($children[$key], $optionValues);
        }

        return $children;
    }

    private function getOptionValues($childId){
        $options = [];
        $conn = $this->em->getConnection();

        $sql = 'SELECT v.id, po.code, ov.option_value_id 
                FROM sylius_product_variant v
                    INNER JOIN sylius_product_variant_option_value ov ON ov.variant_id = v.id
                    INNER JOIN sylius_product_option_value pov ON pov.id = ov.option_value_id
                    INNER JOIN sylius_product_option po ON po.id = pov.option_id
                WHERE v.id = '.$childId;
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        foreach($results as $key => $result){
            $options[$result['code']] = $result['option_value_id'];
        }

        return $options;
    }

    private function getUrlKey($slug){
        $pos = strrpos($slug, '/');
        $key = $pos === false ? $slug : substr($slug, $pos + 1);

        return $key;
    }
}