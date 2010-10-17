<?

class BeastxProjectStatsPage extends BeastxAdminPage {
    
    function __construct($plugin) {
        global $wpdb;
        $this->pageTitle = __('Stats');
        $this->iconClass = 'icon-post';
        $this->tableName = $wpdb->prefix . str_replace('-', '', $plugin->pluginBaseName) . "_stats";
        parent::__construct($plugin);
    }
    
    function display() {
        $this->printHeader();
        $this->printTemplate('statsPage');
    }
}

?>