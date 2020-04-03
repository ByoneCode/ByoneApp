<?php
namespace app\admin\controller;
use app\admin\controller\Byse;
use \think\Cookie;
use app\admin\model\App;
use \think\Db;
class Page extends Byse
{
    public function page_list()
    {
       $key = input('key');
       if(empty($key)){
         $list = App::paginate(10);
       }else{
         $list = App::where('title','like','%'.$key.'%')->paginate(10);  //模糊搜索
       }
       $page = $list->render();
       $this->assign('list',$list);
       $this->assign('page',$page);
       return $this->fetch();
    }
    public function page_edit()
    {
       $id = input('id');
       $find = Db::name('app')
       ->where('id',$id)
       ->find();
       $this->assign('info',$find);
       return $this->fetch();
    }
    public function edit(){
        $id = input('id');
        $all = input('param.');
        $return = Db::name('app')
        ->where('id',$id)
        ->update($all);
        if($return == 1){
            return ['code'=>1,'info'=>'修改成功'];
        }else{
            return ['code'=>0,'info'=>'修改错误'];
        }
    }

    public function page_add(){
        return $this->fetch();
    }

    public function add(){
        $all = input('param.');
        $return = Db::name('app')
        ->insert($all);
        if($return == 1){
            return ['code'=>1,'info'=>'添加成功'];
        }else{
            return ['code'=>0,'info'=>'添加错误'];
        }
    }

    public function updata_img(){  //上传文件
     $file = request()->file('img');
     if($file){
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads','');
        if($info){
            $path = $info->getSaveName();
            return ['code'=>1,'info'=>['path'=>'/uploads/'.$path,'msg'=>'上传成功']];
        }else{
            $error = $file->getError();
            return ['code'=>1,'info'=>['msg'=>$error]];
        }
    }
    }

    public function del(){
        $id = input('id');
        $return = Db::name('app')
        ->where('id',$id)
        ->delete();
        if($return == 1){
            return ['code'=>1,'info'=>'删除成功'];
        }else{
            return ['code'=>0,'info'=>'删除错误'];
        }
    }
    
}
