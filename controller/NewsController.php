<?php
/**
 * Created by PhpStorm.
 * User: Dusty
 * Date: 13.01.2017
 * Time: 21:35
 */

class NewsController extends Controller
{
    public function actionView()
    {

    }

    public function actionList()
    {
        $this->render('inline-form.php', [
            'message' => 'Привет',
            'content' => 'Тест'
        ]);
    }

}