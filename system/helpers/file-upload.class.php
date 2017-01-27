<?php
/**
 * Created by PhpStorm.
 * User: dusty
 * Date: 23.01.17
 * Time: 20:48
 */

class FileUpload {
    public static function upload($file, $path, $is_test = false)
    {
        $upload_function = 'move_uploaded_file';

        if ($is_test) $upload_function = 'copy';

        if ($file["error"] !== UPLOAD_ERR_OK) return NULL;

        $file_name = $path . "/" . $file["name"];

        if ($upload_function($file["tmp_name"], $file_name))
        {
            return $file_name;
        }
        else
        {
            throw new ErrorException('File Upload failed: '.$file_name);
        }
    }
}