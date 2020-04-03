<?php

namespace app\index\controller;

use app\index\controller\Byse;

use app\index\model\User;

use \think\Db;

class Secret extends Byse

{

    public function index(){

        // $this-> islogin();

        $id = input('id');

        $uid = USER_ID;

        $res = Db::name('app')

        ->where('id',$id)

        ->find();

        if(empty($id)||empty($res)||LINK!=$res['url']){

            $this->error('页面错误');

            exit;

        }

        $this->assign('title',$res['title']);

        $this->assign('app',$res);

        return $this->fetch(); 

    }



    public function add(){

        $this-> islogin();

        $id = input('id');

        $res = Db::name('app')

        ->where('id',$id)

        ->find();

        if(empty($id)||empty($res)){

            $this->error('页面错误');

            exit;

        }

        $this->assign('app',$res);

        $this->assign('title',$res['title'].'—添加');

        return $this->fetch();

    }



    public function img(){  //上传文件

        $file = request()->file('img');

        if($file){

           $info = $file->move(ROOT_PATH . 'public' . DS . 'home','');

           if($info){

               $path = $info->getSaveName();

               return ['code'=>1,'info'=>['path'=>'/home/'.$path,'msg'=>'上传成功']];

           }else{

               $error = $file->getError();

               return ['code'=>1,'info'=>['msg'=>$error]];

           }

       }

    }



    public function get_add(){

        $id = USER_ID; //用户id

        $time = time();

        $all = input('param.');

        $all['uid'] = $id;

        $all['time'] = $time;

        $return = Db::name('secret')

        ->insert($all);

        if($return == 1){

            return ['status'=>1,'info'=>'添加成功'];

         }else{

            return ['status'=>0,'info'=>'添加错误'];

         }

    }



    public function get_edit(){

        $id = input('id'); 

        $time = time();

        $all = input('param.');

        $all['time'] = $time;

        $return = Db::name('secret')

        ->where('id',$id)

        ->update($all);

        if($return == 1){

            return ['status'=>1,'info'=>'更新成功'];

         }else{

            return ['status'=>0,'info'=>'更新错误'];

         }

    }



    public function get_del(){  //删除秘语

        $id = input('id'); 

        $sql = "DELETE a.*,b.*,c.* FROM by_secret as a LEFT JOIN by_secpl AS b ON b.zid=a.id  LEFT   JOIN by_seclike AS c ON c.zid=a.id WHERE a.id=".$id;

        $return = Db::execute($sql);  //多表删除

        if($return){

            return ['status'=>1,'info'=>'删除成功'];

         }else{

            return ['status'=>0,'info'=>'删除错误','error'=>$return];

         }

    }



    public function get_data(){ //我的秘语

            $uid = USER_ID;

            $page_size = 5;  //每页显示条数

            $count = Db::name('secret')->where('uid',$uid)->count();  //总记录数

            $data['total_page'] = ceil($count/$page_size);  //总页数

            $cur_page = intval(input('page'))-1;  //默认前端page传过来为1 

            $data['res'] = Db::name('secret')

                ->where('uid',$uid)

                ->order('id desc')

                ->limit(($cur_page*$page_size),$page_size)  //limit默认要从零开始

                ->select();

            foreach($data['res'] as $k => $v){ 

                $data['res'][$k]['content'] =  mb_substr($v['content'], 0, 50, 'utf-8'); //只截取前100字

            }

            return json($data);  //返回json

    }



    public function get_garden(){ //秘语花园

        $uid = USER_ID;

        $page_size = 5;  //每页显示条数

        $count = Db::name('secret')->where('garden',1)->count();  //总记录数

        $data['total_page'] = ceil($count/$page_size);  //总页数

        $cur_page =  (input('page'))-1;  //默认前端page传过来为1 

        $data['res'] = Db::name('secret')

            ->alias('a')

            ->join('by_user i','a.uid = i.id')

            ->field('a.id,a.img,a.content,a.time,a.garden,i.user,i.nickname')

            ->where('a.garden',1)

            ->limit(($cur_page*$page_size),$page_size)  //limit默认要从零开始

            ->order('a.id desc')

            ->select();

        foreach($data['res'] as $k => $v){ 

            $data['res'][$k]['content'] =  mb_substr($v['content'], 0, 50, 'utf-8'); //只截取前100字

        }

        return json($data);  //返回json

    }



