<?php

namespace app\admin\controller;
use \think\Controller;
use \think\Cookie;
use \think\Request;
use \think\Db;
use \think\Config;
class Byse extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $request = Request::instance();
        $con   = $request->controller(); //控制器
        define('ADMIN_ID',Cookie::get('adminid','ad_'));
        //登录操作判断
        if($con != 'Login'){
          if(empty(ADMIN_ID)){
            $this->redirect('/admin/login');
          }
        }else{
          if(!empty(ADMIN_ID)){
            $this->redirect('/admin/index');
          } 
        }

    }



    protected function islogin(){

        if(empty(USER_ID)){

          $this->redirect('/index/login');

          exit;

        }

        $user = Db::name('user')

        ->where('id',USER_ID)

        ->find();

        if(!$user){

          Cookie::clear('by_');

          $this->error('账号不存在','/index/login');

          exit;   

        }else{

          if($user['status']==0){

            Cookie::clear('by_');

            $this->error('你已被拉黑','/index/login');

            exit; 

          }

        }

        

        $res = Db::name('app')

        ->where('url',LINK)

        ->find();

        if($res){

            if($res['status']==0){

              $this->error('访问被禁','/index/index');   

              exit;

            }  

        }

    }



    protected function isindex(){

        if(!empty(USER_ID)){

            $this->error('操作失误','/index/index');

        } 

    }





}

