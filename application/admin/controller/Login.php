<?php

namespace app\admin\controller;
use app\admin\controller\Byse;
use \think\Cookie;
use \think\Request;
use \think\Db;
class Login extends Byse
{
    public function index()
    {
       return $this->fetch();
    }

    public function login(){

        $user = input('user');
        $pass = md5(md5(input('pass')));
        $res = Db::name('admin')
        ->where('user',$user)
        ->find();
        if(empty($user)||empty($pass)){
            return ['status'=>0,'msg'=>'填写完整'];
            exit;
        }
        if($res){
            if($res['type'] != 'admin'){
                return ['status'=>0,'msg'=>'你还不是管理员'];
            }else{
                if($res['password'] == $pass){
                    Cookie::prefix('ad_');
                    Cookie::set('adminid',$res['id']); 
                    Cookie::set('adminuser',$res['user']); 
                    return ['status'=>1,'msg'=>'登录成功'];
                }else{
                    return ['status'=>0,'msg'=>'密码错误'];
                }
            }
        }
        else{
            return ['status'=>0,'msg'=>'用户不存在'];
        }
    }
}

