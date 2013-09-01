<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 勃
 * Date: 13-5-28
 * Time: 下午9:42
 * To change this template use File | Settings | File Templates.
 */
class ImageUtil
{
    /**
     * 根据图片的exif信息,调整图片方向
     * @param $file_path 图片绝对路径
     * @return bool
     */
    public static function orient_image($file_path)
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

    /**
     * 缩小图片大小
     * @param $file_path 被处理图片的绝对路径
     * @param $file_name 被处理图片的文件名
     * @param $version  被处理图片需要放入哪个子目录
     * @param $options  处理参数,包括max_width,max_height,jpeg_quality
     * array(
     * 'max_width' => 800,
     * 'max_height' => 600,
     * 'jpeg_quality' => 80
     * )
     * @return bool|string
     * @throws Exception
     */
    public static function create_scaled_image($file_path, $file_name, $version, $options)
    {
        $new_file_relative_path='';
        //获取缩放后图片的路径
        if (!empty($version)) {
            $version_dir = $version . '/' . strftime("%Y/%m/%d/");
            if (!is_dir(Yii::app()->fileUpload->upload_dir .$version_dir)) {
                mkdir(Yii::app()->fileUpload->upload_dir .$version_dir, Yii::app()->fileUpload->mkdir_mode, true);
            }
            $new_file_relative_path= $version_dir . '/' . $file_name;
            $new_file_path = Yii::app()->fileUpload->upload_dir .$new_file_relative_path;
        } else {
            $new_file_path = $file_path;
        }
        //获取原图片大小
        list($img_width, $img_height) = @getimagesize($file_path);
        if (!$img_width || !$img_height) {
            throw new Exception("image width and height can not be zero");
        }
        //缩放比例
        $scale = min(
            $options['max_width'] / $img_width,
            $options['max_height'] / $img_height
        );
        //如果目前图片大小比最大长宽小，则不变换大小，只拷贝图片
        if ($scale >= 1) {
            if ($file_path !== $new_file_path) {
                copy($file_path, $new_file_path);
            }
            return $new_file_relative_path;
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

        if(!$success){
            throw new Exception("rescaled image write failed");
        }
        return $version_dir . $file_name;
    }
}