    public function oper(){ //微语详情

    //    $this->islogin();

       $id = input('id');

       $garden = input('garden');

       if(empty($id)||$garden==''){

        $this->error('页面错误');

        exit;

       }else{

        $return = Db::name('secret')

        ->where('id',$id)

        ->find();

        $user = User::get($return['uid']);

        $return['nickname'] = $user->nickname;

        $return['headimg'] = $user->headimg;

        if(!$return){

            $this->error('页面错误');

            exit;

        }
        if(USER_ID==-1){
            
          $this->assign('res',$return);  

          $this->assign('gar',$garden); 

        }
        if($garden==1&&$return['garden']==$garden||$garden==0&&$return['uid']==USER_ID){

          $this->assign('res',$return);  

          $this->assign('gar',$garden); 

        }else{

            $this->error('页面错误');

            exit;

        } 

       }

       if($garden == 1){

        $res = Db::name('seclike')

        ->where('uid',USER_ID)

        ->where('zid',$id)

        ->where('type','ready')

        ->find();

       if($res){

          $resnum = Db::name('seclike')

          ->where('uid',USER_ID)

          ->where('zid',$id)

          ->where('type','ready')

          ->setInc('num', 1);

       }else{

           $data = [

               'uid' => USER_ID,

               'zid' => $id,

               'type' => 'ready',

               'time' => time()

           ];

           $resnum = Db::name('seclike')

           ->insert($data);

        }

       }

       $readynum = Db::name('seclike')

       ->where('zid',$id)

       ->where('type','ready')

       ->sum('num');

       $likenum = Db::name('seclike')

       ->where('zid',$id)

       ->where('type','like')

       ->sum('num');

       $like = Db::name('seclike')

       ->where('uid',USER_ID)

       ->where('zid',$id)

       ->where('type','like')

       ->find();

        $this->assign('like',$like['num']);

        $this->assign('readynum',$readynum);

        $this->assign('likenum',$likenum);

        $this->assign('title','来自'.$user->nickname.'的秘语');
        return $this->fetch();

    }

    public function get_like(){  //点赞查看

        $id = input('id');

        $res = Db::name('seclike')

        ->where('uid',USER_ID)

        ->where('zid',$id)

        ->where('type','like')

        ->find();

        if($res){

            $del = Db::name('seclike')

            ->where('uid',USER_ID)

            ->where('zid',$id)

            ->where('type','like')

            ->delete();

            if($del){

                return ['status'=>1,'info'=>'取消喜欢成功','code'=>0];

            }else{                

                return ['status'=>0,'info'=>'取消喜欢错误'];

            }

        }else{

            $data = [

                'uid' => USER_ID,

                'zid' => $id,

                'type' => 'like',

                'num' => 1,

                'time' => time()

            ];

            $add = Db::name('seclike')

            ->insert($data);

            if($add){

                return ['status'=>1,'info'=>'喜欢成功','code'=>1];

            }else{                

                return ['status'=>0,'info'=>'喜欢错误'];

            }

        }

    }

    public function get_pl(){ //提交评论

        $all = input('param.');

        if(empty(trim($all['content']))){

            return ['status'=>0,'info'=>'填写完整'];

        }

        $all['uid'] = USER_ID;

        $all['time'] = time();

        $user = User::get(USER_ID);

        $res = Db::name('secpl')

        ->insert($all);

        $pid = Db::name('secpl')->getLastInsID();  //新增评论id

        if($res == 1){

            return ['status'=>1,'info'=>'评论成功','user'=>['id'=>$user->id,'nickname'=>$user->nickname,'headimg'=>$user->headimg],'pid'=>$pid,'time'=>date('Y-m-d H:i:s',$all['time'])];

         }else{

            return ['status'=>0,'info'=>'评论错误'];

         }

    }

    public function get_delpl($id){  //删除评论
        $res = Db::name('secpl')
        ->delete($id);
        if($res != 0){

            return ['status'=>1,'info'=>'删除成功'];

        }else{                

            return ['status'=>0,'info'=>'删除错误'];

        }
    }


    public function get_comment(){ //评论列表

        $uid = USER_ID;

        $zid = input('id');

        $page_size = 5;  //每页显示条数

        $count = Db::name('secpl')->where('zid',$zid)->count();  //总记录数

        $data['total_page'] = ceil($count/$page_size);  //总页数

        $cur_page =  (input('page'))-1;  //默认前端page传过来为1 

        $data['res'] = Db::name('secpl')

            ->alias('a')

            ->join('by_user i','a.uid = i.id')

            ->field('a.id,a.uid,a.content,a.time,i.user,i.nickname,i.headimg')

            ->where('a.zid',$zid)

            ->limit(($cur_page*$page_size),$page_size)  //limit默认要从零开始

            ->order('a.id desc')

            ->select();

        return json($data);  //返回json

    }

}