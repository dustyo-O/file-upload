<?php
/**
 * Created by PhpStorm.
 * User: dusty
 * Date: 23.01.17
 * Time: 20:28
 */
use Gregwar\Captcha\CaptchaBuilder;

$title = 'GeekFiles';

$this->registerCssFile("/assets/css/index.css");

$this->title = 'Главная - '.$title;

/* @var $builder \Gregwar\Captcha\CaptchaBuilder */

$builder = new CaptchaBuilder();
$builder->build();

System::setMessage('captcha', $builder->getPhrase());
?>
<div class="index-form">
    <h1><?= $title ?></h1>
    <form class="form-inline" role="form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="upload">
        <div class="form-group">
            <label class="sr-only" for="file-upload">Загрузите файл</label>
            <input type="file" id="file-upload" name="file-upload">
        </div>
        <div class="form-group">
            <input class="form-control" type="text" name="captcha" id="captcha" placeholder="Код с изображения"/>
        </div>
        <div class="form-group">
            <img src="<?php echo $builder->inline(); ?>" />
        </div>
        <button type="submit" class="btn btn-success">Загрузить</button>
    </form>
</div>

