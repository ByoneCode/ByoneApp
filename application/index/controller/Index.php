<?php
namespace app\index\controller;
use app\index\controller\Byse;
use \think\Cookie;
use \think\Request;
use \think\Db;
use \think\Config;
class Index extends Byse
{
    public function index()
    {
       $this-> islogin();
       $list = Db::name('app')
       ->where('status',1)
       ->order('order','ASC') //按照序号升序
       ->select();
       $this->assign('title',Config::get('SET')['title']);
       $this->assign('app_list',$list);
       return $this->fetch();
    }

    public function delcok(){
        Cookie::clear('by_');
        header('Location:/index/login/index');
    }

    public function test(){
        $this->isgo();
        return $this->fetch();
    }

    public function user(){
        $this->islogin();
        $id = USER_ID;
        $this->assign('title','个人中心');
        $info = Db::name('user')
        ->where('id',$id)
        ->find();
        $this->assign('info',$info);
        return $this->fetch();
    }
}
