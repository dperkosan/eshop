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

        die;
        


        $categories = $this->getCategories();
        // is_anchor ?
        // display_mode ?
        // custom_layout_update ?
        foreach($categories as $category){
            $this->childrenCount = 0;
            $children = $this->getChildren($categories, $category['id']);
            $qry = '
            {
                "id": '.$category['id'].',
                "parent_id" : '.$category['parent_id'].',
                "name" : "'.$category['name'].'",
                "is_active" : true,
                "position": '.$category['position'].',
                "level" : '.$category['level'].',
                "product_count" : '.$category['product_count'].',
                "children_data" : ['.$children.'],
                "children" : "'.$category['children'].'",
                "created_at": "'.$category['created_at'].'",
                "updated_at": "'.$category['updated_at'].'",
                "path" : "'.$this->getPath($categories, $category['id']).'",
                "available_sort_by" : [],
                "include_in_menu": true,
                "description" : "'.$category['description'].'",
                "meta_title" : "'.$category['name'].'",
                "meta_description" : "'.$category['description'].'",
                "display_mode": "PRODUCTS",
                "is_anchor": "1",
                "custom_layout_update": "",
                "children_count": "'.$this->childrenCount.'",
                "url_key" : "'.$this->getUrlKey($category['slug']).'",
                "url_path" : "'.$category['slug'].'",
                "slug" : "'.$this->getUrlKey($category['slug']).'"
            }';
            
            $result = $this->qryES('POST', 'vue_storefront_catalog_category/_doc/'.$category['id'], $qry);
        }

        $output->writeln([
            '============',
            '',
            'Insert attributes into ES',
            '============',
            '',
        ]);

        $attributes = $this->getAttributes();
        foreach($attributes as $attribute){
            $options = $this->getOptions($attribute['id']);
            $qry = '
            {
                "is_wysiwyg_enabled": false,
                "is_html_allowed_on_front": true,
                "used_for_sort_by": false,
                "is_filterable": true,
                "is_filterable_in_search": false,
                "is_used_in_grid": true,
                "is_visible_in_grid": false,
                "is_filterable_in_grid": true,
                "position": 0,
                "apply_to": [
                    "simple","virtual","bundle","downloadable","configurable"
                ],
                "is_searchable": "0",
                "is_visible_in_advanced_search": "0",
                "is_comparable": "0",
                "is_used_for_promo_rules": "1",
                "is_visible_on_front": "0",
                "used_in_product_listing": "1",
                "is_visible": true,
                "scope": "global",
                "attribute_id": '.$attribute['id'].',
                "attribute_code": "'.$attribute['code'].'",
                "frontend_input": "select",
                "entity_type_id": null,
                "is_required": false,
                "options": ['.$options.'],
                "is_user_defined": true,
                "default_frontend_label": "'.$attribute['name'].'",
                "frontend_labels": null,
                "backend_type": "int",
                "source_model": null,
                "default_value": "'.$attribute['value'].'",
                "is_unique": "0",
                "validation_rules": [],
                "id": '.$attribute['id'].'
            }';
            
            $result = $this->qryES('POST', 'vue_storefront_catalog_attribute/_doc/'.$attribute['id'], $qry);
        }

        $output->writeln([
            '============',
            '',
            'Insert products into ES',
            '============',
            '',
        ]);

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
                $categoriesArr[] = '
                {
                    "category_id": '.$category['id'].',
                    "name": "'.$category['name'].'",
                    "slug": "'.$this->getUrlKey($category['slug']).'",
                    "path": "'.$category['slug'].'"
                }';
            }
            $qry = '
            {
                "id": '.$product['id'].',
                "sku" : "'.$product['sku'].'",
                "name" : "'.$product['name'].'",
                "price" : '.$product['price'].',
                "status": '.$enabled.',
                "visibility": '.$visibility.',
                "type_id" : "'.$type_id.'",
                "created_at": "'.$product['created_at'].'",
                "updated_at": "'.$product['updated_at'].'",
                "product_links": [],
                "tier_prices": [],
                "custom_attributes": null,
                "final_price": '.$product['price'].',
                "max_price": '.$product['price'].',
                "max_regular_price": '.$product['price'].',
                "minimal_regular_price": '.$product['price'].',
                "special_price": null,
                "minimal_price": '.$product['price'].',
                "regular_price": '.$product['price'].',
                "description": "'.trim(preg_replace('/\s+/', ' ', strip_tags($product['description']))).'",
                "image" : "/'.$product['image'].'",
                "small_image": "/'.$product['image'].'",
                "thumbnail": "/'.$product['image'].'",
                "category_ids" : ['.$product['category_ids'].'],
                "options_container" : "",
                "required_options": "0",
                "has_options": "'.$has_options.'",
                "url_key" : "'.$product['url_key'].'",
                "msrp_display_actual_price_type": "0",
                "tax_class_id": null,
                "material": [],
                "eco_collection": "0",
                "performance_fabric": "0",
                "new": "0",
                "sale": "0",
                "style_general": null,
                "pattern": null,
                "climate": [],
                "slug" : "'.$product['url_key'].'",
                "links": {},
                "stock": [
                  {
                    "item_id": '.$product['id'].',
                    "product_id": '.$product['id'].',
                    "stock_id": 1,
                    "qty": 0,
                    "is_in_stock": true,
                    "is_qty_decimal": false,
                    "show_default_notification_message": false,
                    "use_config_min_qty": true,
                    "min_qty": 0,
                    "use_config_min_sale_qty": 1,
                    "min_sale_qty": 1,
                    "use_config_max_sale_qty": true,
                    "max_sale_qty": 10000,
                    "use_config_backorders": true,
                    "backorders": 0,
                    "use_config_notify_stock_qty": true,
                    "notify_stock_qty": 1,
                    "use_config_qty_increments": true,
                    "qty_increments": 0,
                    "use_config_enable_qty_inc": true,
                    "enable_qty_increments": false,
                    "use_config_manage_stock": true,
                    "manage_stock": true,
                    "low_stock_date": null,
                    "is_decimal_divided": false,
                    "stock_status_changed_auto": 0
                  }
                ],
                "media_gallery" : ['.$media.'],'
                .$configurableChildren
                .$configurableOptions.'
                "url_path" : "products/'.$product['url_key'].'",
                "price_incl_tax": null,
                "special_price_incl_tax": null,
                "special_to_date": null,
                "special_from_date": null,
                "category" : ['.implode(",",$categoriesArr).']
            }';

            $result = $this->qryES('POST', 'vue_storefront_catalog_product/_doc/'.$product['id'], $qry);
        }
        
        return 0;
    }

    private function qryES($method, $endPoint, $qry=''){
        $ch = curl_init();
        $url = $this->esUrl.":".$this->esPort."/".$endPoint;

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT, $this->esPort);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $qry);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

        $result = curl_exec($ch);
        print_r($result);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpcode;
    }

    private function getCategories($product=false){
        $conn = $this->em->getConnection();
        if($product){
            $sql = 'SELECT c.id, t.name, t.slug
                FROM sylius_taxon c
                LEFT JOIN sylius_taxon_translation t ON c.id = t.translatable_id 
                LEFT JOIN sylius_product_taxon p ON c.id = p.taxon_id
                LEFT JOIN (SELECT GROUP_CONCAT(id) AS children, parent_id 
                            FROM sylius_taxon 
                            GROUP BY parent_id) ch ON c.id = ch.parent_id
                WHERE c.parent_id is not null AND t.locale = "en_US" AND p.product_id='.$product.'
                GROUP BY c.id';
        }else{
            $sql = 'SELECT c.id, c.parent_id, t.name, t.slug, c.tree_level+1 AS level, c.position+1 AS position, 
                count(p.id) AS product_count, ch.children, t.description, c.created_at, c.updated_at
                FROM sylius_taxon c
                LEFT JOIN sylius_taxon_translation t ON c.id = t.translatable_id 
                LEFT JOIN sylius_product_taxon p ON c.id = p.taxon_id
                LEFT JOIN (SELECT GROUP_CONCAT(id) AS children, parent_id 
                            FROM sylius_taxon 
                            GROUP BY parent_id) ch ON c.id = ch.parent_id
                WHERE c.parent_id is not null AND t.locale = "en_US"
                GROUP BY c.id';
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $categories = $stmt->fetchAll();

        return $categories;
    }

    private function getProducts(){
        $conn = $this->em->getConnection();
        $sql = 'SELECT p.id, t.name, i.path AS image, p.code AS sku, t.slug as url_key, 
                count(o.product_id) as configurable, price.price/100 AS price, pt.category_ids, p.enabled,
                p.created_at, p.updated_at, t.description
                FROM sylius_product p
                LEFT JOIN sylius_product_image i ON p.id = i.owner_id 
                LEFT JOIN sylius_product_translation t ON p.id = t.translatable_id
                LEFT JOIN sylius_product_options o ON p.id = o.product_id
                LEFT JOIN (SELECT product_id, price as price
                            FROM sylius_channel_pricing p
                            LEFT JOIN sylius_product_variant v ON p.product_variant_id = v.id
                            GROUP BY product_id) price ON p.id = price.product_id
                LEFT JOIN (SELECT product_id, GROUP_CONCAT(CONCAT(\'"\', taxon_id, \'"\')) AS category_ids
                            FROM sylius_product_taxon
                            GROUP BY product_id) pt ON p.id = pt.product_id
                WHERE t.locale = "en_US"
                GROUP BY p.id';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $products = $stmt->fetchAll();

        return $products;
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
            $media[] = '{
                "vid": null,
                "image": "/'.$result['path'].'",
                "pos": '.$key.',
                "typ": "image",
                "lab": ""
              }';
        }

        return implode(",", $media);
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
            $options[] = '{
                "label": "'.$result['value'].'",
                "value": "'.$result['id'].'"
              }';
        }

        return implode(",", $options);
    }

    private function getUrlKey($slug){
        $pos = strrpos($slug, '/');
        $key = $pos === false ? $slug : substr($slug, $pos + 1);

        return $key;
    }

    private function getPath($categories, $categoryID){
        $path =  $categoryID;
        foreach($categories as $category){
            if($category['id'] == $categoryID){
                $path = $this->getPath($categories, $category['parent_id']).'/'.$path;
            }
        }
        return $path;
    }

    private function getChildren($categories, $categoryID){
        $children = [];
        foreach($categories as $category){
            if($category['parent_id'] == $categoryID){
                $this->childrenCount ++;
                $children[] = '{
                    "id": '.$category['id'].',
                    "parent_id" : '.$category['parent_id'].',
                    "name" : "'.$category['name'].'",
                    "is_active" : true,
                    "position": '.$category['position'].',
                    "level" : '.$category['level'].',
                    "product_count" : '.$category['product_count'].',
                    "children_data": ['.$this->getChildren($categories, $category['id']).'],
                    "children" : "'.$category['children'].'",
                    "path" : "'.$this->getPath($categories, $category['id']).'",
                    "available_sort_by" : [],
                    "include_in_menu": true,
                    "description" : "'.$category['description'].'",
                    "meta_title" : "'.$category['name'].'",
                    "meta_description" : "'.$category['description'].'",
                    "display_mode": "PRODUCTS",
                    "is_anchor": "1",
                    "custom_layout_update": "<referenceContainer name=\"catalog.leftnav\" remove=\"true\"/>",
                    "children_count": "'.$this->childrenCount.'",
                    "url_key" : "'.$this->getUrlKey($category['slug']).'",
                    "url_path" : "'.$category['slug'].'",
                    "slug" : "'.$this->getUrlKey($category['slug']).'"
                  }';
            }
        }
        return implode(",",$children);
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
                    LEFT JOIN (SELECT product_id, GROUP_CONCAT(CONCAT(\'"\', taxon_id, \'"\')) AS category_ids
                                FROM sylius_product_taxon
                                GROUP BY product_id) pt ON pt.product_id = v.product_id
                    WHERE v.product_id = '.$productId;
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        foreach($results as $key => $result){
            $status = $result['status'] == 1 ? 1 : 2;
            $children[] = '{
                "image": "/'.$result['image'].'",
                "thumbnail": "/'.$result['image'].'",
                "small_image": "/'.$result['image'].'",
                "tax_class_id": null,
                "has_options": "0",
                "tier_prices": [],
                "url_key": "'.$result['url_key'].'",
                "required_options": "0",
                "msrp_display_actual_price_type": "0",
                "price": '.$result['price'].',
                "name": "'.$result['name'].'",
                "id": '.$result['id'].',
                "category_ids": ['.$result['category_ids'].'],
                "sku": "'.$result['sku'].'",'
                .$this->getOptionValues($result['id']).'
                "status": '.$status.'
              }';
        }

        if(count($children) > 0){
            return '"configurable_children": ['.implode(",", $children).'],';
        }else{
            return '';
        }
    }

    private function getConfigurableOptions($productId){
        $options = [];
        $values = [];
        $configurableOptions = [];
        $filterValuesGrouped = '';
        $conn = $this->em->getConnection();

        $sql = 'SELECT po.id AS attributeId, pot.name AS attributeLabel, po.position, po.code AS attributeCode, pv.product_id, pv.id, povt.value AS valueLabel, pov.id AS valueIndex
                FROM sylius_product_variant pv
                    INNER JOIN sylius_product_variant_option_value pvov ON pvov.variant_id = pv.id
                    INNER JOIN sylius_product_option_value pov ON pov.id = pvov.option_value_id
                    INNER JOIN sylius_product_option_value_translation povt ON povt.translatable_id = pov.id AND povt.locale = "en_US"
                    INNER JOIN sylius_product_option po ON po.id = pov.option_id
                    INNER JOIN sylius_product_option_translation pot ON pot.translatable_id = po.id AND pot.locale = "en_US"
                WHERE pv.product_id = '.$productId;
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll();

        foreach($results as $key => $result){
            $configurableOptions[$result['attributeId']]['product_id'] = $result['product_id'];
            $configurableOptions[$result['attributeId']]['id'] = $result['id'];
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
                $values[] = '{
                    "value_index": '.$valueIndex.',
                    "label": "'.$value['label'].'"
                }';
                $filterValues[] = $valueIndex;
            }
            $filterValuesGrouped .= '"'.$configurableOption['attribute_code'].'_options": ['.implode(",", $filterValues).'],';
            $options[] = '{
                "attribute_id": "'.$attributeId.'",
                "values": ['.implode(",",$values).'],
                "product_id": '.$configurableOption['product_id'].',
                "id": '.$configurableOption['id'].',
                "label": "'.$configurableOption['label'].'",
                "position": '.$configurableOption['position'].',
                "attribute_code": "'.$configurableOption['attribute_code'].'"
            }';
        }

        if(count($options) > 0){
            return '"configurable_options": ['.implode(",", $options).'],'.$filterValuesGrouped;
        }else{
            return '';
        }
        
    }

    private function getOptionValues($childId){
        $options = '';
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
            $options .= '"'.$result['code'].'": "'.$result['option_value_id'].'", ';
        }

        return $options;
    }
}