<?php
namespace app\admin\controller;
use app\admin\controller\Byse;
use \think\Session;
use \think\Request;
use \think\Db;
class Seting extends Byse
{
    public function index()
    {
       $res = Db::name('seting')
       ->find();
       $this->assign('info',$res);
       dump(Session::get());
       return $this->fetch();
    }
   
}
