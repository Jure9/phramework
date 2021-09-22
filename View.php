<?php

namespace app\core;

class View
{
    public string $title = '';

    // public function renderView($view, array $params)
    // {
    //     $layoutName = Application::$app->layout;
    //     if (Application::$app->controller) {
    //         $layoutName = Application::$app->controller->layout;
    //     }
    //     $viewContent = $this->renderViewOnly($view, $params);
    //     ob_start();
    //     include_once Application::$ROOT_DIR."/views/layouts/$layoutName.php";
    //     $layoutContent = ob_get_clean();
    //     return str_replace('{{content}}', $viewContent, $layoutContent);
    // }

    // public function renderViewOnly($view, array $params)
    // {
    //     foreach ($params as $key => $value) {
    //         $$key = $value;
    //     }
    //     ob_start();
    //     include_once Application::$ROOT_DIR."/views/$view.php";
    //     return ob_get_clean();
    // }

    public function renderView($view, $params= [])
    {
        $viewContent= $this->renderOnlyView($view, $params);
        $layoutContent= $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    public function renderContent($content)
    {
        $layoutContent= $this->layoutContent();
        return str_replace('{{content}}', $content, $layoutContent);
    }

    protected function layoutContent()
    {
        $layout= Application::$app->layout;
        if(Application::$app->controller)
        {
            $layout= Application::$app->controller->layout;
        }
        ob_start();
        include Application::$ROOT_DIR."/views/layouts/$layout.php";
        return ob_get_clean();
    }

    protected function renderOnlyView($view, $params)
    {        
        // extract($params);

        foreach ($params as $key => $value) {
            $$key = $value;
        }

        ob_start();
        include Application::$ROOT_DIR."/views/{$view}.view.php";
        return ob_get_clean();
    }
}