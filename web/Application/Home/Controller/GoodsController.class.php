<?php

namespace Home\Controller;

use Home\Common\FIdConst;
use Home\Service\GoodsService;
use Home\Service\ImportService;
use Home\Service\UserService;

/**
 * ��ƷController
 *
 * @author ���
 *        
 */
class GoodsController extends PSIBaseController {

	/**
	 * ��Ʒ��ҳ��
	 */
	public function index() {
		$us = new UserService();
		
		if ($us->hasPermission(FIdConst::GOODS)) {
			$this->initVar();
			
			$this->assign("title", "��Ʒ");
			
			$this->assign("pAddCategory", $us->hasPermission(FIdConst::GOODS_CATEGORY_ADD) ? 1 : 0);
			$this->assign("pEditCategory", 
					$us->hasPermission(FIdConst::GOODS_CATEGORY_EDIT) ? 1 : 0);
			$this->assign("pDeleteCategory", 
					$us->hasPermission(FIdConst::GOODS_CATEGORY_DELETE) ? 1 : 0);
			$this->assign("pAddGoods", $us->hasPermission(FIdConst::GOODS_ADD) ? 1 : 0);
			$this->assign("pEditGoods", $us->hasPermission(FIdConst::GOODS_EDIT) ? 1 : 0);
			$this->assign("pDeleteGoods", $us->hasPermission(FIdConst::GOODS_DELETE) ? 1 : 0);
			$this->assign("pImportGoods", $us->hasPermission(FIdConst::GOODS_IMPORT) ? 1 : 0);
			$this->assign("pGoodsSI", $us->hasPermission(FIdConst::GOODS_SI) ? 1 : 0);
			
			$this->assign("pAddBOM", $us->hasPermission(FIdConst::GOODS_BOM_ADD) ? 1 : 0);
			$this->assign("pEditBOM", $us->hasPermission(FIdConst::GOODS_BOM_EDIT) ? 1 : 0);
			$this->assign("pDeleteBOM", $us->hasPermission(FIdConst::GOODS_BOM_DELETE) ? 1 : 0);
			
			$this->assign("pPriceSystem", 
					$us->hasPermission(FIdConst::PRICE_SYSTEM_SETTING_GOODS) ? 1 : 0);
			
			$this->display();
		} else {
			$this->gotoLoginPage("/Home/Goods/index");
		}
	}

	/**
	 * ��Ʒ������λ��ҳ��
	 */
	public function unitIndex() {
		$us = new UserService();
		
		if ($us->hasPermission(FIdConst::GOODS_UNIT)) {
			$this->initVar();
			
			$this->assign("title", "��Ʒ������λ");
			
			$this->display();
		} else {
			$this->gotoLoginPage("/Home/Goods/unitIndex");
		}
	}

	/**
	 * ������е���Ʒ������λ�б�
	 */
	public function allUnits() {
		if (IS_POST) {
			$gs = new GoodsService();
			$this->ajaxReturn($gs->allUnits());
		}
	}

	/**
	 * ������༭��Ʒ��λ
	 */
	public function editUnit() {
		if (IS_POST) {
			$params = array(
					"id" => I("post.id"),
					"name" => I("post.name")
			);
			$gs = new GoodsService();
			$this->ajaxReturn($gs->editUnit($params));
		}
	}

	/**
	 * ɾ����Ʒ������λ
	 */
	public function deleteUnit() {
		if (IS_POST) {
			$params = array(
					"id" => I("post.id")
			);
			$gs = new GoodsService();
			$this->ajaxReturn($gs->deleteUnit($params));
		}
	}

	/**
	 * �����Ʒ����
	 */
	public function allCategories() {
		if (IS_POST) {
			$gs = new GoodsService();
			$params = array(
					"code" => I("post.code"),
					"name" => I("post.name"),
					"spec" => I("post.spec"),
					"barCode" => I("post.barCode")
			);
			$this->ajaxReturn($gs->allCategories($params));
		}
	}

