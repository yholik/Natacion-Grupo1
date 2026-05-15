<?php

require_once __DIR__ . '/../core/BaseController.php';
require_once __DIR__ . '/../models/Swimmer.php';

class UserController extends BaseController
{
    private $swimmerModel;

    public function __construct()
    {
        global $pdo;

        $this->swimmerModel = new Swimmer($pdo);
    }

    public function index()
    {
        $this->checkAuth();

        $swimmers = $this->swimmerModel->getAll();

        $this->render('users/index', [
            'swimmers' => $swimmers
        ]);
    }
}