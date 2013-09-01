<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 勃
 * Date: 13-5-26
 * Time: 下午3:38
 * To change this template use File | Settings | File Templates.
 */
class JQueryFileUpload extends CComponent
{
    public $upload_dir, $upload_url, $script_url, $mkdir_mode, $delete_url;
    protected $options;
    // PHP File Upload error message codes:
    // http://php.net/manual/en/features.file-upload.errors.php
    protected $error_messages = array(
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk',
        8 => 'A PHP extension stopped the file upload',
        'post_max_size' => 'The uploaded file exceeds the post_max_size directive in php.ini',
        'max_file_size' => 'File is too big',
        'min_file_size' => 'File is too small',
        'accept_file_types' => 'Filetype not allowed',
        'max_number_of_files' => 'Maximum number of files exceeded',
        'max_width' => 'Image exceeds maximum width',
        'min_width' => 'Image requires a minimum width',
        'max_height' => 'Image exceeds maximum height',
        'min_height' => 'Image requires a minimum height'
    );

    function __construct($options = null, $initialize = true)
    {
        $this->options = array(
            'script_url' => $this->get_full_url() . '/fileUpload/index',
            'delete_url' => $this->get_full_url() . '/fileUpload/delete',
            'upload_dir' => dirname(Yii::app()->basePath) . '/upload/',
            'upload_url' => $this->get_full_url() . '/upload/',
            'user_dirs' => false,
            'mkdir_mode' => 0755,
            'param_name' => 'files',
            // Set the following option to 'POST', if your server does not support
            // DELETE requests. This is a parameter sent to the client:
            'delete_type' => 'DELETE',
            'access_control_allow_origin' => '*',
            'access_control_allow_credentials' => false,
            'access_control_allow_methods' => array(
                'OPTIONS',
                'HEAD',
                'GET',
                'POST',
                'PUT',
                'PATCH',
                'DELETE'
            ),
            'access_control_allow_headers' => array(
                'Content-Type',
                'Content-Range',
                'Content-Disposition'
            ),
            // Enable to provide file downloads via GET requests to the PHP script:
            'download_via_php' => false,
            // Defines which files can be displayed inline when downloaded:
            'inline_file_types' => '/\.(gif|jpe?g|png)$/i',
            // Defines which files (based on their names) are accepted for upload:
            'accept_file_types' => '/.+$/i',
            // The php.ini settings upload_max_filesize and post_max_size
            // take precedence over the following max_file_size setting:
            'max_file_size' => null,
            'min_file_size' => 1,
            // The maximum number of files for the upload directory:
            'max_number_of_files' => null,
            // Image resolution restrictions:
            'max_width' => null,
            'max_height' => null,
            'min_width' => 1,
            'min_height' => 1,
            // Set the following option to false to enable resumable uploads:
            'discard_aborted_uploads' => true,
            // Set to true to rotate images based on EXIF meta data, if available:
            'orient_image' => false,
            'image_versions' => array(
                // Uncomment the following version to restrict the size of
                // uploaded images:
                'origin' => array(
                    'max_width' => 1920,
                    'max_height' => 1200,
                    'jpeg_quality' => 95
                ),
                // Uncomment the following to create medium sized images:
                'medium' => array(
                    'max_width' => 800,
                    'max_height' => 600,
                    'jpeg_quality' => 80
                ),
                'thumbnail' => array(
                    'max_width' => 120,
                    'max_height' => 120
                )
            )
        );
        if ($options) {
            $this->options = array_merge($this->options, $options);
        }
    }

    public function init()
    {
        if (empty($this->upload_url) || empty($this->upload_dir) || empty($this->upload_url)
            || empty($this->mkdir_mode) || empty($this->delete_url)
        ) {
            throw new Exception("you have to set params for JQueryFileUpload extension in main.php");
        }
        $this->options['upload_url'] = $this->upload_url;
        $this->options['script_url'] = $this->script_url;
        $this->options['mkdir_mode'] = $this->mkdir_mode;

        $this->options['delete_url'] = $this->delete_url;
        $this->options['upload_dir'] = $this->upload_dir;
    }

