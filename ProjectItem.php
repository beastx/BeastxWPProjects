<?

class ProjectItem {

    public $post;
    public $postId;
    public $title;
    public $description;
    public $screenshots;
    public $faqs;
    public $changeLog;
    public $installInstructions;
    public $otherNotes;
    public $version;
    public $category;
    public $tags;
    public $author;
    
    function __construct($plugin, $post, $description) {
        $this->plugin = $plugin;
        $this->textDomain = $plugin->textDomain;
        $this->post = $post;
        $this->postId = $post->ID;
        $custom = get_post_custom($this->postId);
        
        $this->title = $post->post_title;
        $this->description = $description;
        $this->screenshots = $this->parseScreenshots($custom['project_screenshots'][0]);
        $this->faqs = $this->parseFaqs($custom['project_faqs'][0]);
        $this->changeLog = $this->parseChangeLogs($custom['project_changeLog'][0]);
        $this->installInstructions = $this->parseInstallInstructions($custom['project_installInstructions'][0]);
        $this->otherNotes = $this->parseOtherNotes($custom['project_otherNotes'][0]);
        $this->version = $custom['project_version'][0];
        $this->category = $this->parseCategories($custom['project_category'][0]);
        $this->tags = $this->parseTags(get_the_terms($this->postId, 'post_tag'));
        $this->author = $this->parseAuthor($post->post_author);
        
        $this->licence = $this->parseLicence($custom['project_licence'][0]);
        $this->lastUpdate = $this->parseLastUpdate($post->post_modified);
        $this->status = $this->parseStatus($custom['project_status'][0]);
        $this->contributors = $this->parseContributors($custom['project_contributors'][0]);
        
        $this->viewDemoLink = $this->parseViewDemoLink($custom['project_viewDemoLink'][0]);
        $this->supportLink = $this->parseSupportLink($custom['project_supportLink'][0]);
        $this->donateLink = $this->parseDonateLink($custom['project_donateLink'][0]);
        $this->downloadLink = $this->parseDonwloadLink($custom['project_attachments'][0]);
    }
    
    function includeTemplateFile($file) {
        include ('templates/projectItem/' . $file);
    }
    
    function getView() {
        ob_start();
        include('templates/projectItem.php');
        $template = ob_get_contents();
        ob_end_clean();
        $this->registerViewStat();
        return $template;
    }
    
    function registerViewStat() {
        echo "<script>
            jQuery.ajax({
                url: '" . $this->plugin->pluginBaseUrl . "/stats.php?itemId=" . $this->post->ID . "&download=0',
                success: function(data) {}
            });
        </script>";
    }
    
    function parseScreenshots($data) {
        $images = empty($data) ? array() : json_decode($data, true);
        $imgs = array(
            'full' =>array(),
            'tumbs' =>array()
        );
        for ($i = 0; $i < count($images); ++$i) {
            $imgData = wp_get_attachment_image_src($images[$i]['id'], array(480, 360));
            array_push($imgData, $images[$i]['title']);
            array_push(
                $imgs['full'], 
                $imgData
            );
            $imgData = wp_get_attachment_image_src($images[$i]['id'], array(24, 24));
            array_push($imgData, $images[$i]['title']);
            array_push(
                $imgs['tumbs'],
                $imgData
            );
        }
        return $imgs;
    }
    
    function parseLicence($data) {
        $licences = $this->plugin->getOptionValue('licences', 'licences');
        for ($i = 0; $i < count($licences); ++$i) {
            if ($licences[$i]['id'] == $data) {
                return $licences[$i];
            }
        }
        return null;
    }
    
    function parseLastUpdate($data) {
        list($date, $time) = explode(' ', $data);
        list($year, $month, $day) = explode('-', $date);
        return date('j M Y', mktime(0, 0, 0, $month, $day, $year));
    }
    
    function parseStatus($data) {
        $status = array(
            array('id' => 1, 'name' => __('Development', $this->textDomain)),
            array('id' => 2, 'name' => __('Beta', $this->textDomain)),
            array('id' => 3, 'name' => __('Stable', $this->textDomain)),
            array('id' => 4, 'name' => __('Discontinued', $this->textDomain))
        );
        for ($i = 0; $i < count($status); ++$i) {
            if ($status[$i]['id'] == $data) {
                return $status[$i]['name'];
            }
        }
        return null;
    }
    
    function parseContributors($data) {
        $contributors = json_decode($data, true);
        for ($i = 0; $i < count($contributors); ++$i) {
            $contributors[$i]['url'] = preg_match('/http:\/\//', $contributors[$i]['url']) ? $contributors[$i]['url'] : 'http://' . $contributors[$i]['url'];
        }
        return $contributors;
    }
    
    function parseViewDemoLink($data) {
        if (!empty($data)) {
            return preg_match('/http:\/\//', $data) ? $data : 'http://' . $data;
        } else {
            return null;
        }
    }
    
    function parseSupportLink($data) {
        if (!empty($data)) {
            return preg_match('/http:\/\//', $data) ? $data : 'http://' . $data;
        } else {
            return null;
        }
    }
    
    function parseDonateLink($data) {
        if (!empty($data)) {
            return preg_match('/http:\/\//', $data) ? $data : 'http://' . $data;
        } else {
            return null;
        }
    }
    
    function parseDonwloadLink($data) {
        $attachments = json_decode($data, true);
        if (!empty($attachments)) {
            $attachment = $attachments[0];
            return $attachment['url'];
        } else {
            return null;
        }
    }
    
    
    
    function parseFaqs($data) {
        return json_decode($data, true);
    }
    
    function parseChangeLogs($data) {
        $log = json_decode($data, true);
        function _sort($a, $b) {
            if ($a['version'] == $b['version']) {
                return 0;
            }
            return ($a['version'] < $b['version']) ? 1 : -1;
        }
        usort($log, "_sort");
        return $log;
    }
    
    function parseInstallInstructions($data) {
        return nl2br($data);
    }
    
    function parseOtherNotes($data) {
        return nl2br($data);
    }
    
    function parseCategories($data) {
        $categories = $this->plugin->getOptionValue('categories', 'categories');
        for ($i = 0; $i < count($categories); ++$i) {
            if ($categories[$i]['id'] == $data) {
                return $categories[$i];
            }
        }
        return null;
    }
    
    function parseTags($data) {
        return $data;
    }
    
    function parseAuthor($data) {
        return $data;
    }
}

?>