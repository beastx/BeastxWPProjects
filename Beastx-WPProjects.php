<?php
/*
Plugin Name: Beastx Wordpress Projects
Plugin URI: http://www.beastxblog.com/projects/wordpress-plugins/Beastx-WPProjects/
Description: Adding projects post type functionality.
Version: 1.0
Author: Beastx
Author URI: http://www.beastxblog.com/
Text Domain: BeastxWPProjects
*/

/*
    Copyright 2010 Beastx (Leandro Asrilevich) (http://beastxblog.com/)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

require_once 'BeastxWordpressTools/includeBeastxWordpressTools.php';
include 'ProjectsShortCodes.php';
include 'ProjectsPage.php';

load_plugin_textdomain('BeastxWPProjects', null, basename(dirname(__FILE__)) . '/languages/');

$wpdb->show_errors = true;

if (!class_exists('BeastxWPProjects')) {

Class BeastxWPProjects extends BeastxPlugin {

    public $pluginName = 'Beastx WP Projects';
    public $pluginVersion = '1.0';
    public $pluginUrl = 'http://www.beastxblog.com/playground/wordpress-plugins/playground/';
    public $pluginAuthor = 'Beastx';
    public $pluginAuthorUrl = 'http://www.beastxblog.com/';
    public $textDomain = 'BeastxWPProjects';

    public function __construct() {
    
        $this->folders = array(
            'uploads' => '/projects/uploads',
            'templates' => '/projects/templates'
        );
        $this->actionsLinks = array( //  si se declara la propiedad actionsLinks automaticamente se agregaran los links contenidos en el array como actions lnks del plugin en la pagina de plugins
            array('url' => '/wp-admin/admin.php?page=Beastx-WP-Projects-optionsPage', 'label' => __('Settings', $this->textDomain))
        );
        $this->metaLinks = array( //  si se declara la propiedad metaLinks automaticamente se agregaran los links contenidos en el array como meta lnks del plugin en la pagina de plugins
            array('url' => 'http://www.beastxblog.com/project/BeastxWPProjects/#Comments', 'label' => __('Support', $this->textDomain)),
            array('url' => 'http://www.beastxblog.com/project/BeastxWPProjects/#FAQ', 'label' => __('FAQ', $this->textDomain)),
            array('url' => 'http://www.beastxblog.com/project/BeastxWPProjects/#Contribute', 'label' => __('Contribute', $this->textDomain)),
            array('url' => 'http://www.twitter.com/beastxblog', 'label' => __('Follow the author on Twitter', $this->textDomain)),
            array('url' => 'http://www.facebook.com/#!/leandro.asrilevich', 'label' => __('Follow the  author on Facebook', $this->textDomain))
        );
        $this->adminMenuOptions = array( //  si se declara la propiedad adminMenuOptions se usa para generar automaticamente un nuevo bloque de configuracion en la pagina de admin del wordpress con sus subpaginas
            'title' => 'Beastx WP Projects',
            'subOptions' => array(
                array('link' => __('Options', $this->textDomain), 'title' => __('Options', $this->textDomain), 'id' => 'optionsPage'),
                array('link' => __('Stats', $this->textDomain), 'title' => __('Stats', $this->textDomain), 'id' => 'statsPage'),
                array('link' => __('Help', $this->textDomain), 'title' => __('Help', $this->textDomain), 'id' => 'helpPage')
            )
        );
        $this->pluginBaseFileName = plugin_basename(__FILE__);
        $this->pluginBaseUrl = WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__));
        $this->pluginBasePath = WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__));
        $this->assetsPath = WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)) . '/assets/';
        
        parent::__construct();
        
        $this->registerOptionHelper(
            'categories',
            'categoriesOptionsSetter',
            'categoriesOptionsGetter',
            'categoriesOptionsDefault'
        );
        
        $this->registerOptionHelper(
            'licences',
            'licencesOptionsSetter',
            'licencesOptionsGetter',
            'licencesOptionsDefault'
        );
        
        $this->projectsShortCodes = new ProjectsShortCodes($this);
        $this->projectsPage = new ProjectsPage($this);
        
        //~ $this->uninstall();
    }
    
    public function getOptionsPage() {
        include 'optionsPage.php';
        $content = new BeastxProjectOptionsPage($this);
        $content->display();
    }
    
    public function getStatsPage() {
        include 'statsPage.php';
        $content = new BeastxProjectStatsPage($this);
        $content->display();
    }
    
    public function getHelpPage() { 
        include 'helpPage.php';
        $content = new BeastxProjectHelpPage($this);
        $content->display();
    }
    
    public function onWordpressInit()  {
        $this->postType = strtolower($this->getOptionValue('main', 'basePageName'));
        require 'postTypeArgs.php';
        register_post_type($this->postType, $BeastxWPProjectsPostTypeArgs);
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
            
        $this->addFilter('manage_edit-' . $this->postType . '_columns', 'customPostColumns');
        $this->addAction('manage_posts_custom_column', 'custonPostRowValues');
        
        $this->projectsShortCodes->registerShortCodes();
                
        $this->addAction('save_post', 'saveProjectPost');
        
        
        $this->addFilter('the_content', 'filterPostContent');
        $this->addFilter('pre_get_posts', 'filterFrontEndQuery' );
        
    }

    public function onAdminInit()  {
        add_meta_box('projects-title-meta', __('Project Info', $this->textDomain), array(&$this, 'getTitleMetaBoxContent'), $this->postType, 'normal', 'high');
        add_meta_box('projects-description-meta', __('Project Description', $this->textDomain), array(&$this, 'getDescriptionMetaBoxContent'), $this->postType, 'normal', 'high');
        
        add_meta_box('projects-attachments-meta', __('Attachments', $this->textDomain), array(&$this, "getAttachmentsMetaBoxContent"), $this->postType, "normal");
        add_meta_box('projects-screenshots-meta', __('Screenshots', $this->textDomain), array(&$this, "getScreenshotsMetaBoxContent"), $this->postType, "normal");
        add_meta_box('projects-faq-meta', __('FAQs', $this->textDomain), array(&$this, "getFaqsMetaBoxContent"), $this->postType, "normal");
        add_meta_box('projects-installInstructions-meta', __('Install Instructions', $this->textDomain), array(&$this, "getInstallInstructionsMetaBoxContent"), $this->postType, "normal");
        add_meta_box('projects-changeLog-meta', __('Change Log', $this->textDomain), array(&$this, "getChangeLogMetaBoxContent"), $this->postType, "normal");
        add_meta_box('projects-contributors-meta', __('Contributors', $this->textDomain), array(&$this, "getContributorsMetaBoxContent"), $this->postType, "normal");
        
        add_meta_box('projects-options-meta', __('Misc options', $this->textDomain), array(&$this, "getMiscOptionsMetaBoxContent"), $this->postType, "side");
        add_meta_box('projects-categories-meta', __('Categories', $this->textDomain), array(&$this, "getCategoriesMetaBoxContent"), $this->postType, "side");
        add_meta_box('projects-otherNotes-meta', __('Other notes', $this->textDomain), array(&$this, "getOtherNotesMetaBoxContent"), $this->postType, "side");
        add_meta_box('projects-stats-meta', __('Stats', $this->textDomain), array(&$this, "getStatsMetaBoxContent"), $this->postType, "side");
    }
    
    public function onAfterPluginActivate() {
        $this->projectsPage->addListProjectPages();
    }
    
    public function onPluginDeactivate() {
        $this->projectsPage->removeListProjectPages();
    }

    public function addScripts() {
        echo "<script type=\"text/javascript\">
            BeastxWPProjectTexts = {
                'save': '" . __('Save', $this->textDomain) . "',
                'done': '" . __('Done', $this->textDomain) . "',
                'edit': '" . __('Edit', $this->textDomain) . "',
                'remove': '" . __('Remove', $this->textDomain) . "',
                'copy': '" . __('Copy', $this->textDomain) . "',
                'name': '" . __('Name', $this->textDomain) . "',
                'url': '" . __('Url', $this->textDomain) . "',
                'version': '" . __('Version', $this->textDomain) . "',
                'changes': '" . __('Changes', $this->textDomain) . "',
                'step': '" . __('Step', $this->textDomain) . "',
                'question': '" . __('Question', $this->textDomain) . "',
                'answer': '" . __('Answer', $this->textDomain) . "',
                'selectFile': '" . __('Select File', $this->textDomain) . "',
                'fileMaxLength': '" . __('File max length', $this->textDomain) . "',
                'areYouSure': '" . __('Are you sure?', $this->textDomain) . "',
                'setTitle': '" . __('Set Title', $this->textDomain) . "',
                'setTitleLong': '" . __('Set Image Title', $this->textDomain) . "',
                'editImage': '" . __('Edit Image', $this->textDomain) . "',
                'editTitle': '" . __('Edit Title', $this->textDomain) . "',
                'onlyImageFiles': '" . __('Upload Error: Only images files are supported.(jpg, gif, png)', $this->textDomain) . "',
                'onlyZipFiles': '" . __('Upload Error: Only compress files are supported. (zip, rar)', $this->textDomain) . "',
                'wpInsertAttachmentError': '" . __('Upload Error: Insert Attachment Error', $this->textDomain) . "',
                'handleUploadError': '" . __('Upload Error: Handle Upload Error', $this->textDomain) . "',
                'filesIsEmptyOrTooLong': '" . __('Upload Error: Files is empty or too long', $this->textDomain) . "'
            };
        </script>";
        wp_enqueue_script('jquery');
        wp_enqueue_script('beastx', $this->assetsPath . 'scripts/Beastx.js');
        wp_enqueue_script('beastxPluginRowEditor', $this->assetsPath . 'scripts/RowEditor.js');
        wp_enqueue_script('beastxPluginEditPost', $this->assetsPath . 'scripts/EditPost.js');
        wp_enqueue_script('beastxPluginAjaxUploader', $this->assetsPath . 'scripts/ajaxupload.js');
        wp_enqueue_script('beastxPluginFileAttacher', $this->assetsPath . 'scripts/FileAttacher.js');
    }
    
    public function addStyles() {
        wp_enqueue_style('dashboard');
        wp_enqueue_style('global');
        wp_enqueue_style( 'beastxPluginAdminPage', $this->assetsPath . 'styles/adminpage.css');
    }

    function filterPostContent($content) {
        global $wp;
        global $post;
        if (!empty($wp->query_vars['post_type']) && $wp->query_vars['post_type'] == $this->postType) {
            include 'ProjectItem.php';
            $projectItem = new ProjectItem($this, $post, $content);
            return $projectItem->getView();
        } else {
            return $content;
        }
    }
     
    function filterFrontEndQuery( $query ) {
        if ($this->getOptionValue('main', 'showAsNormalPostInFrontEnd')) {
            if (is_home() && false == $query->query_vars['suppress_filters']) {
                $query->set('post_type', array( 'post', $this->postType, 'quote', 'attachment' ));
            }
        }
        return $query;
    }
    
    function customPostColumns($columns) {
        unset($columns['comments']);
        unset($columns['author']);
        $columns["project_version"] = __('Version', $this->textDomain);
        $columns["project_category"] = __('Category', $this->textDomain);
        return $columns;
    }

    function custonPostRowValues($column) {
        global $post;
        $custom = get_post_custom();
        switch ($column) {
            case "project_descripcion":
                echo the_excerpt();
                break;
            case "project_category":
                $categories = $this->getOptionValue('categories', 'categories');
                for ($i = 0; $i < count($categories); ++$i) {
                    if ($custom["project_category"][0] == $categories[$i]['id']) {
                        echo $categories[$i]['categoryName'];
                    }
                }
                break;
            case "project_version":
                echo $custom["project_version"][0];
                break;
            case "project_otherNotes":
                echo $custom["project_otherNotes"][0];
                break;
        }
    }

    function saveProjectPost($id){
        global $post;
        
        //~ if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        //~ return $post_id;
    //~ }


        if (wp_is_post_revision($id) || wp_is_post_autosave($id)) {
            return;
        }
        update_post_meta($post->ID, "project_attachments", $_POST["project_attachments"]);
        update_post_meta($post->ID, "project_screenshots", $_POST["project_screenshots"]);
        update_post_meta($post->ID, "project_faqs", $_POST["project_faqs"]);
        update_post_meta($post->ID, "project_installInstructions", $_POST["project_installInstructions"]);
        update_post_meta($post->ID, "project_changeLog", $_POST["project_changeLog"]);
        update_post_meta($post->ID, "project_otherNotes", $_POST["project_otherNotes"]);
        update_post_meta($post->ID, "project_version", $_POST["project_version"]);
        update_post_meta($post->ID, "project_category", $_POST["project_category"]);
        update_post_meta($post->ID, "project_licence", $_POST["project_licence"]);
        update_post_meta($post->ID, "project_status", $_POST["project_status"]);
        update_post_meta($post->ID, "project_contributors", $_POST["project_contributors"]);
        update_post_meta($post->ID, "project_viewDemoLink", $_POST["project_viewDemoLink"]);
        update_post_meta($post->ID, "project_donateLink", $_POST["project_donateLink"]);
        update_post_meta($post->ID, "project_supportLink", $_POST["project_supportLink"]);
    }
    
    function getTitleMetaBoxContent() {
        global $post;
        $custom = get_post_custom($post->ID);
        $version = empty($custom["project_version"][0]) ? '' : $custom["project_version"][0];
        echo "<table width=\"100%\">
            <tbody>
                <tr>
                    <td class=\"ProjectTitleLeftTD\"><label for=\"title\">" . __('Project Name', $this->textDomain) . "</label></td>
                    <td class=\"ProjectTitleRightTD\"><label for=\"ProjectVersionInput\">" . __('Project Version', $this->textDomain) . "</label></td>
                </tr>
                <tr>
                    <td class=\"ProjectTitleLeftTD\" id=\"ProjectTitleContainer\"></td>
                    <td class=\"ProjectTitleRightTD\" id=\"ProjectVersionContainer\"><input id=\"ProjectVersionInput\" name=\"project_version\" value=\"" . $version . "\" /></td>
                </tr>
            </tbody>
        </table>";
        echo "<script>jQuery('#titlediv').prependTo('#projects-title-meta #ProjectTitleContainer');</script>";
    }
    
    function getDescriptionMetaBoxContent() {
        echo "<script>jQuery('#postdiv, #postdivrich').prependTo('#projects-description-meta .inside');</script>";
    }
    
    function getMiscOptionsMetaBoxContent() {
        global $post;
        $custom = get_post_custom($post->ID);
        $licences = $this->getOptionValue('licences', 'licences');
        $status = array(
            array('id' => 1, 'name' => __('Development', $this->textDomain)),
            array('id' => 2, 'name' => __('Beta', $this->textDomain)),
            array('id' => 3, 'name' => __('Stable', $this->textDomain)),
            array('id' => 4, 'name' => __('Discontinued', $this->textDomain))
        );
        echo '<label for="project_viewDemoLinkInput">' . __('View demo link', $this->textDomain) . ':</label>&nbsp;';
        echo '<br>';
        echo '<input type="text" name="project_viewDemoLink" id="project_viewDemoLinkInput" value="' . $custom["project_viewDemoLink"][0] . '" />';
        echo '<br><br>';
        
        echo '<label for="project_donateLinkInput">' . __('Donate link', $this->textDomain) . ':</label>&nbsp;';
        echo '<br>';
        echo '<input type="text" name="project_donateLink" id="project_donateLinkInput" value="' . $custom["project_donateLink"][0] . '" />';
        echo '<br><br>';
        
        echo '<label for="project_supportLinkInput">' . __('Support link', $this->textDomain) . ':</label>&nbsp;';
        echo '<br>';
        echo '<input type="text" name="project_supportLink" id="project_supportLinkInput" value="' . $custom["project_supportLink"][0] . '" />';
        echo '<br><br>';
        
        echo '<label for="project_statusInput">' . __('Project Status', $this->textDomain) . ':</label>&nbsp;';
        echo '<br>';
        echo '<select name="project_status" id="project_statusInput">';
        echo '<option value="0">' . __('Select', $this->textDomain) . '</option>';
        for ($i = 0; $i < count($status); ++$i) {
            echo '<option ' . ($custom["project_status"][0] == $status[$i]['id'] ? 'selected="selected"' : '') . ' value="' . $status[$i]['id'] . '">' . $status[$i]['name'] . '</option>' . "\n";
        }
        echo '</select>';
        echo '<br><br>';
        
        echo '<label for="project_licenceInput">' . __('Project Licence', $this->textDomain) . ':</label>&nbsp;';
        echo '<br>';
        echo '<select name="project_licence" id="project_licenceInput">';
        echo '<option value="0">' . __('Select', $this->textDomain) . '</option>';
        for ($i = 0; $i < count($licences); ++$i) {
            echo '<option ' . ($custom["project_licence"][0] == $licences[$i]['id'] ? 'selected="selected"' : '') . ' value="' . $licences[$i]['id'] . '">' . $licences[$i]['licenceName'] . '</option>' . "\n";
        }
        echo '</select>';
    }
    
    function getContributorsMetaBoxContent() {
        global $post;
        $custom = get_post_custom($post->ID);
        $items = empty($custom["project_contributors"][0]) ? array() : json_decode(stripcslashes($custom["project_contributors"][0]), true);
        echo "<div id=\"contributorsContainer\"></div>
        <input id=\"addContributorsButton\" class=\"button rowEditorButton\" type=\"button\" value=\"" . __('Add new contributor', $this->textDomain) . "\" />
        <script>
        jQuery(document).ready(function() {
        
        var contributorsRowEditor = New(BeastxRowEditor, [
            'project_contributors',
            jQuery('#post')[0],
            jQuery('#contributorsContainer')[0],
            jQuery('#addContributorsButton')[0],
            BeastxContributorsItem 
        ]);\n";

        for ($i = 0; $i < count($items); ++$i) {
            echo "contributorsRowEditor.addRow(New(BeastxContributorsItem, [ '" . $items[$i]['name'] . "', '" . $items[$i]['url'] . "' ]));\n";
        }
        echo '});</script>';
    }
    
    function getCategoriesMetaBoxContent() {
        global $post;
        $custom = get_post_custom($post->ID);
        $categories = $this->getOptionValue('categories', 'categories');
        echo '<div id="categories-all">';
        echo '<ul class="list:category categorychecklist form-no-clear" id="categorychecklist">' . "\n";
        for ($i = 0; $i < count($categories); ++$i) {
            if ($categories[$i]['enabled']) {
                echo '<li id="category-' . $categories[$i]['categorySlug'] . '">' . "\n";
                echo '<label class="selectit">' . "\n";
                echo '<input ' . ($custom["project_category"][0] == $categories[$i]['id'] ? 'checked="checked"' : '') . ' type="radio" id="in-category-' . $categories[$i]['categorySlug'] . '" name="project_category" value="' . $categories[$i]['id'] . '">' ."\n";
                echo $categories[$i]['categoryName'];
                echo '</label></li> ';
            }
        }
        echo '</ul>';
        echo '</div>';
    }
    
    function getAttachmentsMetaBoxContent() {
        global $post;
        $custom = get_post_custom($post->ID);
        $attachments = empty($custom["project_attachments"][0]) ? array() : json_decode($custom["project_attachments"][0], true);
        $maxAttachments = 1; //$this->getOptionValue('items', 'maxAttachments');
        echo '<input type="hidden" size="20" value="" name="project_attachments" id="attachments_input" />'. "\n";
        echo '<div id="attachmentsContainer"></div>'. "\n";
        echo '<div id="attachmentsFileAttacherContainer"></div>'. "\n";
        echo '<script>'. "\n";
        echo 'jQuery(document).ready(function() {';
        echo 'var attachmentsManager = New(BeastxAttachmentsManager, [ ' . $maxAttachments . ' ]);'. "\n";
        for ($i = 0; $i < count($attachments); ++$i) {
            if (!empty($attachments[$i]['id'])) {
                echo 'attachmentsManager.addItem(\'' . $attachments[$i]['id'] . '\', \'' . $attachments[$i]['name'] . '\', \'' . $attachments[$i]['url'] . '\', \'' . $attachments[$i]['type'] . '\', \'' . $attachments[$i]['size'] . '\');' . "\n";
            }
        }
        echo '})</script>';
    }
    
    function getScreenshotsMetaBoxContent() {
        global $post;
        $custom = get_post_custom($post->ID);
        $screenshots = empty($custom["project_screenshots"][0]) ? array() : json_decode($custom["project_screenshots"][0], true);
        $maxScreenshots = $this->getOptionValue('items', 'maxScreenshots');
        echo '<input type="hidden" size="20" value="" name="project_screenshots" id="screenshots_input" />'. "\n";
        echo '<div id="screenshotsContainer"></div>'. "\n";
        echo '<div id="screenshotsFileAttacherContainer"></div>'. "\n";
        echo '<script>'. "\n";
        echo 'jQuery(document).ready(function() {';
        echo 'var screenshotsManager = New(BeastxScreenshotsManager, [ ' . $maxScreenshots . ' ]);'. "\n";
        for ($i = 0; $i < count($screenshots); ++$i) {
            if (!empty($screenshots[$i]['id'])) {
                $screenshot_url = wp_get_attachment_thumb_url($screenshots[$i]['id'], 'thumbnail');
            } else {
                $screenshot_url = null;
            }
            echo 'screenshotsManager.addScreenshotItem(\'' . $screenshots[$i]['id'] . '\', \'' . $screenshots[$i]['title'] . '\', \'' . $screenshot_url . '\');' . "\n";
        }
        echo '})</script>';
    }
    
    function  getFaqsMetaBoxContent() {
        global $post;
        $custom = get_post_custom($post->ID);
        $items = empty($custom["project_faqs"][0]) ? array() : json_decode($custom["project_faqs"][0], true);
        echo "<div id=\"faqsContainer\"></div>
        <input id=\"addFaqButton\" class=\"button rowEditorButton\" type=\"button\" value=\"" . __('Add new FAQ', $this->textDomain) . "\" />
        <script>
        jQuery(document).ready(function() {
        var faqsRowEditor = New(BeastxRowEditor, [
            'project_faqs',
            jQuery('#post')[0],
            jQuery('#faqsContainer')[0],
            jQuery('#addFaqButton')[0],
            BeastxFaqItem 
        ]);\n";

        for ($i = 0; $i < count($items); ++$i) {
            echo "faqsRowEditor.addRow(New(BeastxFaqItem, [ '" . addslashes($items[$i]['question']) . "', '" . addslashes($items[$i]['answer']) . "' ]));\n";
        }
        echo '});</script>';
    }
    
    function  getInstallInstructionsMetaBoxContent() {
        global $post;
        $custom = get_post_custom($post->ID);
        $instructions = empty($custom["project_installInstructions"][0]) ? '' : $custom["project_installInstructions"][0];
        echo '<textarea class="BeastxWPProjectEditor" id="project_installInstructions" tabindex="2" name="project_installInstructions" cols="20" rows="20">';
        echo $instructions;
        echo '</textarea>';
    }
    
    function  getChangeLogMetaBoxContent() {
        global $post;
        $custom = get_post_custom($post->ID);
            //~ debug($custom["project_changeLog"][0]);
        //~ debug(json_decode(stripcslashes($custom["project_changeLog"][0]), true));
        //~ debug(json_decode($custom["project_changeLog"][0], true));

        $items = empty($custom["project_changeLog"][0]) ? array() : json_decode($custom["project_changeLog"][0], true);
        echo "<div id=\"changeLogContainer\"></div>
        <input id=\"addChangeLogButton\" class=\"button rowEditorButton\" type=\"button\" value=\"" . __('Add new Change Log', $this->textDomain) . "\" />
        <script>
        jQuery(document).ready(function() {
        var changeLogRowEditor = New(BeastxRowEditor, [
            'project_changeLog',
            jQuery('#post')[0],
            jQuery('#changeLogContainer')[0],
            jQuery('#addChangeLogButton')[0],
            BeastxChangeLogItem 
        ]);". "\n";

        for ($i = 0; $i < count($items); ++$i) {
            echo "changeLogRowEditor.addRow(New(BeastxChangeLogItem, [ '" . addslashes($items[$i]['version']) . "', '" . addslashes($items[$i]['changes']) . "' ]));\n";
        }
        echo '});</script>';
    }
    
    function  getOtherNotesMetaBoxContent() {
        global $post;
        $custom = get_post_custom($post->ID);
        $otherNotes = empty($custom["project_otherNotes"][0]) ? '' : $custom["project_otherNotes"][0];
        echo '<textarea class="BeastxWPProjectEditorSimple" id="project_otherNotes" tabindex="2" name="project_otherNotes" cols="20" rows="20">';
        echo $otherNotes;
        echo '</textarea>';
    }
    
    function  getStatsMetaBoxContent() {
        global $post;
        echo 'Falta hacer';
    }
    
    public function categoriesOptionsSetter($categories) {
        global $wpdb;
        $tableName = $wpdb->prefix . str_replace('-', '', $this->pluginBaseName) . "_categories";
        $oldCategories = $this->getOptionValue('categories', 'categories');
        $idsForDelete = array();
        for ($i = 0; $i < count($oldCategories); ++$i) {
            $delete = true;
            for ($j = 0; $j < count($categories); ++$j) {
                if (!empty($categories[$j]['id'])) {
                    if ($oldCategories[$i]['id'] == $categories[$j]['id']) {
                        $delete = false;
                    }
                } else {
                    $delete = false;
                }
            }
            if ($delete) {
                array_push($idsForDelete, $oldCategories[$i]['id']);
            }
        }
        for ($i = 0; $i < count($idsForDelete); ++$i) {
            $wpdb->query("DELETE FROM " . $tableName . " WHERE id =" . $idsForDelete[$i]);
        }
        for ($i = 0; $i < count($categories); ++$i) {
            if (empty($categories[$i]['id'])) {
                $wpdb->insert(
                    $tableName,
                    $categories[$i]
                );
            } else {
                $wpdb->update(
                    $tableName,
                    $categories[$i],
                    array('id'=> $categories[$i]['id'])
                );
            }
        }
    }
    
    public function categoriesOptionsGetter() {
        global $wpdb;
        $tableName = $wpdb->prefix . str_replace('-', '', $this->pluginBaseName) . "_categories";
        $sql = "SELECT * FROM " . $tableName . " ORDER BY id";
        return $wpdb->get_results($sql, ARRAY_A);
    }
    
    public function categoriesOptionsDefault() {
        return array(
            array('categorySlug' =>  'wordpress-plugins', 'categoryName' => 'Wordpress Plugins', 'enabled' => 1),
            array('categorySlug' =>  'wordpress-themes', 'categoryName' => 'Wordpress Themes', 'enabled' => 1),
            array('categorySlug' =>  'greasemonkey-script', 'categoryName' => 'Grease Monkey Scripts', 'enabled' => 1),
            array('categorySlug' =>  'misc', 'categoryName' => 'Misc', 'enabled' => 1)
        );
    }
    
    
    
    
    
    public function licencesOptionsSetter($licences) {
        global $wpdb;
        $tableName = $wpdb->prefix . str_replace('-', '', $this->pluginBaseName) . "_licences";
        $wpdb->query("DELETE FROM " . $tableName);
        for ($i = 0; $i < count($licences); ++$i) {
            $wpdb->insert(
                $tableName,
                $licences[$i]
            );
        }
    }
    
    public function licencesOptionsGetter() {
        global $wpdb;
        $tableName = $wpdb->prefix . str_replace('-', '', $this->pluginBaseName) . "_licences";
        $sql = "SELECT * FROM " . $tableName;
        return $wpdb->get_results($sql, ARRAY_A);
    }
    
    public function licencesOptionsDefault() {
        return array(
            array('licenceName' =>  'GNU GPL', 'licenceUrl' => 'http://www.gnu.org/licenses/gpl.html', 'enabled' => 1),
            array('licenceName' =>  'GNU LGPL', 'licenceUrl' => 'http://www.gnu.org/copyleft/lesser.es.html', 'enabled' => 1),
            array('licenceName' =>  'CDDL', 'licenceUrl' => 'http://www.opensolaris.org/os/licensing/cddllicense.txt', 'enabled' => 1),
            array('licenceName' =>  'MPL', 'licenceUrl' => 'http://www.mozilla.org/MPL/MPL-1.1.html', 'enabled' => 1),
            array('licenceName' =>  'Ms-PL', 'licenceUrl' => 'http://www.microsoft.com/spain/sharedsource/licensingbasics/publiclicense.mspx', 'enabled' => 1),
            array('licenceName' =>  'MIT', 'licenceUrl' => 'http://ocw.mit.edu/OcwWeb/web/terms/terms/index.htm', 'enabled' => 1),
            array('licenceName' =>  'BSD', 'licenceUrl' => 'http://www.proint.info/wiki/index.php?title=Licencia_BSD#Licencia', 'enabled' => 1),
            array('licenceName' =>  'Apache 2.0', 'licenceUrl' => 'http://www.apache.org/licenses/LICENSE-2.0', 'enabled' => 1),
            array('licenceName' =>  'QPL', 'licenceUrl' => 'http://www.opensource.org/licenses/qtpl.php', 'enabled' => 1),
            array('licenceName' =>  'SPL', 'licenceUrl' => 'http://www.opensource.org/licenses/simpl-2.0.html', 'enabled' => 1),
            array('licenceName' =>  'CC BSD', 'licenceUrl' => 'http://creativecommons.org/licenses/BSD/', 'enabled' => 1),
            array('licenceName' =>  'CC LGPL', 'licenceUrl' => 'http://creativecommons.org/choose/cc-lgpl', 'enabled' => 1),
            array('licenceName' =>  'CC GPL', 'licenceUrl' => 'http://creativecommons.org/choose/cc-gpl', 'enabled' => 1),
            array('licenceName' =>  'CC Attribution 3', 'licenceUrl' => 'http://creativecommons.org/licenses/by/3.0/', 'enabled' => 1)
        );
    }
    
    public function uninstall() {
        $this->deleteSqlTables();
        delete_option($this->pluginBaseName . '_options');
        delete_option($this->pluginBaseName . '_options');
        delete_option($this->pluginBaseName . '_relatedPages');
    }
    
}

new BeastxWPProjects();

}
?>