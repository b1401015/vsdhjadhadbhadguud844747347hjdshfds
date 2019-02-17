<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Excel {

    private $excel;

    public function __construct() {
        require_once APPPATH . 'third_party/PHPExcel.php';
        $this->excel = new PHPExcel();
    }

    public function load($path) {
        $objReader = PHPExcel_IOFactory::createReader('Excel5');
        $this->excel = $objReader->load($path);
    }

    public function save($path) {
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save($path);
    }

    public function streamCustom($path='',$filename, $data , $propeties , $title, $errorLineList) {
//        pre($errorLineList);
        if ($data != null) {
            $col = 'A';
            $this->excel->getActiveSheet()->setCellValue('A1', $title);
            $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
            $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            foreach ($propeties as $key) {

                $objRichText = new PHPExcel_RichText();
                $objPayable = $objRichText->createTextRun(str_replace("_", " ", $key));
                $this->excel->getActiveSheet()->getCell($col . '2')->setValue($objRichText);
                $this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
                $col++;
            }
//            $col++;
            $objRichText = new PHPExcel_RichText();
            $objPayable = $objRichText->createTextRun(str_replace("_", " ", "Error message"));
            $this->excel->getActiveSheet()->getCell($col . '2')->setValue($objRichText);
            $this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);

            $this->excel->getActiveSheet()->mergeCells('A1:'.$col . '1');
            $this->excel->getActiveSheet()->getStyle('A1')->applyFromArray(arr_all_border());
            //style header
            $this->excel->getActiveSheet()->getStyle('A2:'.$col. '2')->getFill()->applyFromArray(color_header());
            $this->excel->getActiveSheet()->getStyle('A2:'.$col. '2')->applyFromArray(arr_all_border());
            $this->excel->getActiveSheet()->getStyle('A2:'.$col . '2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $rowNumber = 3;
            foreach ($data as $row) {
                $col = 'A';
                foreach ($row as $cell) {
                    $this->excel->getActiveSheet()->setCellValue($col . $rowNumber, $cell);
                    $col++;
                }
                $this->excel->getActiveSheet()->getStyle('A'.$rowNumber.':'.$col.$rowNumber)->applyFromArray(arr_all_border());
//                $col++;
                if(isset($errorLineList[$rowNumber])){
                    $this->excel->getActiveSheet()->setCellValue($col . $rowNumber , $errorLineList[$rowNumber] );
                    $this->excel->getActiveSheet()->getStyle('A'.$rowNumber.':'.$col.$rowNumber)->getFill()->applyFromArray(color_error());
                }
                $rowNumber++;

            }
        }
        header('Content-type: application/ms-excel');
        header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
        header("Cache-control: private");
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save("export/".$path."/".$filename);
        header("location: " . base_url() . "export/".$path."/".$filename);
        unlink(base_url() . "export/".$path."/".$filename);
        return $filename;
    }



    public function stream($path ,$filename, $data = null, $title) {
        if ($data != null) {
            $col = 'A';
            $this->excel->getActiveSheet()->setCellValue('A1', $title);
            $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
            $this->excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
            $this->excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            foreach ($data[0] as $key => $val) {
                $col++;
                $objRichText = new PHPExcel_RichText();
                $objPayable = $objRichText->createTextRun(str_replace("_", " ", $key));
                $this->excel->getActiveSheet()->getCell($col . '2')->setValue($objRichText);
                $this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            }
            $this->excel->getActiveSheet()->mergeCells('A1:'.$col . '1');
            $this->excel->getActiveSheet()->getStyle('A1')->applyFromArray(arr_all_border());
            $this->excel->getActiveSheet()->getCell('A2')->setValue('No.');
            //style header
            $this->excel->getActiveSheet()->getStyle('A2:'.$col. '2')->getFill()->applyFromArray(color_header());
            $this->excel->getActiveSheet()->getStyle('A2:'.$col. '2')->applyFromArray(arr_all_border());
            $this->excel->getActiveSheet()->getStyle('A2:'.$col . '2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $rowNumber = 2;
            $index = 1;
            foreach ($data as $row) {
                $col = 'A';
                $rowNumber++;
                $this->excel->getActiveSheet()->setCellValue("A" . $rowNumber, $index);
                $index++;
                foreach ($row as $cell) {
                    $col++;
                    $this->excel->getActiveSheet()->setCellValue($col . $rowNumber, $cell);
                }
                $this->excel->getActiveSheet()->getStyle('A'.$rowNumber.':'.$col.$rowNumber)->applyFromArray(arr_all_border());
            }
        }
        header('Content-type: application/ms-excel');
        header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
        header("Cache-control: private");
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save("export/".$path."/".$filename);
        header("location: " . base_url() . "export/".$path."/".$filename);
        unlink(base_url() . "export/".$path."/".$filename);
    }

    public function __call($name, $arguments) {
        if (method_exists($this->excel, $name)) {
            return call_user_func_array(array($this->excel, $name), $arguments);
        }
        return null;
    }

    public function export_sale_management($path ,$filename, $data = null, $title, $time_from, $time_to,$region, $retailer_id) {
        $this->excel->getActiveSheet()->setCellValue('A2', $title);
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(20);
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $this->excel->getActiveSheet()->mergeCells('A2:S2');
        //
        $this->excel->getActiveSheet()->setCellValue('B3', 'KỲ/THÁNG:');
        $this->excel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->setCellValue('C3', date('m/Y'));
        $from = date('d-m-Y', strtotime('first day of this month'));
        $to = date('d-m-Y');
        if($time_from){
            $from = date('d-m-Y', strtotime($time_from));
        }
        $this->excel->getActiveSheet()->setCellValue('B4', 'From');
        $this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->setCellValue('C4', $from);
        if($time_to){
            $to = date('d-m-Y', strtotime($time_to));
        }
        $this->excel->getActiveSheet()->setCellValue('D4', 'To');
        $this->excel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->setCellValue('E4', $to);
        //
        $this->excel->getActiveSheet()->setCellValue('B5', 'FILE: ');
        $this->excel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->setCellValue('C5', 'INPUT');

        //
        $this->excel->getActiveSheet()->setCellValue('B6', 'Region');
        $this->excel->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->setCellValue('C6', $region);
        //
        $this->excel->getActiveSheet()->setCellValue('B7', 'OUTLET');
        $this->excel->getActiveSheet()->getStyle('B7')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->setCellValue('C7', $retailer_id);
        $this->excel->getActiveSheet()->getColumnDimension()->setAutoSize(true);
        //Set header
        $this->excel->getActiveSheet()->setCellValue('A9', 'No.');

        $this->excel->getActiveSheet()->setCellValue('A9', 'No.');
        $this->excel->getActiveSheet()->mergeCells('A9:A10');
        //
        $this->excel->getActiveSheet()->setCellValue('B9', 'City/Province');
        $this->excel->getActiveSheet()->mergeCells('B9:B10');
        //
        $this->excel->getActiveSheet()->setCellValue('C9', 'Sold to_id');
        $this->excel->getActiveSheet()->mergeCells('C9:C10');
        //
        $this->excel->getActiveSheet()->setCellValue('D9', 'Ship to_id');
        $this->excel->getActiveSheet()->mergeCells('D9:D10');
        //
        $this->excel->getActiveSheet()->setCellValue('E9', 'Distributor_name');
        $this->excel->getActiveSheet()->mergeCells('E9:E10');
        //
        $this->excel->getActiveSheet()->setCellValue('F9', 'Outlet_id(retailer id)');
        $this->excel->getActiveSheet()->mergeCells('F9:F10');
        //
        $this->excel->getActiveSheet()->setCellValue('G9', 'Outlet_name');
        $this->excel->getActiveSheet()->mergeCells('G9:G10');
        //
        $this->excel->getActiveSheet()->setCellValue('H9', 'Category');
        $this->excel->getActiveSheet()->mergeCells('H9:H10');
        //
        $this->excel->getActiveSheet()->setCellValue('I9', 'Sub-category');
        $this->excel->getActiveSheet()->mergeCells('I9:I10');
        //
        $this->excel->getActiveSheet()->setCellValue('J9', 'Brand');
        $this->excel->getActiveSheet()->mergeCells('J9:J10');
        //
        $this->excel->getActiveSheet()->setCellValue('K9', 'Variant');
        $this->excel->getActiveSheet()->mergeCells('K9:K10');
        //
        $this->excel->getActiveSheet()->setCellValue('L9', 'SKU_code ');
        $this->excel->getActiveSheet()->mergeCells('L9:L10');
        //
        $this->excel->getActiveSheet()->setCellValue('M9', 'SKU name');
        $this->excel->getActiveSheet()->mergeCells('M9:M10');
        //
        $this->excel->getActiveSheet()->setCellValue('N9', 'Unit');
        $this->excel->getActiveSheet()->mergeCells('N9:N10');
        //
        $this->excel->getActiveSheet()->setCellValue('O9', 'Package');
        $this->excel->getActiveSheet()->mergeCells('O9:O10');
        //
        $this->excel->getActiveSheet()->setCellValue('P9', 'Retailer price (incl. VAT)');
        $this->excel->getActiveSheet()->mergeCells('P9:P10');
        //
        $this->excel->getActiveSheet()->setCellValue('Q9', 'Số lượng nhập từ');
        $this->excel->getActiveSheet()->mergeCells('Q9:R9');
        $this->excel->getActiveSheet()->setCellValue('Q10', 'Thùng');
        $this->excel->getActiveSheet()->setCellValue('R10', 'Đơn vị lẻ');
        //
        $this->excel->getActiveSheet()->setCellValue('S9', 'Total');
        $this->excel->getActiveSheet()->mergeCells('S9:S10');
        $this->excel->getActiveSheet()->mergeCells('A2:S2');
//        $this->excel->getActiveSheet()->getStyle('A2:S2')->applyFromArray(arr_all_border());
        //style header
        $this->excel->getActiveSheet()->getStyle('A9:S10')->getFill()->applyFromArray(color_header());
        $this->excel->getActiveSheet()->getStyle('A9:S10')->applyFromArray(arr_all_border());
        $this->excel->getActiveSheet()->getStyle('A9:S10')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//                $this->excel->getActiveSheet()->getColumnDimension('A9:S10')->setAutoSize(true);

        if ($data != null) {
            $rowNumber = 10;
            $index = 1;
            foreach ($data as $row) {
                $col = 'A';
                $rowNumber++;
                $this->excel->getActiveSheet()->setCellValue("A" . $rowNumber, $index);
                $index++;
                foreach ($row as $cell) {
                    $col++;
                    $this->excel->getActiveSheet()->setCellValue($col . $rowNumber, $cell);
                }
                $this->excel->getActiveSheet()->getStyle('A'.$rowNumber.':'.$col.$rowNumber)->applyFromArray(arr_all_border());
            }
        }else{
            $this->excel->getActiveSheet()->setCellValue('A11', 'No Data');
            $this->excel->getActiveSheet()->mergeCells('A11:S11');
            $this->excel->getActiveSheet()->getStyle('A11:S11')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        header('Content-type: application/ms-excel');
        header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
        header("Cache-control: private");
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save("export/".$path."/".$filename);
        header("location: " . base_url() . "export/".$path."/".$filename);
        unlink(base_url() . "export/".$path."/".$filename);
    }

    public function export_sale_management_error($path ,$filename, $data = null, $title, $time_from, $time_to,$region, $retailer_id, $errorLineList) {
        $this->excel->getActiveSheet()->setCellValue('A2', $title);
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setSize(20);
        $this->excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        $this->excel->getActiveSheet()->mergeCells('A2:S2');
        //
        $this->excel->getActiveSheet()->setCellValue('B3', 'KỲ/THÁNG:');
        $this->excel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->setCellValue('C3', date('m/Y'));
        $from = date('d-m-Y', strtotime('first day of this month'));
        $to = date('d-m-Y');
        if($time_from){
            $from = date('d-m-Y', strtotime($time_from));
        }
        $this->excel->getActiveSheet()->setCellValue('B4', 'From');
        $this->excel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->setCellValue('C4', $from);
        if($time_to){
            $to = date('d-m-Y', strtotime($time_to));
        }
        $this->excel->getActiveSheet()->setCellValue('D4', 'To');
        $this->excel->getActiveSheet()->getStyle('D4')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->setCellValue('E4', $to);
        //
        $this->excel->getActiveSheet()->setCellValue('B5', 'FILE: ');
        $this->excel->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->setCellValue('C5', 'INPUT');

        //
        $this->excel->getActiveSheet()->setCellValue('B6', 'Region');
        $this->excel->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->setCellValue('C6', $region);
        //
        $this->excel->getActiveSheet()->setCellValue('B7', 'OUTLET');
        $this->excel->getActiveSheet()->getStyle('B7')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->setCellValue('C7', $retailer_id);
        $this->excel->getActiveSheet()->getColumnDimension()->setAutoSize(true);
        //Set header
        $this->excel->getActiveSheet()->setCellValue('A9', 'No.');

        $this->excel->getActiveSheet()->setCellValue('A9', 'No.');
        $this->excel->getActiveSheet()->mergeCells('A9:A10');
        //
        $this->excel->getActiveSheet()->setCellValue('B9', 'City/Province');
        $this->excel->getActiveSheet()->mergeCells('B9:B10');
        //
        $this->excel->getActiveSheet()->setCellValue('C9', 'Sold to_id');
        $this->excel->getActiveSheet()->mergeCells('C9:C10');
        //
        $this->excel->getActiveSheet()->setCellValue('D9', 'Ship to_id');
        $this->excel->getActiveSheet()->mergeCells('D9:D10');
        //
        $this->excel->getActiveSheet()->setCellValue('E9', 'Distributor_name');
        $this->excel->getActiveSheet()->mergeCells('E9:E10');
        //
        $this->excel->getActiveSheet()->setCellValue('F9', 'Outlet_id(retailer id)');
        $this->excel->getActiveSheet()->mergeCells('F9:F10');
        //
        $this->excel->getActiveSheet()->setCellValue('G9', 'Outlet_name');
        $this->excel->getActiveSheet()->mergeCells('G9:G10');
        //
        $this->excel->getActiveSheet()->setCellValue('H9', 'Category');
        $this->excel->getActiveSheet()->mergeCells('H9:H10');
        //
        $this->excel->getActiveSheet()->setCellValue('I9', 'Sub-category');
        $this->excel->getActiveSheet()->mergeCells('I9:I10');
        //
        $this->excel->getActiveSheet()->setCellValue('J9', 'Brand');
        $this->excel->getActiveSheet()->mergeCells('J9:J10');
        //
        $this->excel->getActiveSheet()->setCellValue('K9', 'Variant');
        $this->excel->getActiveSheet()->mergeCells('K9:K10');
        //
        $this->excel->getActiveSheet()->setCellValue('L9', 'SKU_code ');
        $this->excel->getActiveSheet()->mergeCells('L9:L10');
        //
        $this->excel->getActiveSheet()->setCellValue('M9', 'SKU name');
        $this->excel->getActiveSheet()->mergeCells('M9:M10');
        //
        $this->excel->getActiveSheet()->setCellValue('N9', 'Unit');
        $this->excel->getActiveSheet()->mergeCells('N9:N10');
        //
        $this->excel->getActiveSheet()->setCellValue('O9', 'Package');
        $this->excel->getActiveSheet()->mergeCells('O9:O10');
        //
        $this->excel->getActiveSheet()->setCellValue('P9', 'Retailer price (incl. VAT)');
        $this->excel->getActiveSheet()->mergeCells('P9:P10');
        //
        $this->excel->getActiveSheet()->setCellValue('Q9', 'Số lượng nhập từ');
        $this->excel->getActiveSheet()->mergeCells('Q9:R9');
        $this->excel->getActiveSheet()->setCellValue('Q10', 'Thùng');
        $this->excel->getActiveSheet()->setCellValue('R10', 'Đơn vị lẻ');
        //
        $this->excel->getActiveSheet()->setCellValue('S9', 'Total');
        $this->excel->getActiveSheet()->mergeCells('S9:S10');
        //
        $this->excel->getActiveSheet()->setCellValue('T9', 'Error Message');
        $this->excel->getActiveSheet()->mergeCells('T9:T10');
        $this->excel->getActiveSheet()->mergeCells('A2:S2');
//        $this->excel->getActiveSheet()->getStyle('A2:S2')->applyFromArray(arr_all_border());
        //style header
        $this->excel->getActiveSheet()->getStyle('A9:S10')->getFill()->applyFromArray(color_header());
        $this->excel->getActiveSheet()->getStyle('A9:S10')->applyFromArray(arr_all_border());
        $this->excel->getActiveSheet()->getStyle('A9:S10')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//                $this->excel->getActiveSheet()->getColumnDimension('A9:S10')->setAutoSize(true);

        if ($data != null) {
            $rowNumber = 11;
            $index = 1;
            foreach ($data as $row) {
                $col = 'A';
                $this->excel->getActiveSheet()->setCellValue("A" . $rowNumber, $index);
                $index++;
                foreach ($row as $cell) {
                    $this->excel->getActiveSheet()->setCellValue($col . $rowNumber, $cell);
                    $col++;
                }
                $this->excel->getActiveSheet()->getStyle('A'.$rowNumber.':'.$col.$rowNumber)->applyFromArray(arr_all_border());
                if(isset($errorLineList[$rowNumber])){
                    $this->excel->getActiveSheet()->setCellValue($col . $rowNumber , $errorLineList[$rowNumber] );
                    $this->excel->getActiveSheet()->getStyle('A'.$rowNumber.':'.$col.$rowNumber)->getFill()->applyFromArray(color_error());
                }
                $rowNumber++;
            }
        }else{
            $this->excel->getActiveSheet()->setCellValue('A11', 'No Data');
            $this->excel->getActiveSheet()->mergeCells('A11:S11');
            $this->excel->getActiveSheet()->getStyle('A11:S11')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        header('Content-type: application/ms-excel');
        header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
        header("Cache-control: private");
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save("export/".$path."/".$filename);
        header("location: " . base_url() . "export/".$path."/".$filename);
        unlink(base_url() . "export/".$path."/".$filename);
        return $filename;
    }
    /**
     * export Retailer target to Excel file
     * @param unknown $subfolder_name: folder contains exported files in folder "export/"
     * @param unknown $filename
     * @param unknown $data: table data
     * @param unknown $title: name of this file
     * @param unknown $term: Ky/Thang
     * @param unknown $region
     * @param unknown $outlet
     */
    public function export_retailer_target($path, $filename, $data, $title, $term, $region, $outlet) {
    	$activeSheet = $this->excel->getActiveSheet();
    	//big title
    	$activeSheet->setCellValue('A1', strtoupper($title));
    	$activeSheet->getStyle('A21')->getFont()->setSize(30);
    	$activeSheet->getStyle('A1')->getFont()->setBold(true);
    	$activeSheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    	$activeSheet->mergeCells('A1:L1');
    	//Term
    	$activeSheet->setCellValue('A2', 'KỲ/THÁNG:');
    	$activeSheet->getStyle('A2')->getFont()->setBold(true);
    	$activeSheet->setCellValue(RT_TERM_COL_IDX.RT_TERM_ROW_IDX, $term);
    	//
    	$activeSheet->setCellValue('A3', 'FILE: ');
    	$activeSheet->getStyle('A3')->getFont()->setBold(true);
    	$activeSheet->setCellValue('B3', 'INPUT');		//hard code
    	//Region
    	$activeSheet->setCellValue('A4', 'Region');
    	$activeSheet->getStyle('A4')->getFont()->setBold(true);
    	$activeSheet->setCellValue(RT_REGION_COL_IDX.RT_REGION_ROW_IDX, $region);
    	//Outlet
    	$activeSheet->setCellValue('A5', 'OUTLET');
    	$activeSheet->getStyle('A5')->getFont()->setBold(true);
    	$activeSheet->setCellValue('B5', $outlet);
    	//Set header of table
    	$tile_row_idx = RT_DATA_BLOCK_BEGIN_ROW_IDX - 1;
    
    	$activeSheet->setCellValue('A'.$tile_row_idx, 'No.');
    	$activeSheet->setCellValue('B'.$tile_row_idx, 'City/Province');
    	$activeSheet->setCellValue('C'.$tile_row_idx, 'Distributor_id');
    	$activeSheet->setCellValue('D'.$tile_row_idx, 'Distributor_name');
    	$activeSheet->setCellValue('E'.$tile_row_idx, 'Outlet_id');
    	$activeSheet->setCellValue('F'.$tile_row_idx, 'Outlet_name');
    	$activeSheet->setCellValue('G'.$tile_row_idx, 'username');
    	$activeSheet->setCellValue('H'.$tile_row_idx, 'Fullname');
    	$activeSheet->setCellValue('I'.$tile_row_idx, 'Tel');
    	$activeSheet->setCellValue('J'.$tile_row_idx, 'History data');
    	$activeSheet->setCellValue('K'.$tile_row_idx, 'Chỉ tiêu (VND)');
    	$activeSheet->setCellValue('L'.$tile_row_idx, 'Noted');
    	//style header
    	$activeSheet->getStyle('A7:L7')->getFill()->applyFromArray(color_header());
    	$activeSheet->getStyle('A7:L7')->applyFromArray(arr_all_border());
    	//fill data into table
    	if (isset($data)) {
    		$index = 1;
    		$data_len = count($data);
    		for ($row_index=0; $row_index<$data_len; $row_index++){
    			//fill data to row
    			$col_index = 1;
    			$activeSheet->setCellValue("A" . (RT_DATA_BLOCK_BEGIN_ROW_IDX+$row_index), $row_index+1);		//cell "No."
    			//set value to this row
    			$begin_data_col_name_index = 'B';
    			foreach($data[$row_index] AS $key=>$val){
    				$activeSheet->setCellValue($begin_data_col_name_index++.''. (RT_DATA_BLOCK_BEGIN_ROW_IDX+$row_index), $val);
    			}
    		}
    		$activeSheet->getStyle('A'.RT_DATA_BLOCK_BEGIN_ROW_IDX.':'.RT_NOTE_COL_IDX.(RT_DATA_BLOCK_BEGIN_ROW_IDX+$data_len-1))->applyFromArray(arr_all_border());
    	}else{
    		$activeSheet->setCellValue('A'.RT_DATA_BLOCK_BEGIN_ROW_IDX, 'No Data');
    		$activeSheet->mergeCells('A'.RT_DATA_BLOCK_BEGIN_ROW_IDX.':L'+(RT_DATA_BLOCK_BEGIN_ROW_IDX));
    		$activeSheet->getStyle('A11:S11')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    	}
    	header('Content-type: application/ms-excel');
    	header('Content-Disposition: attachment; filename="' . $filename . '"');
    	header("Cache-control: private");
    	$objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
    	if (!file_exists(RT_EXPORT_PATH)){
    		mkdir(RT_EXPORT_PATH);
    	}
    	$objWriter->save(RT_EXPORT_PATH.$filename);
    	header("location: " . base_url() . RT_EXPORT_PATH . $filename);
    	unlink(base_url() . RT_EXPORT_PATH .$filename);
    }
}