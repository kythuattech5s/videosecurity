<?php 

namespace vanhenry\manager\controller;

use vanhenry\manager\model\VConfigRegion;

use vanhenry\manager\model\Config;

use vanhenry\manager\model\VDetailTable;

use vanhenry\manager\model\TableProperty;

use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Collection ;

use DB;

use FCHelper;

use PHPExcel;

use PHPExcel_Writer_Excel2007;

use vanhenry\helpers\helpers\StringHelper;

use Illuminate\Support\Facades\Input;

use Illuminate\Support\Facades\Validator;

trait ImportTrait{

	private function _import_trait_CreateExcelObject(){

		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()->setCreator("Tech5s");

		$objPHPExcel->getProperties()->setLastModifiedBy("VTH");

		$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX Test Document");

		$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Test Document");

		$objPHPExcel->getProperties()->setDescription("Import by CMS version 3 of Tech5s");

		return $objPHPExcel;

	}

	private function _import_trait_Coordinates($x,$y){

		return \PHPExcel_Cell::stringFromColumnIndex($x).$y;

	}

	private function _import_trait_FileSample($table,$tableData,$tableDetailData){

		$tableParent = $tableData->table_parent;

		$path = "public/xls/".$table.".xlsx";

		// if(file_exists($path)){

		// 	return $path;

		// }

		$objPHPExcel = $this->_import_trait_CreateExcelObject();

		$objWorkSheet = $objPHPExcel->getActiveSheet();

		if(!StringHelper::isNull($tableParent)){

			$arrs = DB::table($tableParent)->where("act",1)->get();

			$objPHPExcel->setActiveSheetIndex(0);

			$objWorkSheet->setTitle('Cha');

			for($i=0;$i<count($arrs);$i++){

				$item = $arrs[$i];

				$objWorkSheet->setCellValueByColumnAndRow(0,$i+1,$item->name);

				$objWorkSheet->setCellValueByColumnAndRow(1,$i+1,$item->id);

			}

			$objWorkSheet = $objPHPExcel->createSheet(1); 	

		}

		$objWorkSheet->setTitle($table);



		$c = 0;

		$objWorkSheet->setCellValueByColumnAndRow($c,1,"STT");

		$c++;

		$exclude = array("id","ord","act","created_at","updated_at","trash");

		foreach ($tableDetailData as $key => $value) {

			if($value->act !=1 || in_array($value->name, $exclude) || strpos($value->name, "_count")!==FALSE || strpos($value->name, "_true")!==FALSE|| strpos($value->name, "_false")!==FALSE){

				continue;

			}

			if($value->name=="answer"){

				$objWorkSheet->setCellValueByColumnAndRow($c,1,"_answer_1");

				$objWorkSheet->setCellValueByColumnAndRow($c,2,"Đáp án 1");

				$c++;

				$objWorkSheet->setCellValueByColumnAndRow($c,1,"_answer_2");

				$objWorkSheet->setCellValueByColumnAndRow($c,2,"Đáp án 2");

				$c++;

				$objWorkSheet->setCellValueByColumnAndRow($c,1,"_answer_3");

				$objWorkSheet->setCellValueByColumnAndRow($c,2,"Đáp án 3");

				$c++;

				$objWorkSheet->setCellValueByColumnAndRow($c,1,"_answer_4");

				$objWorkSheet->setCellValueByColumnAndRow($c,2,"Đáp án 4");

				$c++;

				$objWorkSheet->setCellValueByColumnAndRow($c,1,"_answer_true");

				$objWorkSheet->setCellValueByColumnAndRow($c,2,"Đáp án đúng");

			}

			else{

				$objWorkSheet->setCellValueByColumnAndRow($c,1,$value->name);

				$objWorkSheet->setCellValueByColumnAndRow($c,2,$value->note);

				if(!StringHelper::isNull($tableParent)){

					if($value->name=="parent" || $value->name=="id_cate"){

						$cell = $this->_import_trait_Coordinates($c,2);



						$objValidation = $objWorkSheet->getCell($cell)->getDataValidation();

						$objValidation->setType( \PHPExcel_Cell_DataValidation::TYPE_LIST );

						$objValidation->setErrorStyle( \PHPExcel_Cell_DataValidation::STYLE_INFORMATION );

						$objValidation->setAllowBlank(false);

						$objValidation->setShowInputMessage(true);

						$objValidation->setShowErrorMessage(true);

						$objValidation->setShowDropDown(true);

						$objValidation->setErrorTitle('Nhập liệu sai');

						$objValidation->setError('Giá trị không chính xác.');

						$objValidation->setPromptTitle('Chọn giá trị');

						$objValidation->setPrompt('Chọn từ danh sách dưới.');

						$objValidation->setFormula1('=\'Cha\'!$A$1:$A$'.count($arrs));



						$c++;

						$objWorkSheet->setCellValueByColumnAndRow($c,1,$value->name."_id");

						$objWorkSheet->setCellValueByColumnAndRow($c,2,'=VLOOKUP('.$cell.',\'Cha\'!A1:B'.count($arrs).',2,0)');





					}

				}

			}



			

			$c++;

		}

		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

		$objWriter->save(public_path()."/xls/".$table.".xlsx");

		return $path;

	}

