<?php
namespace app\admin\controller;
use app\admin\controller\Byse;
use \think\Cookie;
use \think\Request;
use \think\Db;
class Index extends Byse
{
    public function index()
    {
       return $this->fetch();
    }
    public function welcome()
    {
      $info = array(
         'win'=>PHP_OS,
         'phpedit'=>PHP_VERSION,
         'open'=>$_SERVER["SERVER_SOFTWARE"],
         'phpmode'=>php_sapi_name(),
         'tpedit'=>THINK_VERSION.' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
         'annex'=>ini_get('upload_max_filesize'),
         'antime'=>ini_get('max_execution_time').'秒',
         'servertime'=>date("Y年n月j日 H:i:s"),
         '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
         'servername'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
         'space'=>round((disk_free_space(".")/(1024*1024)),2).'M',
         'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
         'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
         'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
     );
      $appnum = Db::name('app')
      ->count('id');
      $membernum = Db::name('user')
      ->count('id');
       $this->assign('appnum',$appnum);
       $this->assign('membernum',$membernum);
       $this->assign('sys',$info);
       return $this->fetch();
    }

    public function welcome1(){
      return $this->fetch();
    }

    public function delcookie(){
         Cookie::clear('ad_');
         header('Location:/admin/login/index');
    }
}
