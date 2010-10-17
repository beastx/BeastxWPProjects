<?

class BeastxProjectHelpPage extends BeastxAdminPage {
    
    function __construct($plugin) {
        $this->pageTitle = __('Help');
        $this->iconClass = 'icon-post';
        parent::__construct($plugin);
    }
    
    function display() {
        $this->printHeader();
        $this->printTemplate('helpPage');
    }
}

?>