	/**
	 * ������༭��Ʒ����
	 */
	public function editCategory() {
		if (IS_POST) {
			$us = new UserService();
			if (I("post.id")) {
				// �༭��Ʒ����
				if (! $us->hasPermission(FIdConst::GOODS_CATEGORY_EDIT)) {
					$this->ajaxReturn($this->noPermission("�༭��Ʒ����"));
					return;
				}
			} else {
				// ������Ʒ����
				if (! $us->hasPermission(FIdConst::GOODS_CATEGORY_ADD)) {
					$this->ajaxReturn($this->noPermission("������Ʒ����"));
					return;
				}
			}
			
			$params = array(
					"id" => I("post.id"),
					"code" => I("post.code"),
					"name" => I("post.name"),
					"parentId" => I("post.parentId")
			);
			$gs = new GoodsService();
			$this->ajaxReturn($gs->editCategory($params));
		}
	}

	/**
	 * ���ĳ���������Ϣ
	 */
	public function getCategoryInfo() {
		if (IS_POST) {
			$params = array(
					"id" => I("post.id")
			);
			$gs = new GoodsService();
			$this->ajaxReturn($gs->getCategoryInfo($params));
		}
	}

	/**
	 * ɾ����Ʒ����
	 */
	public function deleteCategory() {
		if (IS_POST) {
			$us = new UserService();
			if (! $us->hasPermission(FIdConst::GOODS_CATEGORY_DELETE)) {
				$this->ajaxReturn($this->noPermission("ɾ����Ʒ����"));
				return;
			}
			
			$params = array(
					"id" => I("post.id")
			);
			$gs = new GoodsService();
			$this->ajaxReturn($gs->deleteCategory($params));
		}
	}

	/**
	 * �����Ʒ�б�
	 */
	public function goodsList() {
		if (IS_POST) {
			$params = array(
					"categoryId" => I("post.categoryId"),
					"code" => I("post.code"),
					"name" => I("post.name"),
					"brandcode" => I("post.brandCode"),
					"spec" => I("post.spec"),
					"oldspec" => I("post.oldSpec"),
					"chicun" => I("post.chiCun"),
					"barCode" => I("post.barCode"),
					"page" => I("post.page"),
					"start" => I("post.start"),
					"limit" => I("post.limit")
			);
			$gs = new GoodsService();
			$this->ajaxReturn($gs->goodsList($params));
		}
	}

	/**
	 * ������༭��Ʒ
	 */
	public function editGoods() {
		if (IS_POST) {
			$us = new UserService();
			if (I("post.id")) {
				// �༭��Ʒ
				if (! $us->hasPermission(FIdConst::GOODS_EDIT)) {
					$this->ajaxReturn($this->noPermission("�༭��Ʒ"));
					return;
				}
			} else {
				// ������Ʒ
				if (! $us->hasPermission(FIdConst::GOODS_ADD)) {
					$this->ajaxReturn($this->noPermission("������Ʒ"));
					return;
				}
			}
			
			$params = array(
					"id" => I("post.id"),
					"categoryId" => I("post.categoryId"),
					"code" => I("post.code"),
					"name" => I("post.name"),
					"brandcode" => I("post.brandCode"),
					"spec" => I("post.spec"),
					"oldspec" => I("post.oldSpec"),
					"chicun" => I("post.chiCun"),
					"unitId" => I("post.unitId"),
					"salePrice" => I("post.salePrice"),
					"purchasePrice" => I("post.purchasePrice"),
					"barCode" => I("post.barCode"),
					"brandId" => I("post.brandId"),
					"memo" => I("post.memo")
			);
			$gs = new GoodsService();
			$this->ajaxReturn($gs->editGoods($params));
		}
	}

	/**
	 * ɾ����Ʒ
	 */
	public function deleteGoods() {
		if (IS_POST) {
			$us = new UserService();
			if (! $us->hasPermission(FIdConst::GOODS_DELETE)) {
				$this->ajaxReturn($this->noPermission("ɾ����Ʒ"));
				return;
			}
			
			$params = array(
					"id" => I("post.id")
			);
			$gs = new GoodsService();
			$this->ajaxReturn($gs->deleteGoods($params));
		}
	}

