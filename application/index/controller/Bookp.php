<?php
namespace app\index\controller;
use app\index\controller\Byse;
use \think\Request;
use \think\Db;
class Bookp extends Byse
{
    public function index()
    {
       $this-> islogin();
       $id = input('id');
       $uid = USER_ID;
       $res = Db::name('app')
       ->where('id',$id)
       ->find();
       if(empty($id)||empty($res)||LINK!=$res['url']){
        $this->error('页面错误');
        exit;
       }

       $list = Db::name('bookkp')
       ->where('uid',$uid)
       ->where('type','year')
       ->select();
       $this->assign('list',$list);
       $this->assign('title',$res['title']);
       $this->assign('app',$res);
       return $this->fetch(); 
    }


    public function days(){
      $this-> islogin();
      $id = input('id');
      $uid = USER_ID;
      $res = Db::name('bookkp')
       ->where('id',$id)
       ->find();
      if(empty($id)||!$res){
         $this->error('页面错误');
         exit;
       }
      //  dump($res);
      $list = Db::name('bookkp')
      ->where('uid',$uid)
      ->where('type','days')
      ->where('fatday',$id)
      ->select();
      $this->assign('list',$list);
      $this->assign('title',$res['date']);
      $this->assign('app',$res);
      return $this->fetch(); 
    }

    public function add_ymonth(){ //添加当月账本
       $uid = USER_ID; //用户id
       $year = date('Y').'-'.date('m'); //年月
       $time = time();
       
      $res = Db::name('bookkp')
      ->field('id,uid,date,time')
      ->where('uid', $uid)
      ->where('type','year')
      ->where('date',$year)
      ->find();
      
      if($res){
         return ['status'=>0,'info'=>'你已经创建该月账单了'];
      }else{
         $data = [
            'uid'=>$uid,
            'type'=>'year',
            'date'=>$year,
            'title'=>$year,
            'time'=>$time
         ];
         $res = Db::name('bookkp')
         ->insert($data);
         
         if($res == 1){
            return ['status'=>1,'info'=>'创建账单成功'];
         }else{
            return ['status'=>0,'info'=>'创建账单错误'];
         }
      }

    }

    public function add_days(){ //添加当天账本
      $uid = USER_ID; //用户id
      $id = input('id');
      $days = date('Y').'-'.date('m').'-'.date('d'); //年月
      $time = time();
      
      $res = Db::name('bookkp')
      ->field('id,uid,date,time')
      ->where('uid', $uid)
      ->where('type','days')
      ->where('date',$days)
      ->find();

      if($res){
         return ['status'=>0,'info'=>'你已经创建今日账单了'];
      }else{
         $data = [
            'uid'=>$uid,
            'type'=>'days',
            'date'=>$days,
            'title'=>$days,
            'fatday' =>$id,
            'time'=>$time
         ];
         $res = Db::name('bookkp')
         ->insert($data);
         
         if($res == 1){
            return ['status'=>1,'info'=>'创建账单成功'];
         }else{
            return ['status'=>0,'info'=>'创建账单错误'];
         }
      }
    }

    public function consum(){ //页面查询该日账单数据
       $this->islogin();
       $id = input('id');
       $uid = USER_ID;
       $res = Db::name('bookkp')
        ->where('id',$id)
        ->find();
       if(empty($id)||!$res){
          $this->error('页面错误');
          exit;
        }
       $list = Db::name('consum')
       ->alias('a')
       ->join('by_bookkp i','a.zid = i.id')
       ->field('a.id,a.content,a.money,a.pay,a.time')
       ->where('i.uid',$uid)
       ->where('i.id',$id)
       ->select();

       $this->assign('list',$list);
       $this->assign('app',$res);
       $this->assign('zid',$id);
       $this->assign('title',$res['date']);
       return $this->fetch();
    }

    public function add_consum(){  //添加数据
       $time = time();
       $all = input('param.');
       $all['time']= $time;
       
       $res = Db::name('consum')
       ->insert($all);
       if($res == 1){
         return ['status'=>1,'info'=>'添加成功'];
       }else{
         return ['status'=>0,'info'=>'添加错误'];
       }
    }

   public function del_consum(){ //删除数据
      $id = input('id');
      $res = Db::name('consum')
      ->where('id',$id)
      ->delete();
      if($res == 1){
         return ['status'=>1,'info'=>'删除成功'];
       }else{
         return ['status'=>0,'info'=>'删除错误'];
       }
   }

   public function info_consum(){ //查询数据
      $id = input('id');
      $info = Db::name('consum')
      ->field('content,money,pay')
      ->where('id',$id)
      ->find();
      return $info;
   }

   public function edit_consum(){ //修改数据
      $id = input('id');
      $time = time();
      $all = input('param.');
      $all['time']= $time;
      $res = Db::name('consum')
      ->where('id',$id)
      ->update($all);
      if($res == 1){
         return ['status'=>1,'info'=>'修改成功'];
       }else{
         return ['status'=>0,'info'=>'修改错误'];
       }
   }

   public function del_index(){
      $id = input('id');

      $sql = "DELETE a.*,b.* FROM by_bookkp AS a LEFT JOIN by_consum AS b ON b.zid=a.id WHERE a.id=$id OR a.fatday=".$id;
      $return = Db::execute($sql);  //多表删除
        if($return){
            return ['status'=>1,'info'=>'删除成功'];
         }else{
            return ['status'=>0,'info'=>'删除错误','error'=>$return];
         }
   }


   public function del_days(){
      $id = input('id');

      $sql = "DELETE a.*,b.* FROM by_bookkp AS a LEFT JOIN by_consum AS b ON b.zid=a.id WHERE a.id=".$id;
      $return = Db::execute($sql);  //多表删除
        if($return){
            return ['status'=>1,'info'=>'删除成功'];
         }else{
            return ['status'=>0,'info'=>'删除错误','error'=>$return];
         }
      
   }
}
