<?php

namespace Home\Service;

require __DIR__ . '/../Common/Excel/PHPExcel.php';
require __DIR__ . '/../Common/Excel/PHPExcel/Reader/Excel5.php';
require __DIR__ . '/../Common/Excel/PHPExcel/Reader/Excel2007.php';

/**
 * PHPExcel文件 Service
 *
 * @author James(张健)
 */
class GoodsImportService {

	public function importGoodsFromExcelFile($excelFilename, $ext) {
		// $PHPExcel=new \PHPExcel();
		
		// 默认xlsx
		$PHPReader = new \PHPExcel_Reader_Excel2007();
		// 如果excel文件后缀名为.xls，导入这个类
		if ($ext == 'xls') {
			$PHPReader = new \PHPExcel_Reader_Excel5();
		}
		$ps = new PinyinService();
		$idGen = new IdGenService();
		$bs = new BizlogService();
		$gs = new GoodsService();
		
		// $py = $ps->toPY($name);
		// 载入文件
		$PHPExcel = $PHPReader->load($excelFilename);
		// 获取表中的第一个工作表
		$currentSheet = $PHPExcel->getSheet(0);
		/**
		 * 单元格定义
		 * A 商品分类编码
		 * B 商品编码
		 * C 商品名称
		 * D 规格型号
		 * E 计量单位
		 * F 销售单价
		 * G 建议采购单价
		 * H 条形码
		 */
		// 获取总行数
		$allRow = $currentSheet->getHighestRow();
		$message = " ";
		// 从第一行获取数据
		for($currentRow = 2; $currentRow <= $allRow; $currentRow ++) {
			// 数据坐标
			$index_category = 'A' . $currentRow;
			$index_code = 'B' . $currentRow;
			$index_name = 'C' . $currentRow;
			$index_spec = 'D' . $currentRow;
			$index_unit = 'E' . $currentRow;
			$index_sale_price = 'F' . $currentRow;
			$index_purchase_price = 'G' . $currentRow;
			$index_barcode = 'H' . $currentRow;
			// 读取到的数据，保存到数组$arr中
			$catagory = $currentSheet->getCell($index_category)->getValue();
			$code = $currentSheet->getCell($index_code)->getValue();
			$name = $currentSheet->getCell($index_name)->getValue();
			$spec = $currentSheet->getCell($index_spec)->getValue();
			$unit = $currentSheet->getCell($index_unit)->getValue();
			$sale_price = $currentSheet->getCell($index_sale_price)->getValue();
			$purchase_price = $currentSheet->getCell($index_purchase_price)->getValue();
			$barcode = $currentSheet->getCell($index_barcode)->getValue();
			
			// 如果为空则直接读取下一条记录
			if (! $catagory || ! $code || ! $name || ! $unit)
				continue;
			
			$unitId = null;
			$categoryId = null;
			
			$db = M();
			$sql = "select id, name from t_goods_unit where name = '%s' ";
			$data = $db->query($sql, $unit);
			if (! $data) {
				// 新增计量单位
				$newUnitParams = array(
						name => $unit
				);
				$newUnit = $gs->editUnit($newUnitParams);
				$unitId = $newUnit["id"];
			} else {
				$unitId = $data[0]["id"];
			}
			
			$sql = "select id, name from t_goods_category where code = '%s' ";
			$data = $db->query($sql, $catagory);
			if (! $data) {
				// 新增分类
				continue;
			} else {
				$categoryId = $data[0]["id"];
			}
			
			// 新增
			// 检查商品编码是否唯一
			$sql = "select 1  from t_goods where code = '%s' ";
			$data = $db->query($sql, $code);
			if ($data) {
				$message .= "商品: 商品编码 = {$code}, 品名 = {$name}, 规格型号 = {$spec} 已存在; \r\n";
				continue;
			}
			
			// 如果录入了条形码，则需要检查条形码是否唯一
			if ($barcode) {
				$sql = "select 1  from t_goods where bar_code = '%s' ";
				$data = $db->query($sql, $barcode);
				if ($data) {
					$message .= "商品: 商品编码 = {$code}, 品名 = {$name}, 规格型号 = {$spec}，条形码 = {$barcode} 已存在;\r\n";
					continue;
				}
			}
			
			$id = $idGen->newId();
			$py = $ps->toPY($name);
			
			$sql = "insert into t_goods (id, code, name, spec, category_id, unit_id, sale_price,
						py, purchase_price, bar_code)
					values ('%s', '%s', '%s', '%s', '%s', '%s', %f, '%s', %f, '%s')";
			$db->execute($sql, $id, $code, $name, $spec, $categoryId, $unitId, $sale_price, $py, 
					$purchase_price, $barcode);
			
			$log = "导入方式新增商品: 商品编码 = {$code}, 品名 = {$name}, 规格型号 = {$spec}";
			// $message = $message."导入方式新增商品: 商品编码 = {$code}, 品名 = {$name}, 规格型号 = {$spec}";
			$bs->insertBizlog($log, "基础数据-商品");
		}
		
		$result = array(
				msg => $message,
				success => true
		);
		
		return $result;
	}
}