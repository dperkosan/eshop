<?php

namespace App\Command\Datapump;

use App\Command\Datapump\Es;

class CategoryIndex
{
    private $em;
    private $mapping;
    private $totalChildrenCount;
    private $categoryChildrenCount;

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

    public function importData()
    {
        $categories = $this->getCategories();
        
        foreach($categories as $category){
            // is_anchor ?
            // display_mode ?
            // custom_layout_update ?
            $this->totalChildrenCount = 0;
            $children = $this->getChildren($categories, $category['id']);
            $qry = [
                "id" => (int)$category['id'],
                "parent_id" => (int)$category['parent_id'],
                "name" => $category['name'],
                "is_active" => true,
                "position" => (int)$category['position'],
                "level" => (int)$category['level'],
                "product_count" => (int)$category['product_count'],
                "children_data" => $children,
                "children" => $category['children'],
                "created_at" => $category['created_at'],
                "updated_at" => $category['updated_at'],
                "path" => $this->getPath($categories, $category['id']),
                "available_sort_by" => [],
                "include_in_menu" => true,
                "description" => $category['description'],
                "meta_title" => $category['name'],
                "meta_description" => $category['description'],
                "display_mode" => "PRODUCTS",
                "is_anchor" => "1",
                "custom_layout_update" => "",
                "children_count" => $this->totalChildrenCount,
                "url_key" => $this->getUrlKey($category['slug']),
                "url_path" => $category['slug'],
                "slug" => $this->getUrlKey($category['slug'])
            ];
            
            $result = Es::qryES('POST', 'vue_storefront_catalog_category/_doc/'.$category['id'], json_encode($qry));
        }
    }

    private function getCategories(){
        $conn = $this->em->getConnection();
        
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
        
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $categories = $stmt->fetchAll();

        return $categories;
    }

    private function getChildren($categories, $categoryID){
        $children = [];

        foreach($categories as $category){
            if($category['parent_id'] == $categoryID){
                $this->totalChildrenCount ++;
                $children[] = [
                    "id" => (int)$category['id'],
                    "parent_id" => (int)$category['parent_id'],
                    "name" => $category['name'],
                    "is_active" => true,
                    "position" => (int)$category['position'],
                    "level" => (int)$category['level'],
                    "product_count" => (int)$category['product_count'],
                    "children_data" => $this->getChildren($categories, $category['id']),
                    "children" => $category['children'],
                    "path" => $this->getPath($categories, $category['id']),
                    "available_sort_by" => [],
                    "include_in_menu" => true,
                    "description" => $category['description'],
                    "meta_title" => $category['name'],
                    "meta_description" => $category['description'],
                    "display_mode" => "PRODUCTS",
                    "is_anchor" => "1",
                    "custom_layout_update" => "",
                    "children_count" => 0,
                    "url_key" => $this->getUrlKey($category['slug']),
                    "url_path" => $category['slug'],
                    "slug" => $this->getUrlKey($category['slug'])
                ];
            }
        }

        foreach($children as &$child){
            if(isset($child['children_data'])){
                $child['children_count'] = count($child['children_data']);
                if(isset($this->categoryChildrenCount[$child['id']])){
                    $child['children_count'] += $this->categoryChildrenCount[$child['id']];
                }
                if(isset($this->categoryChildrenCount[$child['parent_id']])){
                    $this->categoryChildrenCount[$child['parent_id']] += $child['children_count'];
                }else{
                    $this->categoryChildrenCount[$child['parent_id']] = $child['children_count'];
                }
                
            }
        }
        unset($child);

        return $children;
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

    private function getUrlKey($slug){
        $pos = strrpos($slug, '/');
        $key = $pos === false ? $slug : substr($slug, $pos + 1);

        return $key;
    }
}