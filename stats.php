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

?>