<?php
/**
 * Created by PhpStorm.
 * User: dusty
 * Date: 23.01.17
 * Time: 20:52
 */
// id url folder filename
/**
 * Class UploadedFile
 * @public filename string
 */
class UploadedFile extends model
{
    public static $table = 'files';

    const UPLOADS_ROOT = 'uploads';

    private static function generateRandomString($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function generateFolder()
    {
        $folder = '';

        while (file_exists(static::UPLOADS_ROOT . '/' . $folder))
        {
            $folder = self::generateRandomString();
        }

        mkdir(static::UPLOADS_ROOT . '/' . $folder);

        return $folder;
    }

    protected static function generateURL()
    {
        do {
            $url = self::generateRandomString(5);
            $query = "SELECT COUNT(*) FROM `" . static::get_table() . "` WHERE `url` = '{$url}'";

            $result = mysqli_query(self::get_db(), $query);

            if (!($row = mysqli_fetch_row($result)))
            {
                throw new Exception('generate URL failed');
            }
        } while((int) $row[0] > 0);

        return $url;
    }

    public static function getFileByUrl($url)
    {

        $query = "SELECT * FROM `" . static::get_table() . "` WHERE `url` = '{$url}'";



        $result = mysqli_query(self::get_db(), $query);

        if (!($row = mysqli_fetch_assoc($result)))
        {
            throw new Exception('file not found');
        }

        $class_name = static::class;
        $file = new $class_name();

        $file->load($row);

        return $file;
    }

    public function save()
    {
        if ($this->url === NULL)
        {
            $this->url = static::generateURL();
        }
        return parent::save();
    }

    public function getUrl()
    {
        return '/' .static::UPLOADS_ROOT . '/' . $this->folder . '/' . $this->filename;
    }
}


