<?php
namespace app\admin\controller;
use app\admin\controller\Byse;
use \think\Request;
use app\admin\model\User;
use \think\Db;
class Member extends Byse
{
    public function member_list()
    {
        $return = Db::name('user')
        ->select();
        $this->assign('list',$return);
        return $this->fetch();
    }
    public function member_edit()
    {
        $id = input('id');
        $return = Db::name('user')
        ->where('id',$id)
        ->find();
        $this->assign('info',$return);
        return $this->fetch();
    }
    public function member_add()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $this->assign('ip',$ip);
        return $this->fetch();
    }
    public function edit(){
        $id = input('id');
        $all = input('param.');
        $all['time'] = strtotime($all['time']);
        if($all['password'] ==''){
            unset($all['password']);
        }else{
            $all['password'] = md5(md5($all['password']));
        }
        
        $return = Db::name('user')
        ->where('id',$id)
        ->update($all);
        if($return == 1){
            return ['code'=>1,'info'=>'修改成功'];
        }else{
            return ['code'=>0,'info'=>'修改错误'];
        }
    }

    public function add(){
        $time = time();
        $all = input('param.');
        $all['time'] = $time;
        $all['passtype'] = 1;
        $all['password'] = md5(md5($all['password']));
        $return = Db::name('user')
        ->insert($all);
        if($return == 1){
            return ['code'=>1,'info'=>'添加成功'];
        }else{
            return ['code'=>0,'info'=>'添加错误'];
        }
    }

    public function del(){
        $id = input('id');
        $return = Db::name('user')
        ->where('id',$id)
        ->delete();
        if($return == 1){
            return ['code'=>1,'info'=>'删除成功'];
        }else{
            return ['code'=>0,'info'=>'删除错误'];
        }
    }

    public function delall(){
        $id=input("id/a");
        $return = Db::name('user')
        ->delete($id);
        if(json_encode($return)!=0){
            return ['code'=>1,'info'=>'删除成功'];
        }else{
            return ['code'=>0,'info'=>'删除错误'];
        }
    }

    public function pagedata(){
        //获取总条数
       $list = User::all(); 
       $count=count($list);
       //获取每页显示的条数
       $limit= Request::instance()->param('limit');
       //获取当前页数
       $page= Request::instance()->param('page');
       //计算出从那条开始查询
       $tol=($page-1)*$limit;
       // 查询出当前页数显示的数据
       $listpage = User::field('id,user,nickname,ip,status,time')->limit($tol,$limit)->select();

       $list = collection($listpage)->toArray();
       //返回数据
       return ["code"=>"0","msg"=>"","count"=>$count,"data"=>$list];
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
   
}