	private function _import_trait_GetExcelObject($path){

		$ret = array();

		if(file_exists($path)){

			$inputFileType = \PHPExcel_IOFactory::identify($path);

		    $objReader = \PHPExcel_IOFactory::createReader($inputFileType);

		    $objPHPExcel = $objReader->load($path);

		    $sheet = $objPHPExcel->getSheet(1); 

			$highestRow = $sheet->getHighestRow(); 

			$highestColumn = $sheet->getHighestColumn();

			

			$objPHPExcel->setActiveSheetIndex(1);



			// $cell = $this->_import_trait_Coordinates(3,3);

			// $this->_import_trait_ExcelFieldToHtml($objPHPExcel,$cell);





			//$cell =  $objPHPExcel->getActiveSheet()->getCell($cell);

//$style = $objPHPExcel->getActiveSheet()->getStyle($cell);

			// var_dump($cell->getValue());die;

			// var_dump($cell->getValue()->getRichTextElements());die;

			// $header = $sheet->rangeToArray('A1:' . $highestColumn . "1",

			//                                     NULL,

			//                                     TRUE,

			//                                     FALSE);

			// $header = count($header)>0?$header[0]:array();

			// for ($row = 3; $row <= $highestRow; $row++){

			//     $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,

			//                                     NULL,

			//                                     TRUE,

			//                                     FALSE);

			//     if(count($rowData)==0 ) continue;

			//     $rowData = $rowData[0];



			//     $tmp = array();

			//     for ($i=0; $i < count($header) ; $i++) { 

			//     	$value = $header[$i];

			//     	$tmp[$value] = $rowData[$i];

			//     }

			//     array_push($ret, $tmp);

			// }

			$header = $sheet->rangeToArray('A1:' . $highestColumn . "1",

			                                    NULL,

			                                    TRUE,

			                                    FALSE);

			$header = $header[0];



			$highestRow         = $sheet->getHighestRow(); // e.g. 10

		    $highestColumn      = $sheet->getHighestColumn(); // e.g 'F'

		    $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);

		    $nrColumns          = ord($highestColumn) - 64;

		    for ($row = 3; $row <= $highestRow; ++ $row) 

		    {

		    	$tmp = array();

		        for ($col = 1; $col < $highestColumnIndex; ++ $col) 

		        {

		        	// $cell = $sheet->getCellByColumnAndRow($col, $row);

		        	$value = $header[$col];

		        	$cell = $this->_import_trait_Coordinates($col,$row);

		        	$tmp[$value] = $this->_import_trait_ExcelFieldToHtml($objPHPExcel,$cell,$this->_import_trait_NeedCheckHtml($value));

		        }

		        array_push($ret,$tmp);

		    }

		}

