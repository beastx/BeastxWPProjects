<?
ini_set('display_errors', false);
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-includes/wp-db.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-admin/includes/image.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-admin/includes/file.php');
require_once '../libs/FirePHP.class.php';

function debug($var, $title = null) {
    $firephp = FirePHP::getInstance(true);
    $firephp->log($var, $title);
}
echo saveUpload();

ini_set('display_errors', true);

function saveUpload() {
    $isImage = ($_GET['type'] == 'image') ? true : false;
    $uploads = wp_upload_dir();
    if (is_writable($uploads['path']) && current_user_can('upload_files')) {
        $uploadfile=true;
    }
    $upload=$_FILES[$_GET['id'] . 'UserFile'];
    debug($upload);
    if (!empty($upload['tmp_name'])) {
        $file = $isImage ? handle_image_upload($upload) : handle_upload($upload);
        if ($file) {
            if ($isImage) {
                createTumb($file);
            }
            $file_url=$file['url'];
            $attachment = array(
                'post_mime_type' => $file['type'],
                'guid' => $file_url,
                'post_title' => $isImage ? 'WPProject Screenshot' : 'WPProject Attachment'
            );
            $attachmentId = wp_insert_attachment($attachment, $file['file']);
            if (!is_wp_error($attachmentId) ) {
                wp_update_attachment_metadata( $attachmentId, wp_generate_attachment_metadata( $attachmentId, $file['file'] ) );
                return json_encode(array(
                    'id' => $attachmentId,
                    'fileName' => $upload['name'],
                    'fileType' => $upload['type'],
                    'fileSize' => $upload['size'],
                    'url' => $isImage ? wp_get_attachment_thumb_url($attachmentId) : $file_url
                ));
            }
            return json_encode(array('error' => true, 'errorMsg' => 'wpInsertAttachmentError'));
        } else {
            return json_encode(array('error' => true, 'errorMsg' => 'handleUploadError'));
        }
    } else {
        return json_encode(array('error' => true, 'errorMsg' => 'filesIsEmptyOrTooLong'));
    }
}

function handle_image_upload($upload) {
    if (file_is_displayable_image( $upload['tmp_name'] )) {
        $overrides = array('test_form' => false); 
        $file=wp_handle_upload($upload, $overrides);
    }
    return $file;
}

function handle_upload($upload) {
    $overrides = array('test_form' => false); 
    $file=wp_handle_upload($upload, $overrides);
    return $file;
}

function createTumb($file) {
    $size='medium';
    $resized = image_make_intermediate_size( $file['file'], get_option("{$size}_size_w"), get_option("{$size}_size_h"), get_option("{$size}_crop") );
}