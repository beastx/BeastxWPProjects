<?

class ProjectsPage {

    function __construct($plugin, $filterByCategoryId = null) {
        $this->plugin = $plugin;
        $this->textDomain = $plugin->textDomain;
        $this->filterByCategoryId = $filterByCategoryId;
    }
    
    function includeTemplateFile($file) {
        include ('templates/projectspage/' . $file);
    }
    
    function getView() {
        ob_start();
        include('templates/projectsPage.php');
        $template = ob_get_contents();
        ob_end_clean();
        return $template;
    }
    
    function getCategoryItems($id) {
        $this->filterByCategoryId = $id;
        return $this->getView();
    }
    
    function getCategoryList() {
        return $this->getView();
    }
    
    function printCategoriesList() {
        $pagesId = json_decode(get_option($this->plugin->pluginBaseName . '_relatedPages'), true);
        $returnValue = '';
        foreach ($pagesId['categories'] as $categorySlug => $pageId) {
            $page = get_post($pageId);
            //~ if ($categories[$i]['enabled']) {
                $returnValue .= '<h1><a href="' . get_permalink($page->ID) . '">' . $page->post_title . '</a></h1><br>';
                //~ $returnValue .= $this->printItemsInCategory($categories[$i]['id']);
            //~ }
        }
        return $returnValue;
    }
    
    function printCategoryPage() {
        ?><a href="/<? echo strtolower($this->plugin->getOptionValue('main', 'basePageName')); ?>s/">Volver</a><?
        return $this->printItemsInCategory($this->filterByCategoryId);
    }
    
    function printItemsInCategory($categoryId) {
        $posts = $this->getItemsInCategory($categoryId);
        $returnValue = '<ul>';
        for ($i = 0; $i < count($posts); ++$i) {
            $returnValue.= '<li><a href="'. get_permalink($posts[$i]->ID) .'" rel="bookmark" title="Permanent Link to ' . $posts[$i]->post_title . '">' . $posts[$i]->post_title . '</a></li>';
        }
        $returnValue.= '</ul>';
        return $returnValue;
    }
    
    function getItemsInCategory($categoryId) {
        wp_reset_query();
        $posts = get_posts(array(
            'offset' => 0,
            'posts_per_page' => -1,
            'post_type' => $this->plugin->postType,
            'meta_key' => 'project_category',
            'meta_value' => $categoryId
        ));
        return $posts;
    }
    
    public function addListProjectPages($pageName = null) { 
        global $user_ID;
        $pageIds = array();
        if (empty($pageName)) {
            $pageName = $this->plugin->getOptionValue('main', 'basePageName') . 's';
        }
        $categories = $this->plugin->getOptionValue('categories', 'categories');
        $pageIds['main'] = wp_insert_post(array(
            'post_title' => $pageName,
            'post_content' => '[BWPProjects]',
            'post_status' => 'publish', 
            'post_type' => 'page',
            'ping_status' => get_option('default_ping_status'), 
            'post_parent' => 0,
            'post_author'  => $user_ID
        )); 
        $pageIds['categories'] = array();
        for ($i = 0; $i < count($categories); ++$i) {
            if ($categories[$i]['enabled']) {
                $pageIds['categories'][$categories[$i]['id']] = wp_insert_post(array(
                    'post_title' => $categories[$i]['categoryName'],
                    'post_content' => '[BWPProjectsCategory id=' . $categories[$i]['id'] . ']',
                    'post_status' => 'publish', 
                    'post_type' => 'page',
                    'ping_status' => get_option('default_ping_status'), 
                    'post_parent' => $pageIds['main'],
                    'post_author'  => $user_ID
                )); 
            }
        }
        update_option($this->plugin->pluginBaseName . '_relatedPages', json_encode($pageIds));
    }
    
    public function removeListProjectPages() { 
        $pagesId = json_decode(get_option($this->plugin->pluginBaseName . '_relatedPages'), true);
        wp_delete_post($pagesId['main'], true);
        foreach ($pagesId['categories'] as $categorySlug => $postId) {
            wp_delete_post($postId, true);
        }
    }
    
    public function renameMainPage($newName) { 
        $this->removeListProjectPages();
        $this->addListProjectPages($newName . 's');
    }
    
}

?>