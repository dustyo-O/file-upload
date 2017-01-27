<?php
/**
 * Created by PhpStorm.
 * User: Dusty
 * Date: 13.01.2017
 * Time: 20:34
 */

class Template {
    public $layout;

    public $title;
    public $description;

    public $css = [];
    public $js = [];

    public $template;

    public function render($data = [])
    {
        foreach($data as $key => $value)
        {
            $$key = $value; // $key = 'message' $$key $'message' $message
        }

        unset($data);

        ob_start();
        require_once './templates/'.$this->template;
        $content = ob_get_contents();
        ob_end_clean();

        require_once './templates/layout/'.$this->layout;
    }

    public function head()
    {
        $result = '';
        foreach ($this->css as $css)
        {
            if ($result !== '') $result .= "\t";
            $result .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$css}\"/>\r\n";
        }
        return $result;
    }

    public function endBody()
    {
        $result = '';
        foreach ($this->js as $js)
        {
            if ($result !== '') $result .= "\t";
            $result .= "<script type=\"text/javascript\" href=\"{$js}\">\r\n";
        }
        return $result;
    }

    public function registerCssFile($css_file)
    {
        array_unshift($this->css, $css_file);
    }
    public function registerJSFile($js_file)
    {
        array_unshift($this->js, $js_file);
    }
}