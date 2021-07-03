<?php
namespace vanhenry\manager\controller;
use Illuminate\Http\Request;
use Session;
use Illuminate\Database\Eloquent\Collection;
use PHPExcel;
use vanhenry\manager\helpers\DetailTableHelper;
use vanhenry\helpers\helpers\FCHelper;
use PHPExcel_IOFactory;

trait ExportTrait{
	private function _export_trait_CreateExcelObject(){
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Tech5s");
		$objPHPExcel->getProperties()->setLastModifiedBy("VTH");
		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");
		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");
		$objPHPExcel->getProperties()->setDescription("Export by CMS version 3 of Tech5s");
		return $objPHPExcel;
	}
	public function exportOrder(Request $request,$table) {
		$responses = new \stdClass();
		$fieldExports = \DB::table('v_detail_tables')->select(['name', 'note'])->where([['parent_name', $table], ['has_export_excel', 1]])->get();
		if(count($fieldExports) == 0){
			$responses->code = 20;
			$responses->message = 'Không có trường nào được lựa chọn để export excel';
			return response()->json($responses);
		}
		$select = [];
		foreach ($fieldExports as $key => $value) {
			$select[] = $value->name;
		}
		$realdata = \DB::table($table)->select($select)->orderBy('id', 'desc')->get();
		if (count($realdata) == 0) {
			$responses->code = 30;
			$responses->message = 'Không có dữ liệu để Export';
			return response()->json($responses);
		}
		$objPHPExcel = $this->_export_trait_CreateExcelObject();
		$sheet = $objPHPExcel->setActiveSheetIndex();

		$default_border = array(
			'style' => \PHPExcel_Style_Border::BORDER_THIN,
			'color' => array('rgb'=>'1006A3')
		);
		$style_header = array(
			'borders' => array(
				'bottom' => $default_border,
				'left' => $default_border,
				'top' => $default_border,
				'right' => $default_border,
			),
			'fill' => array(
				'type' => \PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'E1E0F7'),
			),
			'font' => array(
				'bold' => true,
				// 'size' => 13,
			),
			'alignment' => array(
				'vertical' => \PHPExcel_Style_Alignment::VERTICAL_TOP,
			)
		);
		$sheet->getStyle('A1:I1')->applyFromArray($style_header);
		for ($col = ord('a'); $col <= ord('i'); $col++)
		{
			// $sheet->getColumnDimension(chr($col))->setAutoSize(true);
			$sheet->getColumnDimension(chr($col))->setWidth(15);
		}
		$col=0;
		foreach($fieldExports as $field) {
			$sheet->setCellValueByColumnAndRow($col,1,FCHelper::ep($field,'note'));
			$col++;
		}
		for ($i=0; $i < count($realdata); $i++) {
			$tmpArr = (array)($realdata[$i]);
			$tmpKeys = array_keys($tmpArr);
			for ($j=0; $j < count($tmpKeys); $j++) { 
				if($tmpKeys[$j]!='id') {
					if ($table == 'register_events') {
						$cellValue = $this->convertData($tmpKeys[$j], $tmpArr[$tmpKeys[$j]]);
					}
					else{
						$cellValue = $tmpArr[$tmpKeys[$j]];
					}
					$sheet->setCellValueByColumnAndRow($j,$i+2, $cellValue);
				}
			}
		}
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('public/orders.xls');
		return redirect('public/orders.xls');
	}
	public function convertData($key, $value)
	{
		return $value;
	}
}