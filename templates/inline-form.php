<?php
/**
 * Created by PhpStorm.
 * User: dusty
 * Date: 23.01.17
 * Time: 19:50
 */
?>
<form class="form-inline" role="form">
    <div class="form-group">
        <label class="sr-only" for="file-upload">Загрузите файл</label>
        <input type="file" id="file-upload" name="file-upload">
    </div>
    <div class="form-group">
        <label class="sr-only" for="exampleInputPassword2">Выберите категорию</label>
        <select class="form-control"><option value="0">Без категории</option></select>
    </div>
    <button type="submit" class="btn btn-succes">Загрузить</button>
</form>

