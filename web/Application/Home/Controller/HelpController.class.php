<?php

namespace Home\Controller;

use Home\Service\BizlogService;

/**
 * 帮助Controller
 *
 * @author 李静波
 *        
 */
class HelpController extends PSIBaseController {

	public function index() {
		$bs = new BizlogService();
		
		$key = I("get.t");
		switch ($key) {
			case "login" :
				// 用户直接访问登录帮助的时候，多半还没有登录所以没法记录业务日志
				redirect("/help/10.html");
				break;
			case "user" :
				$bs->insertBizlog("访问帮助页面：用户管理", "帮助");
				redirect("/help/02-01.html");
				break;
			case "priceSystem" :
				$bs->insertBizlog("访问帮助页面：价格体系", "帮助");
				redirect("/help/02-04-03.html");
				break;
			case "initInv" :
				$bs->insertBizlog("访问帮助页面：库存建账", "帮助");
				redirect("/help/02-06.html");
				break;
			case "permission" :
				$bs->insertBizlog("访问帮助页面：权限管理", "帮助");
				redirect("/help/02-02.html");
				break;
			case "bizlog" :
				$bs->insertBizlog("访问帮助页面：业务日志", "帮助");
				redirect("/help/03.html");
				break;
			case "warehouse" :
				$bs->insertBizlog("访问帮助页面：仓库", "帮助");
				redirect("/help/02-05.html");
				break;
			case "goods" :
				$bs->insertBizlog("访问帮助页面：商品", "帮助");
				redirect("/help/02-04.html");
				break;
			case "goodsBrand" :
				$bs->insertBizlog("访问帮助页面：商品品牌", "帮助");
				redirect("/help/02-04-02.html");
				break;
			case "goodsUnit" :
				$bs->insertBizlog("访问帮助页面：商品计量单位", "帮助");
				redirect("/help/02-04-01.html");
				break;
			default :
				$bs->insertBizlog("通过主菜单进入帮助页面", "帮助");
				redirect("/help");
		}
	}
}