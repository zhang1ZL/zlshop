<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use think\Controller;
class Common extends Controller
{
    protected $mod,$role,$system,$nav,$menudata,$cache_model,$categorys,$module,$moduleid,$adminRules,$HrefId;
    public function _initialize()
    {

        //判断管理员是否登录
        if (!session('aid')) {
            $this->redirect('login/index');
        }
        define('MODULE_NAME',strtolower(request()->controller()));//获取访问的控制器     strtolower：把所有字符转换为小写
        define('ACTION_NAME',strtolower(request()->action()));//获取访问的方法
        //权限管理
        //当前操作权限ID
        if(session('aid')!=1){
            $this->HrefId = db('auth_rule')->where('href',MODULE_NAME.'/'.ACTION_NAME)->value('id');
            //当前管理员权限
            $map['a.admin_id'] = session('aid');
            $rules=Db::table(config('database.prefix').'admin')->alias('a')
                ->join(config('database.prefix').'auth_group ag','a.group_id = ag.group_id','left')
                ->where($map)
                ->value('ag.rules');
            $this->adminRules = explode(',',$rules);
            if($this->HrefId){
                if(!in_array($this->HrefId,$this->adminRules)){
                    $this->error('您无此操作权限','index');
                }
            }
        }
        $this->system = F('System');
        $this->categorys = F('Category');
        $this->module = F('Module');
        $this->mod = F('Mod');
        $this->role = F('Role');
        $this->cache_model=array('Module','Role','Category','Posid','Field','System');
        if(empty($this->system)){
            foreach($this->cache_model as $r){
                savecache($r);
            }
        }
    }
    //空操作
    public function _empty(){
        return $this->error('空操作，返回上次访问页面中...');
    }
	public function optime($starttime,$endtime){
		if ($starttime != '') {
            $starttime  = trim(strtotime($starttime));
        } else {
            $starttime ='';
        }
        if ($endtime!= '') {
            $endtime = trim(strtotime($endtime))+24*3600;;
        } else {
            $endtime   ='';
        }
        if ($starttime && $endtime) {
            $time = array('between',array($starttime,$endtime));
        } else {
            if ($starttime) {
                $time = array('gt',$starttime);
            }
            if ($endtime) {
                $time = array('lt',$endtime);
            }
        }
		return $time;
	}
}
