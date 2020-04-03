<?php

namespace app\index\controller;

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

        if(Cookie::has('userid')){
          define('USER_ID',Cookie::get('userid'));
          define('USER_NAME',Cookie::get('username'));
        }else{
          define('USER_ID',-1);
          define('USER_NAME','游客');
        }


        $request = Request::instance();
        $con   = $request->controller(); //控制器
        $mode = $request->module(); //模块
        $action = $request->action(); //方法
        $link = '/'.$mode.'/'.$con;
        define('LINK',$link);
        $set = Db::name('seting')
        ->find();
        Config::set('SET',$set);
        $this->assign('action',$action);
    }



    protected function islogin(){

        if(empty(USER_ID) || USER_ID == -1){

          $this->redirect('/index/login');

          exit;

        }

        $user = Db::name('user')

        ->where('id',USER_ID)

        ->find();

        if(!$user&&USER_ID != -1){

          Cookie::clear('by_');

          $this->error('账号不存在','/index/login');

          exit;   

        }else{

          if($user['status']==0&&USER_ID != -1){

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

        if(!empty(USER_ID)&&USER_ID != -1){

            $this->error('操作失误','/index/index');

        } 

    }





}

