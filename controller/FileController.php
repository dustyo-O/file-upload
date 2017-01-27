<?php
/**
 * Created by PhpStorm.
 * User: dusty
 * Date: 23.01.17
 * Time: 21:53
 */

class FileController extends Controller
{
    public function actionD($url)
    {
        $uploaded_file = UploadedFile::getFileByUrl($url);

        $this->render('file/download.php', [
            'file' => $uploaded_file
        ]);
    }

}