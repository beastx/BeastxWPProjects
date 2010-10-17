<?

class BeastxProjectOptionsPage extends BeastxAdminPage {
    
    function __construct($plugin) {
        $this->pageTitle = __('Configurations', $this->textDomain);
        $this->iconClass = 'icon-options-general';
        parent::__construct($plugin);
    }
    
    function display() {
        $this->printTemplate('optionsPage');
    }
    
    function updatePostType($newPostType) {
        //~ function set_post_type( $post_id = 0, $post_type = 'post' ) {
        $oldPostType = $this->plugin->getOptionValue('main', 'basePageName');
        if ($oldPostType != $newPostType) {
            global $wpdb;
            $wpdb->update(
                $wpdb->prefix . "posts",
                array('post_type' => strtolower($newPostType)),
                array('post_type' => $oldPostType)
            );
        }
        $this->plugin->projectsPage->renameMainPage($newPostType);
    }
    
    function saveFormAction($post) {
        $pluginOptions = $this->plugin->getOptions();
        foreach ($pluginOptions as $sectionId => $sectionOptions) {
            foreach ($sectionOptions['options'] as $optionId => $options) {
                switch ($options['type']) {
                    case 'checkbox':
                        $pluginOptions[$sectionId]['options'][$optionId]['value'] = empty($post['option_' . $sectionId . '_' . $optionId]) ? 0 : 1;
                        break;
                    case 'text':
                        $pluginOptions[$sectionId]['options'][$optionId]['value'] = $post['option_' . $sectionId . '_' . $optionId];
                        break;
                    case 'rowEditor':
                        $pluginOptions[$sectionId]['options'][$optionId]['value'] = json_decode(stripslashes($post['option_' . $sectionId . '_' . $optionId]), true);
                        break;
                    default:
                        $pluginOptions[$sectionId]['options'][$optionId]['value'] = $post['option_' . $sectionId . '_' . $optionId];
                        break;
                }
                // TODO: use validators!
                if ($sectionId == 'main' && $optionId == 'basePageName') {
                    $this->updatePostType($post['option_' . $sectionId . '_' . $optionId]);
                }
            }
        }
        $this->plugin->saveOptions($pluginOptions);
        $this->saveMsg = __('Options has been saved', $this->textDomain);
        header('location:/wp-admin/admin.php?page=Beastx-WPProjects/Beastx-WPProjects.php');
    }
    
    function resetFormAction() {
        $this->plugin->registerDefaultOptions();
        $this->saveMsg = __('Options has been reseted', $this->textDomain);
    }
    
    function getInputsFromOptions($setcion, $options) {
        $inputs = array();
        foreach($options['options'] as $optionId => $option) {
            array_push(
                $inputs,
                $this->makeBoxRow(
                    $option['label'],
                    $option['description'],
                    array(
                        'type' => $option['type'],
                        'name' => 'option_' . $setcion . '_' . $optionId,
                        'value' => $option['value']
                    )
                )
            );
        }
        return $inputs;
    }
    
    function getMainBox() {
        $options = $this->plugin->getOption('main');
        $this->makeBox(
            'main', 
            $options['label'],
            $options['description'],
            $this->getInputsFromOptions('main', $options),
            false
        );
    }
    
    function getCategoriesBox() {
        $categories = $this->plugin->getOptionValue('categories', 'categories');
        $content = array(
            '<div id="categoriesContainer"></div>',
            '<input id="addCategoryButton" class="button rowEditorButton" type="button" value="' . __('Add new category', $this->textDomain) . '" />',
            '<script>',
            "jQuery(document).ready(function() {",
            "var categoriesEditor = New(BeastxRowEditor, [
                'option_categories_categories',
                jQuery('#myForm')[0],
                jQuery('#categoriesContainer')[0],
                jQuery('#addCategoryButton')[0],
                BeastxCategoryRowEditor 
            ]);"
        );
        for ($i = 0; $i < count($categories); ++$i) {
            array_push($content, "categoriesEditor.addRow(New(BeastxCategoryRowEditor, [ '" . $categories[$i]['id'] . "', '" . $categories[$i]['categorySlug'] . "', '" . $categories[$i]['categoryName'] . "', " . $categories[$i]['enabled'] . " ]));");
        }
        array_push($content, '});</script>');
        
        $this->makeBox(
            'categories', 
            __('Download Categories', $this->textDomain),
            null,
            $content,
            true
        );
    }
    
    function getLicencessBox() {
        $licences = $this->plugin->getOptionValue('licences', 'licences');
        $content = array(
            '<div id="licencesContainer"></div>',
            '<input id="addLicenceButton" class="button rowEditorButton" type="button" value="' . __('Add new licence', $this->textDomain) . '" />',
            '<script>',
            "jQuery(document).ready(function() {",
            "var licencessEditor = New(BeastxRowEditor, [
                'option_licences_licences',
                jQuery('#myForm')[0],
                jQuery('#licencesContainer')[0],
                jQuery('#addLicenceButton')[0],
                BeastxLicenceRowEditor 
            ]);"
        );
        for ($i = 0; $i < count($licences); ++$i) {
            array_push($content, "licencessEditor.addRow(New(BeastxLicenceRowEditor, [ '" . $licences[$i]['id'] . "', '" . $licences[$i]['licenceName'] . "', '" . $licences[$i]['licenceUrl'] . "', " . $licences[$i]['enabled'] . " ]));");
        }
        array_push($content, '});</script>');
        
        $this->makeBox(
            'licences', 
            __('Project Licences', $this->textDomain),
            null,
            $content,
            true
        );
    }
    
    function getFoldersBox() {
        $options = $this->plugin->getOption('folders');
        $this->makeBox(
            'folders', 
            $options['label'],
            $options['description'],
            $this->getInputsFromOptions('folders', $options),
            false
        );
    }
    
    function getStatsBox() {
        $options = $this->plugin->getOption('stats');
        $this->makeBox(
            'stats', 
            $options['label'],
            $options['description'],
            $this->getInputsFromOptions('stats', $options),
            false
        );
    }
    
    function getItemsBox() {
        $options = $this->plugin->getOption('items');
        $this->makeBox(
            'items', 
            $options['label'],
            $options['description'],
            $this->getInputsFromOptions('items', $options),
            false
        );
    }
}

?>