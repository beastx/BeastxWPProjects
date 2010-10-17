<?

class ProjectsShortCodes {

    function __construct($plugin) {
        $this->plugin = $plugin;
        $this->textDomain = $plugin->textDomain;
        $this->registerShortCodes();
    }
    
    function registerShortCodes() {
        add_shortcode('BWPProjects', array(&$this, 'categoriesShortCode'));
        add_shortcode('BWPProjectsCategory', array(&$this, 'listItemsInCategoriesShortCode'));
    }
    
    function categoriesShortCode($atts) {
        extract(shortcode_atts(array('id' => null), $atts));
        return $this->plugin->projectsPage->getCategoryList();
    }
    
    function listItemsInCategoriesShortCode($atts) {
        extract(shortcode_atts(array('id' => null), $atts));
        return $this->plugin->projectsPage->getCategoryItems($id);
    }
    
}

?>