	/**
	 * ��Ʒ�Զ����ֶΣ���ѯ����
	 */
	public function queryData() {
		if (IS_POST) {
			$queryKey = I("post.queryKey");
			$gs = new GoodsService();
			$this->ajaxReturn($gs->queryData($queryKey));
		}
	}

	/**
	 * ��Ʒ�Զ����ֶΣ���ѯ����
	 */
	public function queryDataWithSalePrice() {
		if (IS_POST) {
			$queryKey = I("post.queryKey");
			$customerId = I("post.customerId");
			$gs = new GoodsService();
			$this->ajaxReturn($gs->queryDataWithSalePrice($queryKey, $customerId));
		}
	}

	/**
	 * ��Ʒ�Զ����ֶΣ���ѯ����
	 */
	public function queryDataWithPurchasePrice() {
		if (IS_POST) {
			$queryKey = I("post.queryKey");
			$gs = new GoodsService();
			$this->ajaxReturn($gs->queryDataWithPurchasePrice($queryKey));
		}
	}

	/**
	 * ��ѯĳ����Ʒ����Ϣ
	 */
	public function goodsInfo() {
		if (IS_POST) {
			$id = I("post.id");
			$categoryId = I("post.categoryId");
			$gs = new GoodsService();
			$data = $gs->getGoodsInfo($id, $categoryId);
			$data["units"] = $gs->allUnits();
			$this->ajaxReturn($data);
		}
	}

	/**
	 * �����Ʒ�İ�ȫ�����Ϣ
	 */
	public function goodsSafetyInventoryList() {
		if (IS_POST) {
			$params = array(
					"id" => I("post.id")
			);
			$gs = new GoodsService();
			$this->ajaxReturn($gs->goodsSafetyInventoryList($params));
		}
	}

	/**
	 * ���ð�ȫ���ʱ�򣬲�ѯ��Ϣ
	 */
	public function siInfo() {
		if (IS_POST) {
			$params = array(
					"id" => I("post.id")
			);
			$gs = new GoodsService();
			$this->ajaxReturn($gs->siInfo($params));
		}
	}

	/**
	 * ���ð�ȫ���
	 */
	public function editSafetyInventory() {
		if (IS_POST) {
			$us = new UserService();
			if (! $us->hasPermission(FIdConst::GOODS_SI)) {
				$this->ajaxReturn($this->noPermission("������Ʒ��ȫ���"));
				return;
			}
			
			$params = array(
					"jsonStr" => I("post.jsonStr")
			);
			$gs = new GoodsService();
			$this->ajaxReturn($gs->editSafetyInventory($params));
		}
	}

	/**
	 * ���������룬��ѯ��Ʒ��Ϣ, ���۳��ⵥʹ��
	 */
	public function queryGoodsInfoByBarcode() {
		if (IS_POST) {
			$params = array(
					"barcode" => I("post.barcode")
			);
			$gs = new GoodsService();
			$this->ajaxReturn($gs->queryGoodsInfoByBarcode($params));
		}
	}

	/**
	 * ���������룬��ѯ��Ʒ��Ϣ, �ɹ���ⵥʹ��
	 */
	public function queryGoodsInfoByBarcodeForPW() {
		if (IS_POST) {
			$params = array(
					"barcode" => I("post.barcode")
			);
			$gs = new GoodsService();
			$this->ajaxReturn($gs->queryGoodsInfoByBarcodeForPW($params));
		}
	}

