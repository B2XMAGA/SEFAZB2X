<?php


namespace source\App;


use League\Plates\Engine;

class Web
{
    private $view;

    public function __construct()
    {
        $this->view = Engine::create(__DIR__."/../../views/", "php");
    }

    public function login(): void{

        echo $this->view->render('theme/login', [
            "title" => "Login |".SITE
        ]);
    }

    public function home(): void{

        echo $this->view->render('theme/home', [
            "title" => "Login |".SITE
        ]);
    }

    public function error(array $data): void
    {
        echo $this->view->render("error", [
            "title" => "Erro {$data['errcode']} |".SITE,
            "error" => $data['errcode']
        ]);
    }
}