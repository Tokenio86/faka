<?php

namespace app\daili\controller;

use think\Controller;
use think\Request;
class Common extends Controller
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        if (!session('dailiuser')) {
            $this->redirect('/daili.php/login/login');
        }
    }
}