	/**
	 * ͨ��Excel������Ʒ
	 */
	public function import() {
		if (IS_POST) {
			$us = new UserService();
			if (! $us->hasPermission(FIdConst::GOODS_IMPORT)) {
				$this->ajaxReturn($this->noPermission("������Ʒ"));
				return;
			}
			
			$upload = new \Think\Upload();
			
			// �����ϴ����ļ���׺
			$upload->exts = array(
					'xls',
					'xlsx'
			);
			
			// ����·��
			$upload->savePath = '/Goods/';
			
			// ���ϴ��ļ�
			$fileInfo = $upload->uploadOne($_FILES['data_file']);
			if (! $fileInfo) {
				$this->ajaxReturn(
						array(
								"msg" => $upload->getError(),
								"success" => false
						));
			} else {
				$uploadFileFullPath = './Uploads' . $fileInfo['savepath'] . $fileInfo['savename']; // ��ȡ�ϴ����������ļ�·��
				$uploadFileExt = $fileInfo['ext']; // �ϴ��ļ���չ��
				
				$params = array(
						"datafile" => $uploadFileFullPath,
						"ext" => $uploadFileExt
				);
				$ims = new ImportService();
				$this->ajaxReturn($ims->importGoodsFromExcelFile($params));
			}
		}
	}

	/**
	 * ������е���Ʒ������
	 */
	public function getTotalGoodsCount() {
		if (IS_POST) {
			$params = array(
					"code" => I("post.code"),
					"name" => I("post.name"),
					"spec" => I("post.spec"),
					"barCode" => I("post.barCode")
			);
			
			$gs = new GoodsService();
			$this->ajaxReturn($gs->getTotalGoodsCount($params));
		}
	}

	/**
	 * ��ƷƷ����ҳ��
	 */
	public function brandIndex() {
		$us = new UserService();
		
		if ($us->hasPermission(FIdConst::GOODS_BRAND)) {
			$this->initVar();
			
			$this->assign("title", "��ƷƷ��");
			
			$this->display();
		} else {
			$this->gotoLoginPage("/Home/Goods/brandIndex");
		}
	}

	/**
	 * ������е�Ʒ��
	 */
	public function allBrands() {
		if (IS_POST) {
			$gs = new GoodsService();
			$this->ajaxReturn($gs->allBrands());
		}
	}

	/**
	 * ������༭��ƷƷ��
	 */
	public function editBrand() {
		if (IS_POST) {
			$params = array(
					"id" => I("post.id"),
					"name" => I("post.name"),
					"parentId" => I("post.parentId")
			);
			
			$gs = new GoodsService();
			$this->ajaxReturn($gs->editBrand($params));
		}
	}

	/**
	 * ���ĳ��Ʒ�Ƶ��ϼ�Ʒ��ȫ��
	 */
	public function brandParentName() {
		if (IS_POST) {
			$params = array(
					"id" => I("post.id")
			);
			
			$gs = new GoodsService();
			$this->ajaxReturn($gs->brandParentName($params));
		}
	}

	/**
	 * ɾ����ƷƷ��
	 */
	public function deleteBrand() {
		if (IS_POST) {
			$params = array(
					"id" => I("post.id")
			);
			
			$gs = new GoodsService();
			$this->ajaxReturn($gs->deleteBrand($params));
		}
	}

	/**
	 * ĳ����Ʒ����Ʒ����
	 */
	public function goodsBOMList() {
		if (IS_POST) {
			$params = array(
					"id" => I("post.id")
			);
			
			$gs = new GoodsService();
			$this->ajaxReturn($gs->goodsBOMList($params));
		}
	}

	/**
	 * ������༭��Ʒ����
	 */
	public function editGoodsBOM() {
		if (IS_POST) {
			$us = new UserService();
			if (I("post.id")) {
				// �༭
				if (! $us->hasPermission(FIdConst::GOODS_BOM_EDIT)) {
					$this->ajaxReturn($this->noPermission("�༭����Ʒ"));
					return;
				}
			} else {
				if (! $us->hasPermission(FIdConst::GOODS_BOM_ADD)) {
					$this->ajaxReturn($this->noPermission("�½�����Ʒ"));
					return;
				}
			}
			
			$params = array(
					"id" => I("post.id"),
					"addBOM" => I("post.addBOM"),
					"subGoodsId" => I("post.subGoodsId"),
					"subGoodsCount" => I("post.subGoodsCount")
			);
			
			$gs = new GoodsService();
			$this->ajaxReturn($gs->editGoodsBOM($params));
		}
	}

