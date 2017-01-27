<?php
/**
 * Created by PhpStorm.
 * User: dusty
 * Date: 27.01.17
 * Time: 21:08
 */

require_once "system/core.php";
require_once "system/helpers/file-upload.class.php";


class UploadedFileTest extends PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $data = [
            'id' => 123,
            'folder' => 'dummy folder',
            'filename' => 'dummy filename'
        ];

        $uploaded_file = new UploadedFile();
        $uploaded_file->load($data);

        $this->assertEquals($data['id'], $uploaded_file->id, 'UploadedFile: id не загружен методом load');
        $this->assertEquals($data['folder'], $uploaded_file->folder, 'UploadedFile: folder не загружен методом load');
        $this->assertEquals($data['filename'], $uploaded_file->filename, 'UploadedFile: filename не загружен методом load');

    }

    public function testFileUpload()
    {
        $example_file_path = 'tests/test-data/example.json';
        $file = [
            'error' => UPLOAD_ERR_OK,
            'tmp_name' => $example_file_path,
            'name' => pathinfo($example_file_path, PATHINFO_BASENAME)
        ]; // TODO проверка всего - размер и т.п.

        $folder = UploadedFile::generateFolder();

        $upload_error = false;
        try {
            FileUpload::upload($file, UploadedFile::UPLOADS_ROOT . '/' . $folder, true);
        }
        catch (ErrorException $exception)
        {
            $uploaded_error = $exception;
        }

        $this->assertEquals(false, $upload_error, 'FileUpload: Загрузка прошла с ошибкой');

        $file_name = $file["name"];

        $uploaded_file = new UploadedFile();
        $uploaded_file->filename = $file_name;
        $uploaded_file->folder = $folder;

        $uploaded_file->save();

        $this->assertFileExists(UploadedFile::UPLOADS_ROOT . '/' . $folder, 'FileUpload: Папка не создана');
        $this->assertFileExists(UploadedFile::UPLOADS_ROOT . '/' . $folder . '/' . $file_name, 'FileUpload: Файл не существует');
        $this->assertNotNull($uploaded_file, 'FileUpload: При сохранении в БД не был получен id');

        @unlink(UploadedFile::UPLOADS_ROOT . '/' . $folder . '/' . $file_name);
        @rmdir(UploadedFile::UPLOADS_ROOT . '/' . $folder);

        $uploaded_file->delete();

    }
}