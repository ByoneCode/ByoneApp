<?php
namespace app\index\controller;
use app\index\controller\Byse;
use \think\Request;
use \think\Db;
use \think\Config;
class User extends Byse
{
    public function index()
    {
       $this-> islogin();
       $id = USER_ID;
       $info = Db::name('user')
       ->where('id',$id)
       ->find();
       $this->assign('title',"编辑会员");
       $this->assign('info',$info);
       return $this->fetch();
    }

    public function updata_headimg(){  //上传文件
        $file = request()->file('headimg');
        if($file){
           $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads/headimg/','');
           if($info){
               $path = $info->getSaveName();
               return ['code'=>1,'info'=>['path'=>'/uploads/headimg/'.$path,'msg'=>'上传成功']];
           }else{
               $error = $file->getError();
               return ['code'=>1,'info'=>['msg'=>$error]];
           }
       }
    }


    public function edit_user(){
        $all = input('param.');
        $id = USER_ID;
        $res = Db::name('user')
        ->where('id',$id)
        ->update($all);
        if($res == 1){
            return ['status'=>1,'info'=>'修改成功'];
        }else{
            return ['status'=>0,'info'=>'修改错误'];
        }
    }
}
