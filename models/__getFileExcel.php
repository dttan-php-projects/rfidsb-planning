<?php
    //get exel File function
    function getFileExcel($fileName){	
        //Nhúng file PHPExcel
        require_once "../assets/PHPExcel/IOFactory.php";
        //Tiến hành xác thực file
        $objFile = PHPExcel_IOFactory::identify($fileName);
        $objData = PHPExcel_IOFactory::createReader($objFile);
        //Chỉ đọc dữ liệu
        $objData->setReadDataOnly(true);

        // Load dữ liệu sang dạng đối tượng
        $objPHPExcel = $objData->load($fileName);
        //Chọn trang cần truy xuất
        $sheet = $objPHPExcel->setActiveSheetIndex(0);

        //Lấy ra số dòng cuối cùng
        $Totalrow = $sheet->getHighestRow();
        //Lấy ra tên cột cuối cùng
        $LastColumn = $sheet->getHighestColumn();
        //Chuyển đổi tên cột đó về vị trí thứ, VD: C là 3,D là 4
        $TotalCol = PHPExcel_Cell::columnIndexFromString($LastColumn);
        //Tạo mảng chứa dữ liệu
        $data = [];
        //Tiến hành lặp qua từng ô dữ liệu
        //----Lặp dòng, Vì dòng đầu là tiêu đề cột nên chúng ta sẽ lặp giá trị từ dòng 2 , nếu có Tiêu Đề , 1 không có tiêu đề
        $firstRow = $sheet->getCellByColumnAndRow(0, 1)->getValue();
        $checkColumD = $sheet->getCellByColumnAndRow(3, 1)->getValue();
        for ($i = 2; $i <= $Totalrow; $i++) {
        //----Lặp cột
            for ($j = 0; $j < $TotalCol; $j++) {
                // Tiến hành lấy giá trị của từng ô đổ vào mảng
                $dataValue = $sheet->getCellByColumnAndRow($j, $i)->getValue();
                $dataValue = trim($dataValue);
                $data[$i - 1][$j] = $dataValue;
            }	
        }
        //Hiển thị mảng dữ liệu
        return $data;
    }