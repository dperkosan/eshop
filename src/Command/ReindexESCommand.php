<?php

// import attribute table
// import for different stores
// import for more product types then just configurable and simple

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Doctrine\ORM\EntityManagerInterface;

class ReindexESCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:reindex-es';
    private $esUrl = "localhost";
    private $esPort = "9200";
    private $em;
    private $childrenCount;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
        // the short description shown while running "php bin/console list"
        ->setDescription('Reindex ES.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to reindex ES');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Create indexes',
            '============',
            '',
        ]);

        // create taxrule index
        $result = $this->qryES('GET', 'vue_storefront_catalog_taxrule?pretty=true');
        if($result == 200){
            $output->writeln("index 'vue_storefront_catalog_taxrule'   already exists");
        }else{
            // TODO: Mapping JSON need to be in config, not here
            $qry = '{"mappings":{"properties":{"creation_time":{"format":"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis","type":"date"},"identifier":{"type":"keyword"},"code":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"customer_tax_class_ids":{"type":"long"},"tax_rate_ids":{"type":"long"},"rates":{"properties":{"code":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"tax_region_id":{"type":"long"},"rate":{"type":"long"},"id":{"type":"long"},"tax_country_id":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"tax_postcode":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}}}},"priority":{"type":"long"},"calculate_subtotal":{"type":"boolean"},"tsk":{"type":"long"},"update_time":{"format":"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis","type":"date"},"id":{"type":"integer"},"position":{"type":"long"},"product_tax_class_ids":{"type":"long"}}}}';
            $result = $this->qryES('PUT', 'vue_storefront_catalog_taxrule', $qry);
            if($result == 200){
                $output->writeln("index 'vue_storefront_catalog_taxrule'   created");
            }else{
                $output->writeln("index 'vue_storefront_catalog_taxrule'   NOT created");
            }
        }

        // create category index
        $result = $this->qryES('GET', 'vue_storefront_catalog_category?pretty=true');
        if($result == 200){
            $output->writeln("index 'vue_storefront_catalog_category'  already exists");
        }else{
            // TODO: Mapping JSON need to be in config, not here
            $qry = '{"mappings":{"properties":{"is_anchor":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"display_mode":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"custom_layout_update":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"children_count":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"product_count":{"type":"integer"},"created_at":{"format":"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis","type":"date"},"description":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"tsk":{"type":"long"},"path":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"updated_at":{"format":"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis","type":"date"},"children":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"page_layout":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"id":{"type":"long"},"include_in_menu":{"type":"boolean"},"slug":{"type":"keyword"},"url_path":{"type":"keyword"},"is_active":{"type":"boolean"},"meta_title":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"level":{"type":"long"},"custom_apply_to_products":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"url_key":{"type":"keyword"},"meta_description":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"parent_id":{"type":"integer"},"name":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"position":{"type":"integer"},"custom_use_parent_settings":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"children_data":{"properties":{"is_anchor":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"display_mode":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"custom_layout_update":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"children_count":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"product_count":{"type":"long"},"created_at":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"description":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"path":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"updated_at":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"children":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"page_layout":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"id":{"type":"long"},"include_in_menu":{"type":"boolean"},"slug":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"url_path":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"is_active":{"type":"boolean"},"meta_title":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"level":{"type":"long"},"custom_apply_to_products":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"url_key":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"meta_description":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"parent_id":{"type":"long"},"name":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"position":{"type":"long"},"custom_use_parent_settings":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"children_data":{"properties":{"is_anchor":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"display_mode":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"children_count":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"product_count":{"type":"long"},"created_at":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"description":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"path":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"updated_at":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"children":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"page_layout":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"id":{"type":"long"},"include_in_menu":{"type":"boolean"},"slug":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"url_path":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"is_active":{"type":"boolean"},"meta_title":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"level":{"type":"long"},"custom_apply_to_products":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"url_key":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"meta_description":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"parent_id":{"type":"long"},"name":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"position":{"type":"long"},"custom_use_parent_settings":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"children_data":{"properties":{"is_anchor":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"is_active":{"type":"boolean"},"display_mode":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"level":{"type":"long"},"children_count":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"product_count":{"type":"long"},"created_at":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"description":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"custom_apply_to_products":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"url_key":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"path":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"updated_at":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"children":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"parent_id":{"type":"long"},"name":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"id":{"type":"long"},"position":{"type":"long"},"custom_use_parent_settings":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"include_in_menu":{"type":"boolean"},"slug":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"url_path":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}}}}}}}}}}}';
            $result = $this->qryES('PUT', 'vue_storefront_catalog_category', $qry);
            if($result == 200){
                $output->writeln("index 'vue_storefront_catalog_category'  created");
            }else{
                $output->writeln("index 'vue_storefront_catalog_category'  NOT created");
            }
        }

        // create attribute index
        $result = $this->qryES('GET', 'vue_storefront_catalog_attribute?pretty=true');
        if($result == 200){
            $output->writeln("index 'vue_storefront_catalog_attribute' already exists");
        }else{
            // TODO: Mapping JSON need to be in config, not here
            $qry = '{"mappings":{"properties":{"backend_type":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"is_html_allowed_on_front":{"type":"boolean"},"is_visible_in_grid":{"type":"boolean"},"note":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"is_visible_on_front":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"is_wysiwyg_enabled":{"type":"boolean"},"is_unique":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"used_for_sort_by":{"type":"boolean"},"entity_type_id":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"frontend_input":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"is_user_defined":{"type":"boolean"},"tsk":{"type":"long"},"is_filterable_in_grid":{"type":"boolean"},"is_required":{"type":"boolean"},"is_used_for_promo_rules":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"scope":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"used_in_product_listing":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"options":{"properties":{"label":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"value":{"type":"text"}}},"is_filterable":{"type":"boolean"},"id":{"type":"integer"},"is_comparable":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"is_searchable":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"backend_model":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"is_visible":{"type":"boolean"},"is_visible_in_advanced_search":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"is_filterable_in_search":{"type":"boolean"},"default_value":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"is_used_in_grid":{"type":"boolean"},"default_frontend_label":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"attribute_id":{"type":"integer"},"frontend_class":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"apply_to":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"position":{"type":"long"},"source_model":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"attribute_code":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}}}}}';
            $result = $this->qryES('PUT', 'vue_storefront_catalog_attribute', $qry);
            if($result == 200){
                $output->writeln("index 'vue_storefront_catalog_attribute' created");
            }else{
                $output->writeln("index 'vue_storefront_catalog_attribute' NOT created");
            }
        }

        // create product index
        $result = $this->qryES('GET', 'vue_storefront_catalog_product?pretty=true');
        if($result == 200){
            $output->writeln("index 'vue_storefront_catalog_product'   already exists");
        }else{
            // TODO: Mapping JSON need to be in config, not here
            $qry = '{"mappings":{"properties":{"small_image":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"gift_message_available":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"configurable_options":{"properties":{"store_label":{"type":"text"},"frontend_label":{"type":"text"},"attribute_id":{"type":"integer"},"product_id":{"type":"long"},"values":{"properties":{"store_label":{"type":"text"},"frontend_label":{"type":"text"},"value_index":{"type":"keyword"},"default_label":{"type":"text"},"label":{"type":"text"}}},"default_label":{"type":"text"},"id":{"type":"long"},"label":{"type":"text"},"position":{"type":"long"},"attribute_code":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}}}},"price_view":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"erin_recommends":{"type":"integer"},"price":{"type":"float"},"links":{"properties":{"related":{"properties":{"pos":{"type":"long"},"sku":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}}}},"associated":{"properties":{"pos":{"type":"long"},"sku":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}}}}}},"special_from_date":{"format":"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis","type":"date"},"category_ids":{"type":"integer"},"id":{"type":"integer"},"category_gear":{"type":"integer"},"sku":{"type":"keyword"},"stock":{"properties":{"min_sale_qty":{"type":"long"},"qty_increments":{"type":"long"},"stock_status_changed_auto":{"type":"long"},"is_in_stock":{"type":"boolean"},"show_default_notification_message":{"type":"boolean"},"use_config_max_sale_qty":{"type":"boolean"},"product_id":{"type":"long"},"use_config_qty_increments":{"type":"boolean"},"notify_stock_qty":{"type":"long"},"manage_stock":{"type":"boolean"},"item_id":{"type":"long"},"min_qty":{"type":"long"},"use_config_min_qty":{"type":"boolean"},"use_config_notify_stock_qty":{"type":"boolean"},"stock_id":{"type":"long"},"use_config_backorders":{"type":"boolean"},"max_sale_qty":{"type":"long"},"backorders":{"type":"long"},"qty":{"type":"long"},"use_config_enable_qty_inc":{"type":"boolean"},"is_decimal_divided":{"type":"boolean"},"enable_qty_increments":{"type":"boolean"},"is_qty_decimal":{"type":"boolean"},"low_stock_date":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"use_config_manage_stock":{"type":"boolean"},"use_config_min_sale_qty":{"type":"long"}}},"slug":{"type":"keyword"},"strap_bags":{"type":"long"},"eco_collection_options":{"type":"integer"},"image":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"new":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"news_to_date":{"format":"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis","type":"date"},"thumbnail":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"visibility":{"type":"integer"},"format":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"weight":{"type":"integer"},"style_bottom":{"type":"long"},"links_purchased_separately":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"meta_description":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"required_options":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"size":{"type":"integer"},"minimal_price":{"type":"long"},"name":{"type":"text"},"configurable_children":{"properties":{"color":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"small_image":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"tier_prices":{"properties":{"qty":{"type":"long"},"extension_attributes":{"properties":{"website_id":{"type":"long"}}},"customer_group_id":{"type":"long"},"value":{"type":"long"}}},"regular_price":{"type":"long"},"msrp_display_actual_price_type":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"final_price":{"type":"long"},"price":{"type":"float"},"special_from_date":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"category_ids":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"id":{"type":"long"},"sku":{"type":"keyword"},"special_to_date":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"image":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"thumbnail":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"tax_class_id":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"has_options":{"type":"keyword"},"url_key":{"type":"keyword"},"max_price":{"type":"long"},"minimal_regular_price":{"type":"long"},"required_options":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"size":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"special_price":{"type":"float"},"minimal_price":{"type":"long"},"name":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"max_regular_price":{"type":"long"},"status":{"type":"long"}}},"price_type":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"max_regular_price":{"type":"long"},"status":{"type":"integer"},"short_description":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"activity":{"type":"long"},"color":{"type":"integer"},"gender":{"type":"integer"},"pattern":{"type":"text"},"created_at":{"format":"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis","type":"date"},"description":{"type":"text"},"eco_collection":{"type":"integer"},"tier_prices":{"properties":{"qty":{"type":"long"},"extension_attributes":{"properties":{"website_id":{"type":"long"}}},"customer_group_id":{"type":"long"},"value":{"type":"long"}}},"tsk":{"type":"long"},"size_options":{"type":"integer"},"regular_price":{"type":"long"},"msrp_display_actual_price_type":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"attribute_set_id":{"type":"long"},"updated_at":{"format":"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis","type":"date"},"final_price":{"type":"long"},"color_options":{"type":"integer"},"product_links":{"properties":{"link_type":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"linked_product_sku":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"linked_product_type":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"extension_attributes":{"properties":{"qty":{"type":"long"}}},"position":{"type":"long"},"sku":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}}}},"samples_title":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"options_container":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"special_to_date":{"format":"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis","type":"date"},"url_path":{"type":"keyword"},"news_from_date":{"format":"yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis","type":"date"},"cost":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"bundle_options":{"properties":{"option_id":{"type":"long"},"position":{"type":"long"},"product_links":{"properties":{"qty":{"type":"long"},"can_change_quantity":{"type":"long"},"option_id":{"type":"long"},"id":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"position":{"type":"long"},"is_default":{"type":"boolean"},"sku":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}}}},"sku":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"title":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"type":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"required":{"type":"boolean"}}},"sku_type":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"type_id":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"media_gallery":{"properties":{"vid":{"properties":{"meta":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"title":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"type":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"url":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"desc":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"video_id":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}}}},"image":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"pos":{"type":"long"},"typ":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"lab":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}}}},"style_bags":{"type":"long"},"tax_class_id":{"type":"integer"},"has_options":{"type":"keyword"},"climate":{"type":"long"},"features_bags":{"type":"long"},"style_general":{"type":"long"},"links_title":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"url_key":{"type":"keyword"},"performance_fabric":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"sale":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"max_price":{"type":"long"},"minimal_regular_price":{"type":"long"},"material":{"type":"integer"},"special_price":{"type":"float"},"custom_options":{"properties":{"image_size_x":{"type":"long"},"image_size_y":{"type":"long"},"product_sku":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"max_characters":{"type":"long"},"price":{"type":"long"},"values":{"properties":{"price":{"type":"long"},"option_type_id":{"type":"long"},"price_type":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"title":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"sort_order":{"type":"long"}}},"price_type":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"option_id":{"type":"long"},"is_require":{"type":"boolean"},"title":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"type":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"sort_order":{"type":"long"}}},"category":{"properties":{"path":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"category_id":{"type":"long"},"name":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"slug":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}}}},"shipment_type":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}},"weight_type":{"type":"text","fields":{"keyword":{"ignore_above":256,"type":"keyword"}}}}}}';
            $result = $this->qryES('PUT', 'vue_storefront_catalog_product', $qry);
            if($result == 200){
                $output->writeln("index 'vue_storefront_catalog_product'   created");
            }else{
                $output->writeln("index 'vue_storefront_catalog_product'   NOT created");
            }
        }

        $output->writeln([
            '============',
            '',
            'Insert categories into ES',
            '============',
            '',
        ]);

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
        foreach($products as $product){
            $type_id = $product['configurable'] > 0 ? "configurable" : "simple";
            $has_options = $product['configurable'] > 0 ? "1" : "0";
            $enabled = $product['enabled'] == 1 ? 1 : 2;
            $visibility = $product['enabled'] == 1 ? 4 : 1;
            $categories = $this->getCategories($product['id']);
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

                "url_path" : "products/'.$product['url_key'].'",
                "price_incl_tax": null,
                "special_price_incl_tax": null,
                "special_to_date": null,
                "special_from_date": null,
                "category" : ['.implode(",",$categoriesArr).'],
                "stock": [
                  {
                    "is_in_stock": true,
                    "qty": 10
                  }
                ]
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
        
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpcode;
    }

    private function getCategories($product=false){
        $conn = $this->em->getConnection();
        if($product){
            $sql = 'SELECT c.id, c.parent_id, t.name, t.slug, c.tree_level+1 AS level, c.position+1 AS position, count(p.id) AS product_count
                FROM sylius_taxon c
                LEFT JOIN sylius_taxon_translation t ON c.id = t.translatable_id 
                LEFT JOIN sylius_product_taxon p ON c.id = p.taxon_id
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
}