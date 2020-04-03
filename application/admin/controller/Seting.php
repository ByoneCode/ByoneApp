<?php
namespace app\admin\controller;
use app\admin\controller\Byse;
use \think\Request;
use \think\Db;
class Seting extends Byse
{
    public function index()
    {
       $res = Db::name('seting')
       ->find();
       $this->assign('info',$res);
       return $this->fetch();
    }

    public function setData(){
        $data =  input('param.');
        
        $res = Db::name('seting')
        ->where('id',1)
        ->update($data);

        if($res == 1){
            return ['code'=>1,'info'=>'设置成功'];
        }else{
            return ['code'=>0,'info'=>'设置错误'];
        }
        
    }
   
}
