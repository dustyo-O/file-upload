<?php
/**
 * Created by PhpStorm.
 * User: dusty
 * Date: 23.01.17
 * Time: 20:27
 */
use Gregwar\Captcha\CaptchaBuilder;

require_once "system/helpers/file-upload.class.php";

class MainController extends Controller
{
    public function actionIndex()
    {
        if (System::post('action') === 'upload')
        {
            $user_captcha = System::post('captcha');

            $builder = new CaptchaBuilder(System::getMessage('captcha'));
            $builder->build();

            if (!$builder->testPhrase($user_captcha))
            {
                System::setMessage('error', 'Вы ввели неверный код');

                header("Location: /");
                exit();
            }

            $file = $_FILES['file-upload']; // TODO проверка всего - размер и т.п.

            $folder = UploadedFile::generateFolder();

            if (FileUpload::upload($file, UploadedFile::UPLOADS_ROOT . '/' . $folder))
            {
                $file_name = $file["name"];

                $uploaded_file = new UploadedFile();
                $uploaded_file->filename = $file_name;
                $uploaded_file->folder = $folder;

                if ($uploaded_file->save())
                {
                    System::setMessage('success', 'Ура! Файл загружен! Ссылка на файл: <a href="/file/d/'.$uploaded_file->url.'">тыц</a>');
                }
                else
                {
                    System::setMessage('error', 'К сожалению, загрузить файл не удалось');
                }

                header("Location: /");
                exit();
            }
        }

        $this->render('main/index.php', [
        ]);
    }

}