<?php

namespace Home\Controller;

use Think\Controller;
use Home\Service\InitInvertoryService;

class InitInvertoryController extends Controller {

    public function warehouseList() {
        if (IS_POST) {
			$is = new InitInvertoryService();
            $this->ajaxReturn($is->warehouseList());
        }
    }

    public function initInfoList() {
        if (IS_POST) {
            $params = array(
                "warehouseId" => I("post.warehouseId"),
                "page" => I("post.page"),
                "start" => I("post.start"),
                "limit" => I("post.limit")
            );
			$is = new InitInvertoryService();
            $this->ajaxReturn($is->initInfoList($params));
        }
    }

    public function goodsCategoryList() {
        if (IS_POST) {
			$is = new InitInvertoryService();
            $this->ajaxReturn($is->goodsCategoryList());
        }
    }

    public function goodsList() {
        if (IS_POST) {
            $params = array(
                "warehouseId" => I("post.warehouseId"),
                "categoryId" => I("post.categoryId"),
                "page" => I("post.page"),
                "start" => I("post.start"),
                "limit" => I("post.limit")
            );
			$is = new InitInvertoryService();
            $this->ajaxReturn($is->goodsList($params));
        }
    }

    public function commitInitInvertoryGoods() {
        if (IS_POST) {
            $params = array(
                "warehouseId" => I("post.warehouseId"),
                "goodsId" => I("post.goodsId"),
                "goodsCount" => I("post.goodsCount"),
                "goodsMoney" => I("post.goodsMoney")
            );
			$is = new InitInvertoryService();
            $this->ajaxReturn($is->commitInitInvertoryGoods($params));
        }
    }

    public function finish() {
        if (IS_POST) {
            $params = array(
                "warehouseId" => I("post.warehouseId"),
            );
			$is = new InitInvertoryService();
            $this->ajaxReturn($is->finish($params));
        }
    }
    
    public function cancel() {
        if (IS_POST) {
            $params = array(
                "warehouseId" => I("post.warehouseId"),
            );
			$is = new InitInvertoryService();
            $this->ajaxReturn($is->cancel($params));
        }
    }
}