    public function initialize()
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'OPTIONS':
            case 'HEAD':
                $this->head();
                break;
            case 'GET':
                $this->get();
                break;
            case 'PATCH':
            case 'PUT':
            case 'POST':
                $this->post();
                break;
            case 'DELETE':
                $this->delete();
                break;
            default:
                $this->header('HTTP/1.1 405 Method Not Allowed');
        }
    }

    protected function get_full_url()
    {
        $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        return
            ($https ? 'https://' : 'http://') .
            (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] . '@' : '') .
            (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] .
            ($https && $_SERVER['SERVER_PORT'] === 443 ||
            $_SERVER['SERVER_PORT'] === 80 ? '' : ':' . $_SERVER['SERVER_PORT']))) .
            substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
    }

    protected function get_user_id()
    {
        return Yii::app()->user->id;
    }

    protected function get_user_path()
    {
        if ($this->options['user_dirs']) {
            return $this->get_user_id() . '/';
        }
        return '';
    }

    protected function get_upload_path($file_name = null, $version = null)
    {
        $file_name = $file_name ? $file_name : '';
        $version_path = empty($version) ? '' : $version . '/';
        return $this->options['upload_dir'] . $this->get_user_path()
            . $version_path . $file_name;
    }

    protected function get_download_url($file_name, $version = null, $sub_dir = null)
    {
        if ($this->options['download_via_php']) {
            $url = $this->options['script_url'] . '?file=' . rawurlencode($file_name);
            if ($version) {
                $url .= '&version=' . rawurlencode($version);
            }
            return $url . '&download=1';
        }
        $version_path = empty($version) ? '' : rawurlencode($version) . '/';
        return $this->options['upload_url'] . $this->get_user_path()
            . $version_path . $sub_dir . rawurlencode($file_name);
    }

    protected function set_file_delete_properties($file)
    {
        $file->delete_url = $this->options['delete_url']
            . '?id=' . rawurlencode($file->id);
        $file->delete_type = $this->options['delete_type'];
        if ($file->delete_type !== 'DELETE') {
            $file->delete_url .= '&_method=DELETE';
        }
        if ($this->options['access_control_allow_credentials']) {
            $file->delete_with_credentials = true;
        }
    }

    // Fix for overflowing signed 32 bit integers,
    // works for sizes up to 2^32-1 bytes (4 GiB - 1):
    protected function fix_integer_overflow($size)
    {
        if ($size < 0) {
            $size += 2.0 * (PHP_INT_MAX + 1);
        }
        return $size;
    }

    protected function get_file_size($file_path, $clear_stat_cache = false)
    {
        if ($clear_stat_cache) {
            clearstatcache(true, $file_path);
        }
        return $this->fix_integer_overflow(filesize($file_path));

    }

    protected function is_valid_file_object($file_name)
    {
        $file_path = $this->get_upload_path($file_name);
        if (is_file($file_path) && $file_name[0] !== '.') {
            return true;
        }
        return false;
    }

    protected function get_file_object($file_name)
    {
        if ($this->is_valid_file_object($file_name)) {
            $file = new stdClass();
            $file->name = $file_name;
            $file->size = $this->get_file_size(
                $this->get_upload_path($file_name)
            );
            $file->url = $this->get_download_url($file->name);
            foreach ($this->options['image_versions'] as $version => $options) {
                if (!empty($version)) {
                    if (is_file($this->get_upload_path($file_name, $version))) {
                        $file->{$version . '_url'} = $this->get_download_url(
                            $file->name,
                            $version
                        );
                    }
                }
            }
            $this->set_file_delete_properties($file);
            return $file;
        }
        return null;
    }

    protected function get_file_objects($iteration_method = 'get_file_object')
    {
        $upload_dir = $this->get_upload_path();
        if (!is_dir($upload_dir)) {
            return array();
        }

        return array_values(array_filter(array_map(
            array($this, $iteration_method),
            scandir($upload_dir)
        )));
    }

    protected function count_file_objects()
    {
        return count($this->get_file_objects('is_valid_file_object'));
    }

    /**
     * 创建缩放图片。如果现有图片比缩放目标的最大限制小，则做单纯的拷贝操作，不会放大图片
     * @param $file_name
     * @param $version
     * @param $options
     * @return bool
     */
    protected function create_scaled_image($file_name, $version, $options)
    {
        //获取需要缩放的图片的路径
        $file_path = $this->get_upload_path($file_name);
        //获取缩放后图片的路径
        if (!empty($version)) {
            $version_dir = $this->get_upload_path(null, $version);
            if (!is_dir($version_dir)) {
                mkdir($version_dir, $this->options['mkdir_mode'], true);
            }
            $new_file_path = $version_dir . '/' . $file_name;
        } else {
            $new_file_path = $file_path;
        }
        //获取原图片大小
        list($img_width, $img_height) = @getimagesize($file_path);
        if (!$img_width || !$img_height) {
            return false;
        }
        //缩放比例
        $scale = min(
            $options['max_width'] / $img_width,
            $options['max_height'] / $img_height
        );
        //如果目前图片大小比最大长宽小，则不变换大小，只拷贝图片
        if ($scale >= 1) {
            if ($file_path !== $new_file_path) {
                return copy($file_path, $new_file_path);
            }
            return true;
        }
        //如果需要变换大小，就用gd库变换大小
        $new_width = $img_width * $scale;
        $new_height = $img_height * $scale;
        $new_img = @imagecreatetruecolor($new_width, $new_height);
        switch (strtolower(substr(strrchr($file_name, '.'), 1))) {
            case 'jpg':
            case 'jpeg':
                $src_img = @imagecreatefromjpeg($file_path);
                $write_image = 'imagejpeg';
                $image_quality = isset($options['jpeg_quality']) ?
                    $options['jpeg_quality'] : 75;
                break;
            case 'gif':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                $src_img = @imagecreatefromgif($file_path);
                $write_image = 'imagegif';
                $image_quality = null;
                break;
            case 'png':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                @imagealphablending($new_img, false);
                @imagesavealpha($new_img, true);
                $src_img = @imagecreatefrompng($file_path);
                $write_image = 'imagepng';
                $image_quality = isset($options['png_quality']) ?
                    $options['png_quality'] : 9;
                break;
            default:
                $src_img = null;
        }
        $success = $src_img && @imagecopyresampled(
            $new_img,
            $src_img,
            0, 0, 0, 0,
            $new_width,
            $new_height,
            $img_width,
            $img_height
        ) && $write_image($new_img, $new_file_path, $image_quality);
        // Free up memory (imagedestroy does not delete files):
        @imagedestroy($src_img);
        @imagedestroy($new_img);
        return $success;
    }

    protected function get_error_message($error)
    {
        return array_key_exists($error, $this->error_messages) ?
            $this->error_messages[$error] : $error;
    }

    function get_config_bytes($val)
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $this->fix_integer_overflow($val);
    }

    protected function validate($uploaded_file, $file, $error, $index)
    {
        if ($error) {
            $file->error = $this->get_error_message($error);
            return false;
        }
        $content_length = $this->fix_integer_overflow(intval($_SERVER['CONTENT_LENGTH']));
        if ($content_length > $this->get_config_bytes(ini_get('post_max_size'))) {
            $file->error = $this->get_error_message('post_max_size');
            return false;
        }
        if (!preg_match($this->options['accept_file_types'], $file->name)) {
            $file->error = $this->get_error_message('accept_file_types');
            return false;
        }
        if ($uploaded_file && is_uploaded_file($uploaded_file)) {
            $file_size = $this->get_file_size($uploaded_file);
        } else {
            $file_size = $content_length;
        }
        if ($this->options['max_file_size'] && (
            $file_size > $this->options['max_file_size'] ||
                $file->size > $this->options['max_file_size'])
        ) {
            $file->error = $this->get_error_message('max_file_size');
            return false;
        }
        if ($this->options['min_file_size'] &&
            $file_size < $this->options['min_file_size']
        ) {
            $file->error = $this->get_error_message('min_file_size');
            return false;
        }
        if (is_int($this->options['max_number_of_files']) && (
            $this->count_file_objects() >= $this->options['max_number_of_files'])
        ) {
            $file->error = $this->get_error_message('max_number_of_files');
            return false;
        }
        list($img_width, $img_height) = @getimagesize($uploaded_file);
        if (is_int($img_width)) {
            if ($this->options['max_width'] && $img_width > $this->options['max_width']) {
                $file->error = $this->get_error_message('max_width');
                return false;
            }
            if ($this->options['max_height'] && $img_height > $this->options['max_height']) {
                $file->error = $this->get_error_message('max_height');
                return false;
            }
            if ($this->options['min_width'] && $img_width < $this->options['min_width']) {
                $file->error = $this->get_error_message('min_width');
                return false;
            }
            if ($this->options['min_height'] && $img_height < $this->options['min_height']) {
                $file->error = $this->get_error_message('min_height');
                return false;
            }
        }
        return true;
    }

    protected function upcount_name_callback($matches)
    {
        $index = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        $ext = isset($matches[2]) ? $matches[2] : '';
        return ' (' . $index . ')' . $ext;
    }

    protected function upcount_name($name)
    {
        return preg_replace_callback(
            '/(?:(?: \(([\d]+)\))?(\.[^.]+))?$/',
            array($this, 'upcount_name_callback'),
            $name,
            1
        );
    }

    protected function trim_file_name($name, $type, $index, $content_range)
    {
        // Remove path information and dots around the filename, to prevent uploading
        // into different directories or replacing hidden system files.
        // Also remove control characters and spaces (\x00..\x20) around the filename:
        $file_name = trim(basename(stripslashes($name)), ".\x00..\x20");
        // Add missing file extension for known image types:
        if (strpos($file_name, '.') === false &&
            preg_match('/^image\/(gif|jpe?g|png)/', $type, $matches)
        ) {
            $file_name .= '.' . $matches[1];
        }
        while (is_dir($this->get_upload_path($file_name))) {
            $file_name = $this->upcount_name($file_name);
        }
        $uploaded_bytes = $this->fix_integer_overflow(intval($content_range[1]));
        while (is_file($this->get_upload_path($file_name))) {
            if ($uploaded_bytes === $this->get_file_size(
                $this->get_upload_path($file_name))
            ) {
                break;
            }
            $file_name = $this->upcount_name($file_name);
        }
        return $file_name;
    }

    protected function handle_form_data($file, $index)
    {
        // Handle form data, e.g. $_REQUEST['description'][$index]
    }

    protected function orient_image($file_path)
    {
        if (!function_exists('exif_read_data')) {
            return false;
        }
        $exif = @exif_read_data($file_path);
        if ($exif === false) {
            return false;
        }
        $orientation = intval(@$exif['Orientation']);
        if (!in_array($orientation, array(3, 6, 8))) {
            return false;
        }
        $image = @imagecreatefromjpeg($file_path);
        switch ($orientation) {
            case 3:
                $image = @imagerotate($image, 180, 0);
                break;
            case 6:
                $image = @imagerotate($image, 270, 0);
                break;
            case 8:
                $image = @imagerotate($image, 90, 0);
                break;
            default:
                return false;
        }
        $success = imagejpeg($image, $file_path);
        // Free up memory (imagedestroy does not delete files):
        @imagedestroy($image);
        return $success;
    }

    protected function make_dir_for_upload_file()
    {
        $path = strftime("%Y/%m/%d/");
        $absolute_path = $this->options['upload_dir'] . $path;
        if (!is_dir($absolute_path)) {
            mkdir($absolute_path, $this->options['mkdir_mode'], true);
        }
        return $path;
    }

    protected function handle_file_upload($uploaded_file, $name, $size, $type, $error,
                                          $index = null, $content_range = null)
    {
        $file = new stdClass();
        //去除路径信息，文件只存在于该目录内，而不会破坏其他目录结构
        //取出点号和控制符号，不会覆盖隐藏文件
        //如果存在一个已经上传的同名图片，则upcount文件名
        $file->name = $this->trim_file_name($name, $type, $index, $content_range);
        $file->size = $this->fix_integer_overflow(intval($size));
        $file->type = $type;
        if ($this->validate($uploaded_file, $file, $error, $index)) {
            $this->handle_form_data($file, $index);
            $upload_dir = $this->get_upload_path();
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, $this->options['mkdir_mode'], true);
            }
            $file_path = $this->get_upload_path($file->name);
            $append_file = $content_range && is_file($file_path) &&
                $file->size > $this->get_file_size($file_path);
            if ($uploaded_file && is_uploaded_file($uploaded_file)) {
                // multipart/formdata uploads (POST method uploads)
                if ($append_file) {
                    //如果是被上传文件的非第一个数据包，则append到目标文件
                    file_put_contents(
                        $file_path,
                        fopen($uploaded_file, 'r'),
                        FILE_APPEND
                    );
                } else {
                    //如果是上传文件第一段数据包的上传，则直接移到目标目录
                    move_uploaded_file($uploaded_file, $file_path);
                }
            } else {
                // Non-multipart uploads (PUT method support)
                file_put_contents(
                    $file_path,
                    fopen('php://input', 'r'),
                    $append_file ? FILE_APPEND : 0
                );
            }
            $file_size = $this->get_file_size($file_path, $append_file);
            if ($file_size === $file->size) { //如果文件上传完成
                $upload_file = new UploadFile; //创建文件到数据库
                $upload_file->attributes = array(
                    'user_id' => Yii::app()->user->id
                );
                try {
                    if (!$upload_file->save()) {
                        throw new Exception("upload file first db save fail", 1);
                    }
                    //将文件改名放入指定目录
                    $file_dir_under_upload_dir = $this->make_dir_for_upload_file();

                    $upload_file_name = Common::generateRandomString(10) . strrchr($file->name, '.');
                    $upload_file_path = $file_dir_under_upload_dir . $upload_file_name;

                    if (!rename($file_path, $this->options['upload_dir'] . $upload_file_path)) {
                        throw new Exception("Move $file_path to $upload_file_path failed", 2);
                    }
                    //保存文件信息
                    $upload_file->path = $upload_file_path;
                    $upload_file->name = $upload_file_name;
                    $upload_file->size = $file_size;
                    if (!$upload_file->save()) {
                        throw new Exception('upload file second db save fail', 3);
                    }

                } catch (Exception $e) { //异常处理
                    if ($e->getCode() == 1) {
                        unlink($file_path);
                        $upload_file->delete();
                    } elseif ($e->getCode() == 2) {
                        unlink($file_path);
                    } elseif ($e->getCode() == 3) {
                        unlink($upload_file_path);
                        $upload_file->delete();
                    }
                }
                $file->url = $this->get_download_url($upload_file_name, null, $file_dir_under_upload_dir);
                $file->object = $upload_file;
                $file->id = $upload_file->id;

                $this->set_file_delete_properties($file);
            } else if (!$content_range && $this->options['discard_aborted_uploads']) {
                //如果上传中途终止上传，则删除图片
                unlink($file_path);
                $file->error = 'abort';
            }
            $file->postfix = strtolower(substr(strrchr($name, '.'), 1));
            $file->name = $name;
            $file->size = $file_size;
            $file->delete_type = $this->options['delete_type'];
            //设置用什么url可以删除该图片
        }
        return $file;
    }

    protected function readfile($file_path)
    {
        return readfile($file_path);
    }

    protected function body($str)
    {
        echo $str;
    }

    protected function header($str)
    {
        header($str);
    }

    public function generate_response($content, $print_response = true)
    {
        if ($print_response) {
            $json = json_encode($content);
            $redirect = isset($_REQUEST['redirect']) ?
                stripslashes($_REQUEST['redirect']) : null;
            if ($redirect) {
                $this->header('Location: ' . sprintf($redirect, rawurlencode($json)));
                return;
            }
            $this->head();
            if (isset($_SERVER['HTTP_CONTENT_RANGE']) && is_array($content) && count($content) != 0 &&
                is_object($content[0]) && $content[0]->size
            ) {
                $this->header('Range: 0-' . ($this->fix_integer_overflow(intval($content[0]->size)) - 1));
            }
            $this->body($json);
        }
        return $content;
    }

    protected function get_version_param()
    {
        return isset($_GET['version']) ? basename(stripslashes($_GET['version'])) : null;
    }

    protected function get_file_name_param()
    {
        return isset($_GET['file']) ? basename(stripslashes($_GET['file'])) : null;
    }

    protected function get_file_type($file_path)
    {
        switch (strtolower(pathinfo($file_path, PATHINFO_EXTENSION))) {
            case 'jpeg':
            case 'jpg':
                return 'image/jpeg';
            case 'png':
                return 'image/png';
            case 'gif':
                return 'image/gif';
            default:
                return '';
        }
    }

    protected function download()
    {
        if (!$this->options['download_via_php']) {
            $this->header('HTTP/1.1 403 Forbidden');
            return;
        }
        $file_name = $this->get_file_name_param();
        if ($this->is_valid_file_object($file_name)) {
            $file_path = $this->get_upload_path($file_name, $this->get_version_param());
            if (is_file($file_path)) {
                if (!preg_match($this->options['inline_file_types'], $file_name)) {
                    $this->header('Content-Description: File Transfer');
                    $this->header('Content-Type: application/octet-stream');
                    $this->header('Content-Disposition: attachment; filename="' . $file_name . '"');
                    $this->header('Content-Transfer-Encoding: binary');
                } else {
                    // Prevent Internet Explorer from MIME-sniffing the content-type:
                    $this->header('X-Content-Type-Options: nosniff');
                    $this->header('Content-Type: ' . $this->get_file_type($file_path));
                    $this->header('Content-Disposition: inline; filename="' . $file_name . '"');
                }
                $this->header('Content-Length: ' . $this->get_file_size($file_path));
                $this->header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($file_path)));
                $this->readfile($file_path);
            }
        }
    }

    protected function send_content_type_header()
    {
        $this->header('Vary: Accept');
        if (isset($_SERVER['HTTP_ACCEPT']) &&
            (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
        ) {
            $this->header('Content-type: application/json');
        } else {
            $this->header('Content-type: text/plain');
        }
    }

    protected function send_access_control_headers()
    {
        $this->header('Access-Control-Allow-Origin: ' . $this->options['access_control_allow_origin']);
        $this->header('Access-Control-Allow-Credentials: '
            . ($this->options['access_control_allow_credentials'] ? 'true' : 'false'));
        $this->header('Access-Control-Allow-Methods: '
            . implode(', ', $this->options['access_control_allow_methods']));
        $this->header('Access-Control-Allow-Headers: '
            . implode(', ', $this->options['access_control_allow_headers']));
    }

    public function head()
    {
        $this->header('Pragma: no-cache');
        $this->header('Cache-Control: no-store, no-cache, must-revalidate');
        $this->header('Content-Disposition: inline; filename="files.json"');
        // Prevent Internet Explorer from MIME-sniffing the content-type:
        $this->header('X-Content-Type-Options: nosniff');
        if ($this->options['access_control_allow_origin']) {
            $this->send_access_control_headers();
        }
        $this->send_content_type_header();
    }

    public function get($print_response = true)
    {
        $ret_val = array();
        if ($print_response && isset($_GET['download'])) {
            return $this->download();
        }
        $files = UploadFile::model()->findAll();
        foreach ($files as $file) {
            $obj = new stdClass();
            $obj->name = $file->name;
            $obj->id = $file->id;
            $obj->url = $file->getUrl();
            $obj->size = $file->size;
            $this->set_file_delete_properties($obj);
            $ret_val[] = $obj;
        }
        return $this->generate_response(array('files' => $ret_val), $print_response);

        $file_name = $this->get_file_name_param();
        if ($file_name) {
            $response = array('file' => $this->get_file_object($file_name));
        } else {
            $response = array('files' => $this->get_file_objects());
        }
        return $this->generate_response($response, $print_response);
    }

    public function post($print_response = true)
    {
        //为了兼容没有delete方法的浏览器执行删除操作
        if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE') {
            return $this->delete($print_response);
        }
        //options里面指明$_POST里面用哪个key来标记被上传文件
        $upload = isset($_FILES[$this->options['param_name']]) ?
            $_FILES[$this->options['param_name']] : null;
        // Parse the Content-Disposition header, if available:
        $file_name = isset($_SERVER['HTTP_CONTENT_DISPOSITION']) ?
            rawurldecode(preg_replace(
                '/(^[^"]+")|("$)/',
                '',
                $_SERVER['HTTP_CONTENT_DISPOSITION']
            )) : null;
        // Parse the Content-Range header, which has the following form:
        // Content-Range: bytes 0-524287/2000000
        $content_range = isset($_SERVER['HTTP_CONTENT_RANGE']) ?
            preg_split('/[^0-9]+/', $_SERVER['HTTP_CONTENT_RANGE']) : null;
        //被上传文件的总大小
        $size = $content_range ? $content_range[3] : null;
        $files = array();
        if ($upload && is_array($upload['tmp_name'])) {
            //如果被上传了多个文件，则逐一处理
            // param_name is an array identifier like "files[]",
            // $_FILES is a multi-dimensional array:
            foreach ($upload['tmp_name'] as $index => $value) {
                //$uploaded_file, $name, $size, $type, $error, $index = null, $content_range = null
                $files[] = $this->handle_file_upload(
                    $upload['tmp_name'][$index],
                    $file_name ? $file_name : $upload['name'][$index],
                    $size ? $size : $upload['size'][$index],
                    $upload['type'][$index],
                    $upload['error'][$index],
                    $index,
                    $content_range
                );
            }
        } else {
            //如果被上传了一个文件，则只要处理一次
            // param_name is a single object identifier like "file",
            // $_FILES is a one-dimensional array:
            $files[] = $this->handle_file_upload(
                isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
                $file_name ? $file_name : (isset($upload['name']) ?
                    $upload['name'] : null),
                $size ? $size : (isset($upload['size']) ?
                    $upload['size'] : $_SERVER['CONTENT_LENGTH']),
                isset($upload['type']) ?
                    $upload['type'] : $_SERVER['CONTENT_TYPE'],
                isset($upload['error']) ? $upload['error'] : null,
                null,
                $content_range
            );
        }
        return $files;
    }

    public function delete($print_response = true)
    {
        $file_name = $this->get_file_name_param();
        $file_path = $this->get_upload_path($file_name, 'origin');
        if (preg_match('/^(\d+)\..*/', $file_name, $matches)) {
            $image = Image::model()->findByPk($matches[1]);
            if ($image->user_id != Yii::app()->user->id) {
                AjaxResponse::forbidden();
            }
            $image->delete();
        }
        $success = is_file($file_path) && $file_name[0] !== '.' && unlink($file_path);
        if ($success) {
            foreach ($this->options['image_versions'] as $version => $options) {
                if (!empty($version)) {
                    $file = $this->get_upload_path($file_name, $version);
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
        }
        return $this->generate_response(array('success' => $success), $print_response);
    }

    public function onFileUploadingFinished($file_path)
    {

    }

    public function onFileDelete($file_path)
    {

    }
}