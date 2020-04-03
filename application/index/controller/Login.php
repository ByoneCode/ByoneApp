<?php

namespace app\index\controller;

use app\index\controller\Byse;

use \think\Db;

use \think\Cookie;

class Login extends Byse

{

    public function index()

    {

       $this->isindex();
       $tion = DB::name('seting')
       ->field('tion')
       ->find();
       $this->assign("tion",$tion);
       return $this->fetch();

    }



    public function reg(){

        $tion = DB::name('seting')

        ->field('tion')

        ->find();

        $user = input('post.user');

        $tionn = input('post.tion');

        $pass = input('post.password');

        $rule=preg_match("/[\x7f-\xff]/",$user);

        if(!isset($user)){

            return $this->error("非法访问");

            exit;

        }

        $ip = $_SERVER['REMOTE_ADDR'];

        $time = time();

        $data = [

            'user'=>$user,

            'password'=>md5(md5($pass)),

            'nickname'=>$user,

            'time'=>$time,

            'ip'=>$ip,     

        ];

        if($tion['tion'] != ''){

            if(empty($user)||empty($tion)||empty($pass)){

                return ['state'=>0,'info'=>'填写完整'];

                exit;

            }else{

                if($tionn!= $tion['tion']){

                    return ['state'=>0,'info'=>'邀请码错误'];

                    exit;

                }

            }

        }else{

            if(empty($user)||empty($pass)){

                return ['state'=>0,'info'=>'填写完整'];

                exit;

            }

        }

        if($rule){

            return ['state'=>0,'info'=>'用户名不可输入中文','bool'=>$rule];

            exit; 

        }

        $row = Db::name('user')->where('ip',$ip)->select();

        if(count($row)>=2){

            return ['state'=>0,'info'=>'注册过多'];

            exit;

        }

        $reg = Db::name('user')->where('user',$user)->find();

        if($reg){

            return ['state'=>0,'info'=>'用户已存在'];

        }else{

        $res = Db::name('user')->insert($data);

        if($res){

            return ['state'=>1,'info'=>'注册成功'];

        }else{

            return ['state'=>0,'info'=>'注册失败'];

        }

        }

    }

    



    public function log(){

        $user = input('post.user1');

        $pass = input('post.pwd');

        if(!isset($user)||!isset($pass)){

            return $this->error("非法访问");

            exit;

        }

        // $pwd = Db::name('user')->where('user',$user)->where('password','neq','')->find();

        $res = Db::name('user')->where('user',$user)->find();

        if($res['passtype']==1){

            if(!$res){

               return ['state'=>0,'info'=>'用户不存在']; 

            }else if($res['status']!=1){

               return ['state'=>0,'info'=>'账号已被拉黑']; 

            }else{

               if(md5(md5($pass))==$res['password']){

                Cookie::set('userid',$res['id']); 

                Cookie::set('username',$res['user']); 

                return ['state'=>1,'info'=>'登录成功'];

               }else{

                return ['state'=>0,'info'=>'密码错误']; 

               }

            }

        }else{

            if($res){

                Cookie::set('userid',$res['id']); 

                Cookie::set('username',$res['user']);

                if($res['status']!=1){

                   return ['state'=>0,'info'=>'账号已被拉黑']; 

                }else{

                  return ['state'=>1,'info'=>'登录成功'];   

                } 

            }else{

                return ['state'=>0,'info'=>'用户不存在']; 

            }

        }

    }

}