		return $ret;

	}

	private function _import_trait_NeedCheckHtml($value){

		return $value=="content"||$value=="id_cate_id"||$value=="name"||$value=="explain"||(strpos($value,"_answer_")!==FALSE && $value!="_answer_true");

	}

	private function _import_trait_PreProcessImport($table,$data){

		if($table=="questions"){

			$ret = array();

			$dataans = array();

			if(count($data)<=0) return array();

			foreach ($data[0] as $key => $value) {

				if(strpos($key, "_answer_")===0 && $key!="_answer_true"){

					$tmp = preg_replace("/\D/", "", $key);

					$dataans[$key]= $tmp;

				}

			}

			foreach ($data as $key => $value) {

				$answerTrue = $value["_answer_true"];

				$ans = array();

				foreach ($dataans as $k =>$v) {

					if(!StringHelper::isNull($value[$k])){

						$obj1 = new \stdClass();

						$obj1->position = $v-1;

						$obj1->exact = $answerTrue == $v?1:0;

						$obj1->content = $value[$k];

						array_push($ans, $obj1);

					}

					unset($value[$k]);

				}



				unset($value[""]);

				unset($value["_answer_true"]);

				$value["answer"] = json_encode($ans);

				$value["answer_count"] = count($ans);



				$value["id_cate"] = $value["id_cate_id"];

				unset($value["id_cate_id"]);

				$value["act"] = 1;

				$value["type"] = 1;

				$value["ord"] = time();

				$value["created_at"] = new \DateTime();

				$value["updated_at"] = new \DateTime();

				$value["trash"]=0;

				

				// $detailTables = $this->_import_trait_getFieldTable($table);

				// foreach ($value as $dk => $dv) {

				// 	if(!in_array($dk, $detailTables))

				// 	{

				// 		unset($value[$dk]);

				// 	}

				// }

				array_push($ret, $value);

			}

			



			return $ret;



		}

		return $data;

	}

	private function _import_trait_EmmbeStyleCell($element){

		$cellData = "<span ";

		if($element instanceof \PHPExcel_RichText_Run){

			$font = $element->getFont();

            if($font->getColor()){

            	$color = $font->getColor();

            	$color = $color->getRGB();

            	$cellData .=" style='color:#".$color."'>";

            }

            else{

            	$cellData .=">";

            }

			if ($font->getSuperScript()) {

                $cellData .= '<sup>';

            } else if ($font->getSubScript()) {

                $cellData .= '<sub>';

            }

            if($font->getBold()){

            	$cellData .="<b>";

            }

            if($font->getItalic()){

            	$cellData .="<i>";

            }

            // if($font->getUnderline()){

            // 	$cellData .="<u>";

            // }

		}

		else{

			$cellData .=">";

		}

	 	$cellText = $element->getText();

        $cellData .= htmlspecialchars($cellText);



        if($element instanceof \PHPExcel_RichText_Run){

			$font = $element->getFont();

            // if($font->getUnderline()){

            // 	$cellData .="</u>";

            // }

            if($font->getItalic()){

            	$cellData .="</i>";

            }

            if($font->getBold()){

            	$cellData .="</b>";

            }

            if ($font->getSuperScript()) {

                $cellData .= '</sup>';

            } else if ($font->getSubScript()) {

                $cellData .= '</sub>';

            }

		}

		$cellData.="</span>";

		$cellData = nl2br($cellData);

		return $cellData;

	}

	private function _import_trait_EmmbeStyleCellString($style,$value){

		$font = $style->getFont();

		$cellData ="<span ";

        if($font->getColor()){

        	$color = $font->getColor();

        	$color = $color->getRGB();

        	$cellData .=" style='color:#".$color."'>";

        }

        else{

        	$cellData .=">";

        }

		if ($font->getSuperScript()) {

            $cellData .= '<sup>';

        } else if ($font->getSubScript()) {

            $cellData .= '<sub>';

        }

        if($font->getBold()){

        	$cellData .="<b>";

        }

        if($font->getItalic()){

        	$cellData .="<i>";

        }

    	$cellData .= htmlspecialchars($value);

        if($font->getItalic()){

        	$cellData .="</i>";

        }

        if($font->getBold()){

        	$cellData .="</b>";

        }

        if ($font->getSuperScript()) {

            $cellData .= '</sup>';

        } else if ($font->getSubScript()) {

            $cellData .= '</sub>';

        }

        $cellData.="<span>";

        $cellData = nl2br($cellData);

        return $cellData;

	}

	private function _import_trait_ExcelFieldToHtml($objPHPExcel,$_cell,$check = false){



		$cell =  $objPHPExcel->getActiveSheet()->getCell($_cell);

		$value = $cell->getValue();

		if(strpos($value, "=")===0)

		{

		    $value = $cell->getOldCalculatedValue();

		    return $value;

		}

		if(!$check){

			return $value;

		}

		if($cell->getDataType() == \PHPExcel_Cell_DataType::TYPE_FORMULA){

			return $cell->getOldCalculatedValue();

		}

		else{

			if ($value instanceof \PHPExcel_RichText) {

			    $elements = $cell->getValue()->getRichTextElements();

			    // var_dump($elements);die;

			    $cellData = "";

			    foreach ($elements as $element) {

			        $cellData.= $this->_import_trait_EmmbeStyleCell($element);

			    }

			    return $cellData;

			}

			else if(is_string($value)){

				$style = $objPHPExcel->getActiveSheet()->getStyle($_cell);

				$cellData = $this->_import_trait_EmmbeStyleCellString($style,$value);



				return $cellData;

			}

		}

		return $value;





	}

	public function do_import($table){

		$file = array('file_import' => Input::file('file_import'));

		$rules = array('file_import' => 'required');

		$validator = Validator::make($file, $rules);

		if ($validator->fails()) {

		    return redirect($this->admincp."/import/".$table)->with('error', 'Bạn chưa tải file lên');

		}

		else{

			if (Input::file('file_import')->isValid()) {

				$file = Input::file('file_import');

				$file_path = $file->getPathName();

				$name = $file->getClientOriginalName();

				$file->move(storage_path('xls'), $name);



				$path = storage_path("xls/".$name);

				$data = $this->_import_trait_GetExcelObject($path);

				$data = $this->_import_trait_PreProcessImport($table,$data);		

				DB::table($table)->insert($data);

				return redirect($this->admincp."/import/".$table)->with('error', 'Đã cập nhật!');

			}	

			else{

				return redirect($this->admincp."/import/".$table)->with('error', 'File không xác định!');

			}

		}



	}

	public function import($table){

		$tableDetailData = self::__getListDetailTable($table);

		$data['dataItem'] = array();

		$tableData = self::__getListTable()[$table];

		$data['tableData'] = new Collection($tableData);

		$data["file"]= $this->_import_trait_FileSample($table,$tableData,$tableDetailData);

		return view('vh::import.view',$data);

	}

	private function _import_trait_getFieldTable($table){

		$tableDetailData = self::__getListDetailTable($table);

		$ret = array();

		foreach ($tableDetailData as $key => $value) {

			array_push($ret,$value['name']);

		}

		return $ret;

	}

}

?>