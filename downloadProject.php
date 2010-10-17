<?
ini_set('display_errors', false);
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-includes/wp-db.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-admin/includes/image.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-admin/includes/file.php');

class BeastxProjectStats {
    
    function __construct($itemId, $isDownload = 0) {
        global $wpdb;
        $this->itemId = $itemId;
        $this->isDownload = $isDownload;
        $this->tableName = $wpdb->prefix . "BeastxWPProjects_stats";
    }
    
    function getStatsData() {
        return array(
            'itemId' => $this->itemId,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'userAgent' => $_SERVER['HTTP_USER_AGENT'],
            'referer' => $_SERVER['HTTP_REFERER'],
            'date' => date('Y-m-d H:i:s'),
            'isDownloadStat' => $this->isDownload
        );
    }
    
    function count() {
        global $wpdb;
        $wpdb->insert(
            $this->tableName,
            $this->getStatsData()
        );
    }
}

$stat = new BeastxProjectStats($_REQUEST['itemId'], $_REQUEST['download']);
$stat->count();

echo json_encode(array('error' => false));














 add_action('init', 'flush_rewrite_rules');
function flush_rewrite_rules() {
    global $wp_rewrite;
    $wp_rewrite->flush_rules();
}

add_action('generate_rewrite_rules', 'add_rewrite_rules');
function add_rewrite_rules($wp_rewrite) {
    $new_rules = array(
        'image/(.+)' => 'index.php?image=' .
        $wp_rewrite->preg_index(1)
    );
    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}

add_filter('query_vars', 'add_query_vars');
function add_query_vars($qvars){
    $qvars[] = 'image';
    return $qvars;
}

add_action('template_redirect', array(&$this, 'template_redirect_intercept'));
function template_redirect_intercept(){
    global $wp_query;
    if ($wp_query->get('image')) {
        if (file_exists(TEMPLATEPATH . '/images.php')) {
            include(TEMPLATEPATH . '/images.php');
            exit;
        }
    }
}

$wp_query->get(‘image’) will return false if the second part of the URL, the image name, hasn’t been entered or the image name if it has. You would use this return value in your custom page to figure out what it is you need to display there.

?>