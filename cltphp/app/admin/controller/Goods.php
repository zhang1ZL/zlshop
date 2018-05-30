<?php
namespace app\admin\controller;
use think\Db;
use clt\Leftnav;
use app\admin\model\Admin;
use app\admin\model\AuthGroup;
use app\admin\model\authRule;
use think\Validate;
use think\Request;
/*
 * 商品 列表    发布
 * 
 * */
class Goods extends Common{
	public function goodsList(){//商品列表
		return $this->fetch('goodsList');		
	}
	public function getGoods(){
		$starttime = trim(input('get.start'));
		$endtime = trim(input('get.end'));
		$condition = trim(input('get.condition'));
		$where['gtime'] = $this->optime($starttime,$endtime);
		if(!empty($condition)){
			$where['gname | gnumber | gclass_id'] = array('like',"%$condition%");
		}
		$data = Db::name('goods')->where($where)->alias('a')
		->join('admin b','b.admin_id = a.uid')
		//->join('classify c,c.id = a.gclass_id')
		->field('id,gnumber,gname,gtitle,price,state,check,gtime')
		->page(input('get.page').",".input('get.limit'))->select();
		$count = Db::name('goods')->where($where)->count();
		foreach($data as $k=>$v){
			$data[$k]['gtime'] = date('Y-m-d H:s:i',$v['gtime']);
		}
		return json(['code'=>1,'data'=>$data,'count'=>$count]);
	}
	/*==================  商品操作  功能    =====================*/
	public function addGoods(){//添加商品   商品发布
		if(Request::instance()->post()){
			$gnum = Db::name('goods')->count();
			$gnum += 1;
			$lang = strlen($gnum);
			if($lang < 5){
				for($i = 0;$i < (5-$lang);$i++){
					$zero .= "0";
				}
				$gnumber = date('Ymd',time()).$zero.$gnum;
			}else if($lang == 5){
				$gnumber = date('Ymd',time()).$gnum;
			}else{
				$gnumber = time();
			}
  			$set_meal = implode(',',input('post.set_meal/a'));
			$goods = array(
				'gname'=>trim(input('post.gname')),//商品名
				'gtitle'=>input('post.gtitle'),//商品标题
				'gnumber'=>$gnumber,//商品编号
				'ginfo' =>input('post.ginfo'),//商品简介
				'details'=>input('post.details'),//商品详情
				'price'=>trim(input('post.price')),//商品单价
				'price_cost'=>trim(input('post.price_cost')),//商品原价
				'gclass_id'=>input('post.gclass_id'),//商品分类ID
				'set_meal'=>$set_meal,//商品套餐ID
				'state'=>input('post.state'),//商品状态
				'check'=>0,//是否审核
				'uid'=>session('admin_id'),//创建人ID
				'gtime'=>time(),//创建时间
				'attr'=>trim(input('post.attr')),//属性
				'spec'=>trim(input('post.spec'))//规格
			);
			$validate = Db::name('goods')->insert($goods);
			if($validate){
				return $this->success('商品发布成功','goodsList');
			}else{
				return $this->error('商品发布失败，返回上次访问页面中...');
			}
		}else{
			return $this->fetch();
		}	
	}
	public function editGoods(){//修改商品
		if(Request::instance()->post()){
			
		}else{
			$id = input('get.gid');
			$goods = Db::name('goods')->where('id',$id)->select();
			$this->assign('goods',$goods);
			return $this->fetch();
		}
	}
	public function delGoods(){//删除商品
		
	}
	public function stateGoods(){//商品状态
		
	}
	public function checkGoods(){// 商品审核
		 
	}
	public function classGoods(){//商品分类  选择
		
	}
	public function skuGoods(){//商品  属性  规格  选择
		
	}
	/*==================  商品套餐操作  功能   =====================*/
	public function packageGoods(){//选择套餐
		
	}
	public function addPackage(){//添加套餐
		
	}
	public function editPackage(){//修改套餐
		
	}	
}
?>