<?php

namespace app\core;

use app\models\User;

class Application
{

    public string $layout= 'main';
    public static string $ROOT_DIR;
    public string $userClass;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public ?Controller $controller= null;
    public static Application $app;
    public ?User $user;
    public View $view;

    public function __construct($rootPath, $config)
    {
        $this->user = null;
        $this->userClass = $config['userClass'];
        static::$ROOT_DIR= $rootPath;
        static::$app= $this;
        $this->request= new Request();
        $this->response= new Response();
        $this->session= new Session();
        $this->view= new View();
        $this->router= new Router($this->request, $this->response);

        $this->db= new Database($config['db']);

        $userId = Application::$app->session->get('user');
        if ($userId) {
            $key = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$key => $userId]);
        }
    }

    public static function isGuest()
    {
        return !self::$app->user;
    }

    public function login(DbModel $user)
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $value = $user->{$primaryKey};
        Application::$app->session->set('user', $value);

        return true;
    }

    public function logout()
    {
        $this->user = null;
        self::$app->session->remove('user');
    }

    public function run()
    {
        try{
            echo $this->router->resolve();
        }catch(\Exception $e){
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('_error', [
                'exception' => $e,
            ]);
        }
    }
}