	/**
	 * ����Ʒ�ֶΣ���ѯ����
	 */
	public function queryDataForSubGoods() {
		if (IS_POST) {
			$params = array(
					"queryKey" => I("post.queryKey"),
					"parentGoodsId" => I("post.parentGoodsId")
			);
			
			$gs = new GoodsService();
			$this->ajaxReturn($gs->queryDataForSubGoods($params));
		}
	}

	/**
	 * ��ѯ����Ʒ����ϸ��Ϣ
	 */
	public function getSubGoodsInfo() {
		if (IS_POST) {
			$params = array(
					"goodsId" => I("post.goodsId"),
					"subGoodsId" => I("post.subGoodsId")
			);
			
			$gs = new GoodsService();
			$this->ajaxReturn($gs->getSubGoodsInfo($params));
		}
	}

	/**
	 * ɾ����Ʒ�����е�����Ʒ
	 */
	public function deleteGoodsBOM() {
		if (IS_POST) {
			$us = new UserService();
			if (! $us->hasPermission(FIdConst::GOODS_BOM_DELETE)) {
				$this->ajaxReturn($this->noPermission("ɾ������Ʒ"));
				return;
			}
			
			$params = array(
					"id" => I("post.id")
			);
			
			$gs = new GoodsService();
			$this->ajaxReturn($gs->deleteGoodsBOM($params));
		}
	}

	/**
	 * �۸���ϵ - ��ҳ��
	 */
	public function psIndex() {
		$us = new UserService();
		
		if ($us->hasPermission(FIdConst::PRICE_SYSTEM)) {
			$this->initVar();
			
			$this->assign("title", "�۸���ϵ");
			
			$this->display();
		} else {
			$this->gotoLoginPage("/Home/Goods/psIndex");
		}
	}

	/**
	 * �۸���ϵ-�۸��б�
	 */
	public function priceSystemList() {
		if (IS_POST) {
			$gs = new GoodsService();
			$this->ajaxReturn($gs->priceSystemList());
		}
	}

	/**
	 * ������༭�۸���ϵ�еļ۸�
	 */
	public function editPriceSystem() {
		if (IS_POST) {
			$params = array(
					"id" => I("post.id"),
					"name" => I("post.name"),
					"factor" => I("post.factor")
			);
			$gs = new GoodsService();
			
			$this->ajaxReturn($gs->editPriceSystem($params));
		}
	}

	/**
	 * ɾ���۸���ϵ�еļ۸�
	 */
	public function deletePriceSystem() {
		if (IS_POST) {
			$params = array(
					"id" => I("post.id")
			);
			$gs = new GoodsService();
			
			$this->ajaxReturn($gs->deletePriceSystem($params));
		}
	}

	/**
	 * ��ѯĳ����Ʒ�����м۸���ϵ����ļ۸��б�
	 */
	public function goodsPriceSystemList() {
		if (IS_POST) {
			$params = array(
					"id" => I("post.id")
			);
			
			$gs = new GoodsService();
			$this->ajaxReturn($gs->goodsPriceSystemList($params));
		}
	}

	/**
	 * ��ѯĳ����Ʒ�ļ۸���ϵ�����м۸��ֵ
	 */
	public function goodsPriceSystemInfo() {
		if (IS_POST) {
			$params = array(
					"id" => I("post.id")
			);
			
			$gs = new GoodsService();
			$this->ajaxReturn($gs->goodsPriceSystemInfo($params));
		}
	}

	/**
	 * ������Ʒ�۸���ϵ�еļ۸�
	 */
	public function editGoodsPriceSystem() {
		if (IS_POST) {
			$params = array(
					"jsonStr" => I("post.jsonStr")
			);
			$gs = new GoodsService();
			$this->ajaxReturn($gs->editGoodsPriceSystem($params));
		}
	}
}