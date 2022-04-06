<?php
    require_once ("./define_constant_system.php");
    require_once ("./template/template1.php");

    require_once (PATH_DATA . "/System_Parameters.php");

    InitPage("rfidsb","AVERY DENNISON - RFID SB");
?>

<script>
    //@Layout Main: START
    
    var LayoutMain;
    //load view UA
    var dhxWinsUA;
    var viewUAGrid;
    var UAGrid;
    var dhxWinsAddUA;
    var size_qty_total_check_correct = 0;
    var promise_date_check;
    var request_date_check;
    var dhxWins2;

    function deleteUA()
    {

        var checkIDs = [];
        UAGrid.forEachRow(function(id){
            if(UAGrid.cells(id,0).getValue()==1){
                var id_del = UAGrid.cells(id,4).getValue();
                checkIDs.push(id_del);
            }
        });
        var tes_del = JSON.stringify(checkIDs);
        console.log('tes_del: '+tes_del);

        if(!checkIDs.length>0){
            alert("Vui lòng chọn dòng để XÓA");
            return false;
        }else{
            confirm_delete = confirm("Bạn có muốn XÓA những item đã chọn!!!");
            if(confirm_delete){
                var url_delete = './models/deleteUA_conn.php';
                // get all checkbox
                $.ajax({
                    url: url_delete,
                    type: "POST",
                    data: {data: JSON.stringify(checkIDs)},
                    dataType: "json",
                    beforeSend: function(x) {
                        if (x && x.overrideMimeType) {
                        x.overrideMimeType("application/j-son;charset=UTF-8");
                        }
                    },
                    success: function(result) {
                        if(result.status){
                            // reload	
                            for(var i=0;i<checkIDs.length;i++){
                                UAGrid.deleteRow(checkIDs[i]);
                            }
                            alert(result.mess);		
                            location.reload();
                            
                        }else{
                            alert(result.mess);		
                            location.reload();
                        }
                    },
                    error: function(){
                        alert("Có lỗi trong quá trình xóa!");
                    }
                });					
            }
        }
    }

    function deleteNO(po_no)
    {

        var confirm = confirm("Bạn có chắc chắn muốn xóa NO# " + po_no);
        if(confirm){
			var url = '../models/deleteNO_conn.php';
			$.ajax({
			    url: url,
				type: "POST",
				data: {data: po_no},
				dataType: "json",
				beforeSend: function(x) { if (x && x.overrideMimeType) { x.overrideMimeType("application/j-son;charset=UTF-8"); } },
				success: function(result) {
					if(result.status){
						
                        alert(results.message);
                        location.reload();

					}else{
						alert(results.message);
					}
				}
			});
		}else{
			location.reload();
		}
        
    }

    function DocumentStart()
    {
        
        print_type = getCookie('print_type_rfsb');

        //@Toolbar: START
        ToolbarMain.addText("SO_TEXT", 2, "SO");
        ToolbarMain.addInput("SO",3,"");

        ToolbarMain.addText("GPM_TEXT", 4, "GPM");
        ToolbarMain.addInput("GPM",5, "");

        ToolbarMain.addText("", 6, "FROM DATE");
        ToolbarMain.addInput("FROM_DATE",7,"");
        ToolbarMain.addText("", 8, "TO DATE");
        ToolbarMain.addInput("TO_DATE",9,"");

        var from_date_input = ToolbarMain.getInput("FROM_DATE");
        var to_date_input = ToolbarMain.getInput("TO_DATE");

        myCalendar = new dhtmlXCalendarObject([from_date_input,to_date_input]);
        myCalendar.setDateFormat("%d-%M-%y");
        ToolbarMain.addSpacer("TO_DATE");

        ToolbarMain.addButton('SAVE_NO',17, '<span style="color:red;font-weight:bold;">SAVE</span>', 'fa fa-floppy-o');
        ToolbarMain.addText("SAVE_NO_N",18, "|", null);
        ToolbarMain.addButton("PRINT_NO",19, "PRINT", "print.gif");
        ToolbarMain.hideItem('PRINT_NO');
        ToolbarMain.addText("PRINT_NO_N",20, "|", null);
        ToolbarMain.hideItem('PRINT_NO_N');
        ToolbarMain.addButton('VIEW_NO',21, 'VIEW NO', 'fa fa-list');
        ToolbarMain.addText("",22, "|", null);
        //Report and Export... viewMaterial
        //ToolbarMain.addButton("REPORT_NO",23, "REPORT", "xlsx.gif");
        ToolbarMain.addButton("EXPORT_NO",23, "EXPORT", "fa fa-download");
        ToolbarMain.addText("",24, "|", null);

        var opts = [
            ['DB_1LINE', 'obj', 'DB 1 LINE', 'fa fa-database'],
            ['DB_MS_COLOR', 'obj', 'DB MS COLOR', 'fa fa-database'],
            ['DB_TRIM', 'obj', 'DB TRIM/PVH', 'fa fa-database'],
            ['DB_SETTING_FORM', 'obj', 'DB SETTING FORM', 'fa fa-database'],
            ['SCRAP', 'obj', 'SCRAP', 'fa fa-database'],
            ['UA', 'obj', 'UA', 'fa fa-database'],
            ['RBO_PD', 'obj', 'RBO LT (PD từ Planning)', 'fa fa-database'],
            ['SAMPLE', 'obj', 'File Mẫu', 'fa fa-cloud-download']
        ];
        
        ToolbarMain.addButtonSelect('DB_ALL', 25, 'MASTER FILE', opts, 'fa fa-database');
        
        if(Number(getCookie('rfidsb_acount_type') ) == 1 || Number(getCookie('rfidsb_acount_type') ) == 9 ) {
            
            var user_opts = [
                ['USER_CREATE', 'obj', 'CREATE USER', 'fa fa-user-plus'],
                ['USER_VIEWS', 'obj', 'VIEW USERS', 'fa fa-list']
            ];
            
        } else {

            var user_opts = [
                ['USER_VIEWS', 'obj', 'VIEW USERS', 'fa fa-list']
            ];

        }
        

        ToolbarMain.addButtonSelect("USERS", 40, "USERS", user_opts, 'fa fa-users');
        ToolbarMain.addText("",41, "|", null);

        ToolbarMain.attachEvent("onClick", function(name) {
        //1. Save No
            if (name == "SAVE_NO") {
                // alert("You choose save No");
                saveDatabase();
            } else if (name == "PRINT_NO") { //2. Print No
                // alert("Bạn muốn in");
                var PO_NO     = myForm.getItemValue('frm_no');
                printNO(PO_NO);
            } else if (name == "VIEW_NO") { //3. View No

                viewNO();
            } else if (name == "REPORT_NO") { //5. Report
                //khong 
            } else if(name == "EXPORT_NO") { //6. Export All No
                var from_date_value = ToolbarMain.getValue("FROM_DATE");
                var to_date_value   = ToolbarMain.getValue("TO_DATE");

                if(!from_date_value||!to_date_value){
                    alert('VUI LÒNG CHỌN KHOẢNG NGÀY ĐỂ EXPORT DỮ LIỆU');
                    return false;
                }
                var url_export = './models/reportNO_conn.php?form_type='+print_type+'&from_date_value='+from_date_value+'&to_date_value='+to_date_value;
                document.location.href = url_export;

                /* TRƯỜNG HỢP MUỐN EXPORT TỪNG FORM THÌ SỬ DỤNG*/
                // var text;
                // var report = prompt("CHỌN: 1 - Export form đang làm lệnh || 2 - Export All", "2");
                // switch(report) {
                //     case "1":
                //         text = "Export form đang làm lệnh sản xuất";
                //         alert(text);
                //         var url_export = './models/reportNO_conn.php?form_type='+print_type+'&from_date_value='+from_date_value+'&to_date_value='+to_date_value;
                //         document.location.href = url_export;
                //         break;
                //     case "2":
                //         text = "Export tất cả form";
                //         alert(text);
                //         var url_export = './models/reportNO_conn.php?form_type=""&from_date_value='+from_date_value+'&to_date_value='+to_date_value;
                //         document.location.href = url_export;
                //         break;
                //     default:
                //         text = "Bạn đã không tiếp tục hoặc không nhập đúng";
                //         var url_export = './models/reportNO_conn.php?form_type='+print_type+'&from_date_value='+from_date_value+'&to_date_value='+to_date_value;
                //         console.log(url_export);
                //         alert(text);
                // }     //end switch   
                
            } else if (name == "DB_1LINE") {
                viewDB1Line();
                init_dhxDB1LineGrid();
                init_dhxDB1LineToolbar();

            } else if (name == "DB_MS_COLOR") {
                viewDBMSColor();
                init_dhxDBMSColorGrid();
                init_dhxDBMSColorToolbar();
            } else if (name == "DB_TRIM") {
                viewDBTrim();
                init_dhxDBTrimGrid();
                init_dhxDBTrimToolbar();
            } else if (name == "DB_SETTING_FORM") {
                //alert("Setting form");
                viewSettingForm();
                init_dhxSettingFormGrid();
                init_dhxSettingFormToolbar();
                
            } else if (name == "SCRAP") {
                viewScrap();
            } else if (name == "UA") {
                viewUA();
            } else if (name == "USER_CREATE") {
                
                createUser();

            } else if (name == "USER_VIEWS") {
                
                viewUsers();

            } else if (name == "SAMPLE" ) {
                
                window.open("./data/SampleFile/IMPORT/","blank");

            } else if (name == "RBO_PD" ) {
                
                loadRBOLT();

            }


        });     //@Toolbar: END print_type

        initLayoutHome();


        //1. get SOLINE input
        getSOLINE();

        /** INIT ALL ************************************************************************/
        var LayoutMainHome;
        function initLayoutHome(){
            
            LayoutMainHome = new dhtmlXLayoutObject({
                parent: document.body,
                pattern: "1C",
                offsets: {
                    top: 65
                },
                cells: [
                    {id: "a", header: true, text: "DANH SÁCH ĐƠN HÀNG ĐÃ LÀM LỆNH SX" },
                ]
            });

            var countAll = 0;
            var countCurrent = 0;
            countNOView();

            // viewNOHome();

        }//end init layout

        //@1 ok
        function initLayout(){
            if(print_type=='pvh_rfid'||print_type=='trim'||print_type=='trim_macy' || print_type=='ua_cbs'||print_type=='ua_no_cbs'||print_type=='cbs'||print_type=='rfid') {
                LayoutMain = new dhtmlXLayoutObject({
                    parent: document.body,
                    pattern: "3U",
                    offsets: {
                        top: 65
                    },
                    cells: [
                        {id: "a", header: true, text: "PRODUCTION ORDER FORM", width:widthForm, height: heightOrder},
                        {id: "b", header: true, text: "MATERIAL & INK LIST"},
                        {id: "c", header: true, text: "SO LIST"}
                    ]
                });

                if ( print_type=='ua_cbs'||print_type=='ua_no_cbs'||print_type=='cbs'||print_type=='rfid' ) {


                    LayoutB = LayoutMain.cells("b").attachLayout({
                        pattern: "2E",
                        offsets: {
                            top: 0
                        },
                        cells: [
                            {id: "a", header: true, text: "INK LIST/SIZE LIST",height: 300},
                            {id: "b", header: true, text: "MATERIAL LIST"},
                        ]
                    });
                }

            }

        }//end init layout

        //@2: init grid LIST SO: OK
        function initSoGrid(){

            SoGrid = LayoutMain.cells("c").attachGrid();
            console.log("Here");
            SoGrid.setImagePath("./assets/dhtmlx/skins/skyblue/imgs/");
            SoGrid.setHeader("TT,SO,LINE,QTY,INTERNAL ITEM,ORDER ITEM,PRO DATE,REQ DATE,ORDER DATE,CS NAME,RBO,WIDTH,HEIGHT,INK CODE,INK QTY,INK DES,MATERIAL CODE,MATERIAL QTY,MATERIAL DES,GAP,FORM TYPE,SAMPLE,PACKING INSTR,BILL TO CUSTOMER,COUNT SO,SHIP TO,ORDER TYPE NAME,MATERIAL REMARK,INK REMARK,CREATE DATE,LẤY SAMPLE 15PCS,SOLINE SAMPLE");   //sets the headers of columns
            //SO 1,LINE 2,QTY 3,CUSTOMER PO 4,ITEM 5,POR DATE 6,REQ DATE 7,ORDER DATE 8,CS 9,RBO 10,WIDTH 11,HEIGHT 12,INK 11,INK QTY:14, INK DES 15, MATERIAL CODE 16,MATERIAL QTY 17,MATERIAL DES 18, GAP 19,FORM TYPE 20,SAMPLE 21,PACKING INSTR 22,BILL TO CUSTOMER 23,COUNT SO 24,SHIP TO 25,ORDER TYPE NAME 26
            SoGrid.setColumnIds("ID,SO,LINE,QTY,PO,ORDER_ITEM,PRO_DATE,REQ_DATE,ORDER,CS,RBO,WIDTH,HEIGHT,INK,QTY_INK,INK_DES,MATERIAL,MATERIAL_QTY, MATERIAL_DES,GAP,FORM TYPE,SAMPLE,PACKING_INSTRUCTIONS,BILL_TO_CUSTOMER,COUNT_SO,SHIP_TO,ORDER TYPE NAME,MATERIAL_REMARK,INK_REMARK,CREATE_DATE,SAMPLE15,SOLINE_SAMPLE_ALL");         //sets the columns' ids
            SoGrid.setInitWidths("30,70,50,60,120,120,90,90,90,120,180,60,60,120,60,240,120,100,240,60,100,60,300,200,80,200,200,200,200,100,200,200");   //sets the initial widths of columns
            SoGrid.setColAlign("left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left");     //sets the alignment of columns
            SoGrid.setColTypes("ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");    //sets the types of columns
            SoGrid.setColSorting("str,str,int,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str");  //sets the sorting types of columns
            //SoGrid.setColSorting("na,na,str,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na");  //sets the sorting types of columns
            SoGrid.init();
        }

        //@3: init  (include load) OK
        function initMaterialGrid(){
            if(print_type=='pvh_rfid'||print_type=='trim'||print_type=='trim_macy' ){
                MaterialGrid = LayoutMain.cells("b").attachGrid();
                MaterialGrid.setImagePath("./assets/dhtmlx/skins/skyblue/imgs/");
                MaterialGrid.setHeader("TT,MATERIAL CODE,MATERIAL DESCRIPTION,MATERIAL QTY,INK CODE,INK DESCRIPTION,INK QTY");   //sets the headers of columns
                MaterialGrid.setColumnIds("TT,MATERIAL,MATERIAL_DES,MATERIAL_QTY,INK_CODE,INK_DES,INK_QTY");         //sets the columns' ids
                MaterialGrid.setInitWidths("30,120,160,110,120,140,*");   //sets the initial widths of columns
                MaterialGrid.setColAlign("center,left,left,left,left,left,left");     //sets the alignment of columns
                MaterialGrid.setColTypes("ed,ed,ed,ed,ed,ed,ed");    //sets the types of columns
                MaterialGrid.setColSorting("str,str,str,str,str,str,str");  //sets the sorting types of columns
                MaterialGrid.init();
            } else if ( print_type=='ua_cbs' || print_type=='cbs' || print_type=='ua_no_cbs' || print_type=='rfid' ){
                MaterialGrid = LayoutB.cells("b").attachGrid();
                MaterialGrid.setImagePath("./assets/dhtmlx/skins/skyblue/imgs/");
                MaterialGrid.setHeader("MATERIAL CODE,MATERIAL DESCRIPTION,MATERIAL QTY");   //sets the headers of columns
                MaterialGrid.setColumnIds("MATERIAL,MATERIAL_DES,MATERIAL_QTY");         //sets the columns' ids
                MaterialGrid.setInitWidths("130,*,130");   //sets the initial widths of columns
                MaterialGrid.setColAlign("left,left,left");     //sets the alignment of columns
                MaterialGrid.setColTypes("ed,ed,ed");    //sets the types of columns
                MaterialGrid.setColSorting("str,str,str");  //sets the sorting types of columns
                MaterialGrid.init();

            }
            //load data
            loadMaterialNoCBS();
        }

        //@4: size and ink grid (include load) OK
        function initSizeGrid(){

            if ( print_type=='ua_cbs'||print_type=='ua_no_cbs'||print_type=='cbs'||print_type=='rfid' ){
                if(checkID){
                    if(print_type=='ua_cbs'||print_type=='cbs'){
                        SizeGrid = LayoutB.cells("a").attachGrid();
                        LayoutB.cells('a').setText("SIZE LIST");
                        SizeGrid.setImagePath("./assets/dhtmlx/skins/skyblue/imgs/");
                        SizeGrid.setHeader("SIZE,LABEL,BASE ROLL,QTY");   //sets the headers of columns
                        SizeGrid.setColumnIds("SIZE,LABEL,BASE ROLL,QTY");         //sets the columns' ids
                        SizeGrid.setInitWidths("75,195,130,*");   //sets the initial widths of columns
                        SizeGrid.setColAlign("left,left,left,left");     //sets the alignment of columns
                        SizeGrid.setColTypes("ed,ed,ed,ed");    //sets the types of columns
                        SizeGrid.setColSorting("na,na,na,na");  //sets the sorting types of columns
                        SizeGrid.init();

                        //get and load size
                        getSizeCBSGrid();
                        //getSizeFromSO();

                    }else if(print_type=='ua_no_cbs'||print_type=='rfid'){
                        SizeGrid = LayoutB.cells("a").attachGrid();
                        LayoutB.cells('a').setText("INK LIST");
                        SizeGrid.setImagePath("./assets/dhtmlx/skins/skyblue/imgs/");
                        SizeGrid.setHeader("INK CODE,INK DES,INK QTY");   //sets the headers of columns
                        SizeGrid.setColumnIds("INK_CODE,INK_DES,INK_QTY");         //sets the columns' ids
                        SizeGrid.setInitWidths("130,*,110");   //sets the initial widths of columns
                        SizeGrid.setColAlign("left,left,left");     //sets the alignment of columns
                        SizeGrid.setColTypes("ed,ed,ed");    //sets the types of columns
                        SizeGrid.setColSorting("na,na,na");  //sets the sorting types of columns
                        SizeGrid.init();
                        // load to size , xem list size hien tai la list ink
                        var internal_item = SoGrid.cells(checkID,4).getValue();
                        var material_qty = SoGrid.cells(checkID,17).getValue();
                        var gap = SoGrid.cells(checkID,19).getValue();

                        //load data
                        loadInkNoCBS();

                    }
                }//end if check

            }//if
        }//end function @4


        //@5: init Form No
        var formNO;
        var myForm;
        function initFormNO() {
            if(print_type=='ua_cbs'||print_type=='cbs' || print_type=='ua_no_cbs'||print_type=='rfid' || print_type=='pvh_rfid'||print_type=='trim' || print_type=='trim_macy'){
                formNO = [ 
                    {type:"settings", position:"label-center"},
                    {type: "fieldset",name:"formNo", label: "LỆNH SẢN XUẤT/PRODUCTION ORDER", width:1100,  offsetTop:20,offsetLeft:40, offsetBottom:5, style:"font-size: 18px;", list:[
                        {type: "settings", position: "label-left", labelWidth: 160, inputWidth: 350, labelAlign: "left", offsetLeft:13,},
                        {type: "input", name:"frm_no", label: "NO#", value:"", style:"color:blue; font-size:14px;font-weight: bold; "},//
                        {type: "input", name:"frm_ship_to", label: "SHIP TO", style:" font-size:14px; font-weight:bold; color:red;"},//
                        {type: "input", name:"frm_po", label: "INTERNAL ITEM", style:" font-size:14px; font-weight:bold;"},//
                        {type: "input", name:"frm_req", label: "REQUEST DATE", style:" font-size:14px; font-weight:bold;"},//
                        {type: "input", name:"frm_size", label: "PRINT SIZE", style:" font-size:14px; font-weight:bold;"},//
                        {type: "input", name:"frm_material", label: "MATERIAL CODE", style:" font-size:14px; font-weight:bold;"},//
                        {type: "input", name:"frm_material_des", label: "MATERIAL DESCRIPTION (w)", style:" font-size:11px;"},//
                        {type: "input", name:"frm_ink", label: "INK CODE", style:" font-size:14px; font-weight:bold;"},//
                        {type: "input", name:"frm_ink_des", label: "INK DESCRIPTION (w)", style:" font-size:11px;"},//
                        {type: "input", name:"frm_qty_total", label: "QTY (w)", style:"color:red; font-size:14px;font-weight: bold;"},
                        {type: "input", name:"frm_sample15", label: "LẤY SAMPLE 15 PCS (w)", style:"color:red; font-size:14px;"},
                        {type: "input", name:"frm_date_received", label: "DATE RECEIVED (w)", style:"color:red; font-size:14px;"},
                        // {type: 'checkbox', name:'DON_HANG_BU', label: 'ĐƠN HÀNG BÙ', style:"color:red; font-size:14px; font-weight:bold; "},

                        {type:"newcolumn"},
                        {type: "input", name:"frm_create_date", label: "NGÀY TẠO ĐƠN (w)", style:" font-size:14px; font-weight:bold;"},//
                        {type: "input", name:"frm_order", label: "ORDERED DATE", style:" font-size:14px; font-weight:bold;"},//
                        {type: "input", name:"frm_item", label: "ORDERED ITEM", style:" font-size:14px; font-weight:bold;"},//
                        {type: "input", name:"frm_pd", label: "PROMISE DATE (w)", style:" font-size:14px; font-weight:bold;"},//
                        {type: "input", name:"frm_rbo", label: "RBO",  style:" font-size:14px; font-weight:bold; color:red; font-family:Arial;"},//
                        {type: "input", name:"frm_material_qty", label: "MATERIAL QTY", style:" font-size:14px; font-weight:bold;"},
                        {type: "input", name:"frm_material_remark", label: "MATERIAL REMARK (w)", style:" font-size:11px;"},
                        {type: "input", name:"frm_ink_qty", label: "INK QTY",  style:" font-size:14px; font-weight:bold;"},
                        {type: "input", name:"frm_ink_remark", label: "INK REMARK (w)", style:"font-size:11px; "},
                        {type: "input", name:"frm_main_sample_line", label: "LINE CHÍNH & LINE MẪU (w)",  style:" font-size:14px; font-weight:bold;"},
                        {type: "combo", name: "frm_sample", label: "SAMPLE",  style:"color:green; font-size:13px; ", options:[
                            {text: "ĐƠN MẪU", value: "2"},
                            {text: "ĐƠN CÓ MẪU", value: "1"},
                            {text: "ĐƠN KHÔNG CÓ MẪU", value: "0", selected: true}
                        ]},
                        {type: "combo", name: "frm_file", label: "File (w)", style:"color:green; font-size:13px; ", options:[
                            {text: "Chọn File", value: "0", selected: true},
                            {text: "File 1", value: "1"},
                            {text: "File 2&3", value: "2"},
                            {text: "File 4", value: "4"}
                        ]} //combo

                    ]}
                ];
                //init myForm
                myForm                  = LayoutMain.cells("a").attachForm(formNO);
                //set format date received
                var form_date_received = myForm.getInput("frm_date_received");//get input
                var form_date_received_file = myForm.getInput("frm_file");//get input

                var formCalendar = new dhtmlXCalendarObject([form_date_received]);//set calendar
                formCalendar.setDateFormat("%d-%M-%y"); //set format
                //get value
                var so_0                = SoGrid.cells(checkID,1).getValue();
                var line_0              = SoGrid.cells(checkID,2).getValue();
                var so_line_0           = so_0+'-'+line_0;
                var PO_FORM_TYPE        = SoGrid.cells(checkID,20).getValue().toLowerCase();
                

                // var frm_po_no           = po_no_new;
                var frm_qty             = SoGrid.cells(checkID,3).getValue();
                var frm_po              = SoGrid.cells(checkID,4).getValue();
                var frm_item            = SoGrid.cells(checkID,5).getValue();
                var frm_pd              = SoGrid.cells(checkID,6).getValue();
                var frm_req             = SoGrid.cells(checkID,7).getValue();
                var frm_order           = SoGrid.cells(checkID,8).getValue();
                var frm_cs              = SoGrid.cells(checkID,9).getValue();
                var frm_rbo             = SoGrid.cells(checkID,10).getValue();
                var frm_width           = SoGrid.cells(checkID,11).getValue();
                if(!frm_width) frm_width = 0;
                frm_width               = Number(frm_width);
                var frm_height          = SoGrid.cells(checkID,12).getValue(); //12
                if(!frm_width) frm_width = 0;
                var frm_size            = frm_width+" mm"+" x "+ frm_height+" mm"; //12

                var frm_ink             = SoGrid.cells(checkID,13).getValue(); //13
                var frm_ink_qty_total   = 0;
                checked_SOLINE.forEach(function(element) {
                    var id              = element.grid_id;
                    var frm_ink_qty     = SoGrid.cells(id,14).getValue();
                    frm_ink_qty_total   += Number(frm_ink_qty);
                });

                var frm_ink_des         = SoGrid.cells(checkID,15).getValue();
                var frm_material        = SoGrid.cells(checkID,16).getValue();
                
                var frm_material_qty_total = 0;
                checked_SOLINE.forEach(function(element) {
                    var id              = element.grid_id;
                    var frm_material_qty    = SoGrid.cells(id,17).getValue();
                    frm_material_qty_total += Number(frm_material_qty);

                });

                var frm_material_des    = SoGrid.cells(checkID,18).getValue();
                var frm_gap             = SoGrid.cells(checkID,19).getValue();
                if(!frm_gap) frm_gap    = 0;
                frm_gap                 = Number(frm_gap);
                var form_type           = print_type;
                var SAMPLE              = SoGrid.cells(checkID,21).getValue();
                var PACKING_INSTRUCTIONS       = SoGrid.cells(checkID,22).getValue();
                var frm_ship_to         = SoGrid.cells(checkID,25).getValue(); //GET
                var frm_material_remark = SoGrid.cells(checkID,27).getValue();
                var frm_ink_remark      = SoGrid.cells(checkID,28).getValue();
                var frm_create_date     = SoGrid.cells(checkID,29).getValue();
                //var SOLINE_SAMPLE       = SoGrid.cells(checkID,31).getValue();
                var frm_qty_total       = 0;
                checked_SOLINE.forEach(function(element) {
                    var id              = element.grid_id;
                    var qty             = SoGrid.cells(id,3).getValue();
                    frm_qty_total       += Number(qty);
                });

                //set value  frm_po_no
                console.log('so_line_0: ' + so_line_0 );
                console.log('PO_FORM_TYPE: ' + PO_FORM_TYPE );
                createPrefixNO(so_line_0,PO_FORM_TYPE);
                
                // myForm.setItemValue("frm_no", frm_po_no );
                

                myForm.setItemValue("frm_create_date", frm_create_date );
                myForm.setItemValue("frm_ship_to", frm_ship_to );
                myForm.setReadonly("frm_ship_to", true);

                myForm.setItemValue("frm_order", frm_order );
                myForm.setReadonly("frm_order", true);

                myForm.setItemValue("frm_po", frm_po );
                myForm.setReadonly("frm_po", true);

                myForm.setItemValue("frm_item", frm_item );
                myForm.setReadonly("frm_item", true);

                myForm.setItemValue("frm_req", frm_req );
                myForm.setReadonly("frm_req", true);

                myForm.setItemValue("frm_pd", frm_pd );//write
                //myForm.setReadonly("frm_pd", true);

                myForm.setItemValue("frm_size", frm_size );
                myForm.setReadonly("frm_size", true);

                //  '&amp;' 
                frm_rbo = frm_rbo.replace('&amp;', '&');
                myForm.setItemValue("frm_rbo", frm_rbo );
                myForm.setReadonly("frm_rbo", true);

                myForm.setItemValue("frm_material", frm_material );
                myForm.setReadonly("frm_material", true);

                myForm.setItemValue("frm_material_qty", frm_material_qty_total);
                myForm.setReadonly("frm_material_qty", true);

                myForm.setItemValue("frm_material_des", frm_material_des );
                myForm.setItemValue("frm_material_remark", frm_material_remark );

                myForm.setItemValue("frm_ink", frm_ink );
                myForm.setReadonly("frm_ink", true);

                myForm.setItemValue("frm_ink_qty", frm_ink_qty_total );
                myForm.setReadonly("frm_ink_qty", true);

                myForm.setItemValue("frm_ink_des", frm_ink_des );
                myForm.setItemValue("frm_ink_remark", frm_ink_remark );

                myForm.setItemValue("frm_qty_total", frm_qty_total );
                myForm.setReadonly("frm_qty_total", true);
                
                var SAMPLE_OK = 0; //mặc định đơn không mẫu
                if (SAMPLE == 1) {    
                    SAMPLE_OK = 1; //đơn có mẫu
                } else if (SAMPLE == 2) {
                    SAMPLE_OK = 2; //đơn mẫu
                } else {
                    SAMPLE_OK = 0;//Đơn không có mẫu
                }
                myForm.setItemValue("frm_sample", SAMPLE_OK );

                //check hiển thị line chinh/line mẫu split SAMPLE
                if (PACKING_INSTRUCTIONS.indexOf('SO# MAU CUA SO#') !==-1 || PACKING_INSTRUCTIONS.indexOf('MAU CUA SO#') !==-1 || PACKING_INSTRUCTIONS.indexOf('SAMPLE CUA SO#') !==-1  ) {
                    var  PACKING_INSTRUCTIONS_ARR = PACKING_INSTRUCTIONS.split(' ');
                    var len_packing = PACKING_INSTRUCTIONS_ARR.length;
                    LINE_CHINH = 'MAU CUA SO# '+PACKING_INSTRUCTIONS_ARR[len_packing-1];
                    myForm.setItemValue("frm_main_sample_line", LINE_CHINH );

                    // for (var i = 0; i<len_packing;i++) {
                    //     if (i == len_packing) {
                    //         LINE_CHINH = 'MAU CUA SO# '+PACKING_INSTRUCTIONS_ARR[i];
                    //         console.log('LINE_CHINH: '+LINE_CHINH);
                    //         myForm.setItemValue("frm_main_sample_line", LINE_CHINH );
                    //         //myForm.setReadonly("frm_main_sample_line", true);
                    //     }
                    // }
                } else {
                    myForm.setItemValue("frm_main_sample_line", '' );
                    //myForm.setItemValue("frm_main_sample_line", SOLINE_SAMPLE );//soline mau
                }

                //get GPM
                if(rboMain.indexOf('NIKE')!==-1&&print_type=='rfid'){
                    
                    // GPM cho đơn hàng NIKE
                    input_so_line = ToolbarMain.getInput("SO");
                    getGPM(input_so_line.value);

                } else {

                    var ink_code_gpm = ['INKJET', 'EPSON', 'KIARO D', 'QL800' ];
                    for (var i=0; i<ink_code_gpm.length;i++ ) {
                        if (frm_ink.toUpperCase().indexOf(ink_code_gpm[i]) !== -1) {
                            input_so_line = ToolbarMain.getInput("SO");
                            getGPM(input_so_line.value);
                        }
                    }

                    // //20200702: Đây là trường hợp đơn hàng INKJET hoặc EPSON. Nếu code mực = INKJET hoặc EPSON thì get GPM
                    // if (frm_ink.toUpperCase().indexOf('INKJET') !== -1 || frm_ink.toUpperCase().indexOf('EPSON') !== -1 ) {
                    //     input_so_line = ToolbarMain.getInput("SO");
                    //     getGPM(input_so_line.value);
                    // }

                }


            }//end if



        }//end function

        //@6: init grid attach to window Material
        function init_dhxDB1LineGrid() {
            //init grid vào window
            var dhxDB1LineGrid = dhxWins_DB1Line.window("window_db1line").attachGrid();

            //the path to images required by grid . Grid có 27 cột đánh số từ 0 đến 26
            dhxDB1LineGrid.setImagePath("./assets/dhtmlx/codebase/imgs/");             
            dhxDB1LineGrid.setHeader("TT,Internal Item,RBO,ORDER ITEM,Material code (paper),Ribbon code,Description Material,Description Ink,Chieu Doc (dai),Chieu Ngang,Blank Gap (mm),Ghi chu Item,Notes RBO,Remark GIAY,Sample 15pcs,Remark MUC,First Orders,pcs/shit,Kind of Lable,Standard LT,Note,Co gia/Khong gia,Color,Other remark 1,DIT Reference,Other remark 3,Other remark 4,Update by,Created date");//the headers of columns  
            //TT 0,Internal Item 1,RBO 2,ORDER ITEM 3,Material code (paper) 4,Ribbon code 5,Description Material 6,Description Ink 7,Lengh 8,Width 9,Blank Gap (mm) 10,Ghi chu Item 11,Notes RBO 12,Remark GIAY 13,Sample 15pcs 14,Remark MUC 15,First Orders 16,pcs/shit 17,Kind of Lable 18,Standard LT 19,Note 20,Co gia/Khong gia 21,Color 22,Other remark 1 23,Other remark 2 24,Other remark 3 25,Other remark 4 26");//the headers of columns  
            dhxDB1LineGrid.setInitWidths("30,120,150,150,150,150,120,120,70,85,70,90,120,120,60,120,90,70,100,100,50,150,120,100,100,100,100,100,*");          //the widths of columns  
            dhxDB1LineGrid.setColAlign("center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center");       //the alignment of columns   
            dhxDB1LineGrid.setColTypes("ro,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");                //the types of columns  
            dhxDB1LineGrid.setColSorting("int,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str");       //the sorting types   
            dhxDB1LineGrid.enableStableSorting(true);

            //load data from viewDB1LineGrid_conn.php
            dhxDB1LineGrid.enableSmartRendering(true);
            //Lưu ý: filter vượt quá 26 bị lỗi
            dhxDB1LineGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,,,,,,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
            dhxDB1LineGrid.enableMultiselect(true);
            dhxDB1LineGrid.init();
            dhxDB1LineGrid.load('./models/viewDB1LineGrid_conn.php');
        }

        //@7: Init toolbar attach to window Material
        function init_dhxDB1LineToolbar() {
            var DB1LineToolbar = dhxWins_DB1Line.window("window_db1line").attachToolbar();
            DB1LineToolbar.addText("DATABASE_1LINE_",0,"<a style='font-size:12pt;font-weight:bold;color:blue;'>Thực hiện:</a>");
            DB1LineToolbar.setIconsPath("./assets/dhtmlx/common/imgs/");
            DB1LineToolbar.addButton("UPLOAD_DB1LINE",4, "UPLOAD DB 1 LINE", "xlsx.gif", null);
            DB1LineToolbar.addText("UPLOAD_DB1LINE_TXT",5, "|", "", null);
            DB1LineToolbar.addButton("EXPORT_DB1LINE",8, "EXPORT DB 1 LINE", "downloads.gif", null);

            DB1LineToolbar.attachEvent("onClick", function(db1line) {
                //1. upload
                if (db1line == "UPLOAD_DB1LINE") {
                    //alert('Upload db 1 line');
                    uploadDB1Line();
                } else if (db1line == "EXPORT_DB1LINE") {
                    var url_export = './models/reportDB1Line_conn.php';
                    document.location.href = url_export;
                }
            });

        }

        //@8: init grid attach to window Ink
        function init_dhxDBMSColorGrid() {
            //init grid vào window
            var dhxDBMSColorGrid = dhxWins_DBMSColor.window("window_DBMSColor").attachGrid();

            //the path to images required by grid 
            dhxDBMSColorGrid.setImagePath("./assets/dhtmlx/codebase/imgs/");             
            dhxDBMSColorGrid.setHeader("TT,Internal Item,RBO,Order Item,Color code,Item color,Material code (paper),Description Material,Ribbon code,Description Ink,Chieu Doc(dai),Chieu Ngang(rong),Ghi chu Item,Blank Gap (mm),Remark,Other remark 1, Other remark 2, Other remark 3, Other remark 4,Update by,Created date");//the headers of columns  
            //"TT 0,Internal Item 1,RBO 2,Order Item 3,Color code 4,Item color 5,Material code (paper) 6,Description Material 7,Ribbon code 8,Description Ink 9,Chieu Doc (dai) 10,Chieu Ngang (rong) 11,Ghi chu Item 12,Blank Gap (mm) 13,Remark 14,Other remark 1 15, Other remark 2 16, Other remark 3 17, Other remark 4 18
            
            dhxDBMSColorGrid.setInitWidths("30,150,150,150,150,120,140,140,120,120,110,120,120,110,90,120,110,110,110,110,*");          //the widths of columns  
            dhxDBMSColorGrid.setColAlign("center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center");       //the alignment of columns   
            dhxDBMSColorGrid.setColTypes("ro,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");                //the types of columns  
            dhxDBMSColorGrid.setColSorting("int,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str");       //the sorting types   
            dhxDBMSColorGrid.enableStableSorting(true);

            //load data from viewDBMSColorGrid_conn.php
            dhxDBMSColorGrid.enableSmartRendering(true);
            dhxDBMSColorGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
            dhxDBMSColorGrid.enableMultiselect(true);
            dhxDBMSColorGrid.init();
            dhxDBMSColorGrid.load('./models/viewDBMSColorGrid_conn.php');        
        }

        //@9: Init toolbar attach to window Ink
        function init_dhxDBMSColorToolbar() {
            var DBMSColorToolbar = dhxWins_DBMSColor.window("window_DBMSColor").attachToolbar();
            DBMSColorToolbar.addText("DATABASE_MS_COLOR",0,"<a style='font-size:12pt;font-weight:bold;color:blue;'>Thực hiện:</a>");
            DBMSColorToolbar.setIconsPath("./assets/dhtmlx/common/imgs/");
            DBMSColorToolbar.addButton("UPLOAD_DB_MS_COLOR",4, "UPLOAD DB MS COLOR", "xlsx.gif", null);
            DBMSColorToolbar.addText("UPLOAD_DB_MS_COLOR_TXT",5, "|", "", null);
            DBMSColorToolbar.addButton("EXPORT_DB_MS_COLOR",8, "EXPORT DB MS COLOR", "downloads.gif", null);

            DBMSColorToolbar.attachEvent("onClick", function(dbmscolor) {
                //1. upload
                if (dbmscolor == "UPLOAD_DB_MS_COLOR") {  
                    //alert('Upload db ms color');
                    uploadDBMSColor();
                } else if (dbmscolor == "EXPORT_DB_MS_COLOR") {
                    var url_export = './models/reportDBMSColor_conn.php';
                    document.location.href = url_export;
                }
            });
        }

        //@10: init grid attach to window size
        function init_dhxDBTrimGrid() {
            //init grid vào window
            var dhxDBTrimGrid = dhxWins_DBTrim.window("window_DBTrim").attachGrid();

            //the path to images required by grid 
            dhxDBTrimGrid.setImagePath("./assets/dhtmlx/codebase/imgs/");             
            dhxDBTrimGrid.setHeader("TT,Internal Item,Material code (paper),Description Material,Ribbon code,Description Ribbon,Chieu Doc(dai),Chieu Ngang(rong),RBO,NHAN ORDER ITEM,remark,Remark muc,Machine,remark giay,Other remark 1,DIT Reference,Other remark 3,Other remark 4,Update by,Created date");//the headers of columns  
            dhxDBTrimGrid.setInitWidths("50,150,150,150,150,150,150,150,150,150,130,150,150,110,,150,150,150,150,120,*");          //the widths of columns  
            dhxDBTrimGrid.setColAlign("center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center,center");       //the alignment of columns   
            dhxDBTrimGrid.setColTypes("ro,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");                //the types of columns  
            dhxDBTrimGrid.setColSorting("int,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str,str");       //the sorting types   
            dhxDBTrimGrid.enableStableSorting(true);

            //load data from viewDBMSColorGrid_conn.php
            dhxDBTrimGrid.enableSmartRendering(true);
            dhxDBTrimGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");
            dhxDBTrimGrid.enableMultiselect(true);
            dhxDBTrimGrid.init();
            dhxDBTrimGrid.load('./models/viewDBTrimGrid_conn.php');      

        }

        //@11: Init toolbar attach to window size
        function init_dhxDBTrimToolbar() {
            var DBTrimToolbar = dhxWins_DBTrim.window("window_DBTrim").attachToolbar();
            DBTrimToolbar.addText("DATABASE_TRIM",0,"<a style='font-size:12pt;font-weight:bold;color:blue;'>Thực hiện:</a>");
            DBTrimToolbar.setIconsPath("./assets/dhtmlx/common/imgs/");
            DBTrimToolbar.addButton("UPLOAD_DB_TRIM",4, "UPLOAD DB TRIM/PVH", "xlsx.gif", null);
            DBTrimToolbar.addText("UPLOAD_DB_TRIM_TXT",5, "|", "", null);
            DBTrimToolbar.addButton("EXPORT_DB_TRIM",8, "EXPORT DB TRIM/PVH", "downloads.gif", null);

            DBTrimToolbar.attachEvent("onClick", function(dbtrim) {
                //1. upload
                if (dbtrim == "UPLOAD_DB_TRIM") {  
                    //alert('Upload db trim/pvh');
                    uploadDBTrim();
                } else if (dbtrim == "EXPORT_DB_TRIM") {
                    var url_export = './models/reportDBTrim_conn.php';
                    document.location.href = url_export;
                }
            });
        }


        //@12: init grid attach to window SETTING form
        function init_dhxSettingFormGrid() {
            //init grid vào window
            var dhxSettingFormGrid = dhxWins_SettingForm.window("window_SettingForm").attachGrid();

            //the path to images required by grid 
            dhxSettingFormGrid.setImagePath("./assets/dhtmlx/codebase/imgs/");             
            dhxSettingFormGrid.setHeader("TT,INTERNAL ITEM,FORM TYPE,CREATED BY,CREATED DATE");//the headers of columns  
            dhxSettingFormGrid.setInitWidths("50,*,200,200,200");          //the widths of columns  
            dhxSettingFormGrid.setColAlign("center,center,center,center,center");       //the alignment of columns   
            dhxSettingFormGrid.setColTypes("ro,ed,ed,ed,ed");                //the types of columns  
            dhxSettingFormGrid.setColSorting("int,str,str,str,str");       //the sorting types   
            dhxSettingFormGrid.enableStableSorting(true);

            //load data from viewDBMSColorGrid_conn.php
            dhxSettingFormGrid.enableSmartRendering(true);
            dhxSettingFormGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter");
            dhxSettingFormGrid.enableMultiselect(true);
            dhxSettingFormGrid.init();
            dhxSettingFormGrid.load('./models/viewSettingFormGrid_conn.php');      

        }

        //@13: Init toolbar attach to window setting form
        function init_dhxSettingFormToolbar() {
            var DBSettingFormToolbar = dhxWins_SettingForm.window("window_SettingForm").attachToolbar();
            DBSettingFormToolbar.addText("DATABASE_SETTING_FORM",0,"<a style='font-size:12pt;font-weight:bold;color:blue;'>Thực hiện:</a>");
            DBSettingFormToolbar.setIconsPath("./assets/dhtmlx/common/imgs/");
            DBSettingFormToolbar.addButton("UPLOAD_DBSETTINGFORM",4, "UPLOAD DB SETTING FORM", "xlsx.gif", null);
            DBSettingFormToolbar.addText("UPLOAD_DBSETTINGFORM_TXT",5, "|", "", null);
            DBSettingFormToolbar.addButton("EXPORT_DBSETTINGFORM",8, "EXPORT DB SETTING FORM", "downloads.gif", null);

            DBSettingFormToolbar.attachEvent("onClick", function(settingform) {
                //1. upload
                if (settingform == "UPLOAD_DBSETTINGFORM") {
                    //alert('Upload db 1 line');
                    uploadSettingForm();
                } else if (settingform == "EXPORT_DBSETTINGFORM") {
                    var url_export = './models/reportSettingForm_conn.php';
                    document.location.href = url_export;
                }
            });

        }

        //@@14: Init toolbar attach to window
        function init_dhxUAToolbar() {
            var UAToolbar = dhxWinsUA.window("windowViewUA").attachToolbar();
            UAToolbar.addText("DATABASE_UA",0,"<a style='font-size:12pt;font-weight:bold;color:blue;'>Thực hiện:</a>");
            UAToolbar.setIconsPath("./assets/dhtmlx/common/imgs/");
            UAToolbar.addButton("UPLOAD_UA",4, "UPLOAD DATABASE UA", "xlsx.gif", null);
            UAToolbar.addText("UPLOAD_UA_TXT",5, "|", "", null);
            UAToolbar.addButton("EXPORT_UA",8, "EXPORT DATABASE UA", "downloads.gif", null);

            UAToolbar.attachEvent("onClick", function(ua_db) {
                //1. upload
                if (ua_db == "UPLOAD_UA") {
                    uploadUAFile();
                } else if (ua_db == "EXPORT_UA") {
                    var url_export = './models/reportUA_conn.php';
                    document.location.href = url_export;
                }
            });

        }

        /** GET ALL ************************************************************************/
        //@1. get SOLINE INPUT (from user): OK
        function getSOLINE()
        {
            //get Soline input
            input_so = ToolbarMain.getInput("SO");
            input_so.focus(); // set focus
            input_so.onkeypress = function(event) {
                var keycode = (event.keyCode ? event.keyCode : event.which);

                if(keycode == '13') {

                    var getSOLINE = $(this).val();
                    getSOLINE = getSOLINE.trim();

                    if(!getSOLINE.length>0){
                        alert("[ERROR 01.01]. BẠN CHƯA NHẬP SOLINE");
                        return false;
                    }
                    else {
                        checkSOLINE(getSOLINE);	//ok
                    }

                }

            }//input

        }//END getSOLINE()

        //@2: check SOLINE input: OK
        function checkSOLINE(SO_LINE)
        {
            var url_check =  './views/checkSOLINE.php?SO_LINE='+SO_LINE;
            $.ajax({
                url: url_check,
                type: "POST",
                data: {data: [SO_LINE]},
                dataType: "json",
                beforeSend: function(x) {
                    if (x && x.overrideMimeType) {
                        x.overrideMimeType("application/j-son;charset=UTF-8");
                    }
                },
                // error: function(){
                //     alert("LỖI HỆ THỐNG, VUI LÒNG LIÊN HỆ QUẢN TRỊ");
                // },
                success: function(result) {
                    status = result.status;
                    message = result.message;
                    checkSOExist = result.checkSOExist;
                    error = result.error;

                    if(status == 1) {//true
                        ITEM_VNSO = result.result_vnso.ITEM;
                        if(checkSOExist == 1) {//true
                            confirm_exist = confirm (result.message + " ĐÃ LÀM LSX. NHẤN OK ĐỂ TIẾP TỤC ĐỂ SỬA ĐƠN HÀNG? ");
                            if(!confirm_exist){
                                location.reload();
                                return false;
                            }

                        }
                        
                        getPrintType(SO_LINE, ITEM_VNSO);

                    }
                    else  {//status == false
                        alert(error +" : "+ message);
                        location.reload();
                        //return false;
                    }

                }//end success



            });

        }//END checkSOLINE()

        //@3. get Print type: OK
        function getPrintType(SO_LINE, ITEM_VNSO)
        {
            var url_get_print_type = './views/getPrintType.php?SOLINE='+SO_LINE+'&ITEM_VNSO='+ITEM_VNSO;
            $.ajax({
                url: url_get_print_type,
                //async: false,
                type: "POST",
                data: {data: [ITEM_VNSO]},
                dataType: "json",
                beforeSend: function(x) { if (x && x.overrideMimeType) { x.overrideMimeType("application/j-son;charset=UTF-8");} },
                error: function(){
                    alert("ITEM: "+ITEM_VNSO+ " KHÔNG TÌM THẤY LOẠI FORM, VUI LÒNG LIÊN HỆ QUẢN TRỊ (*)");
                },
                success: function(result){
                    status = result.status_item;
                    message = result.message;
                    print_type = result.result_item;
                    //alert(status+message +'va '+ print_type);
                    //alert(message);
                    if(status == 1){
                        //print_type = result.result_item;
                        var print_type_text = '';
                        if(print_type=='ua_cbs'){
                            print_type_text = 'UNDER ARMOUR CBS';
                        }else if(print_type=='cbs'){
                            print_type_text = 'COLOR BY SIZE';
                        }else if(print_type=='rfid'){
                            print_type_text = 'RFID';
                        }else if(print_type=='pvh_rfid'){
                            print_type_text = 'PVH RFID';
                        }else if(print_type=='trim'){
                            print_type_text = 'TRIM';
                        }else if(print_type=='trim_macy'){
                            print_type_text = "TRIM MACY'S";
                        } else {
                            alert("Vui lòng kiểm tra lại Setting Form (Chỉ có các form ua_cbs, cbs, rfid, pvh_rfid, trim, trim_macy)");
                            location.reload();
                        }
                        // set cookies
                        setCookie('print_type_rfsb',print_type,365); //@setcookies

                        ToolbarMain.setItemText("Title","<a style='font-size:20pt;font-weight:bold'>"+print_type_text+"</a>");

                        initLayout();
                        
                        // init to load Grid
                        initSoGrid();

                        
                        // load grid
                        // kiểm tra xem item có nằm trong danh sách item combine (Ryo gửi). Nếu có thì load theo cách mới. Không có thì giữ nguyên
                        // if (SO_LINE.length == 8 ) {

                        // }
                        checkCombine(SO_LINE);
                        
                    }else{
                        alert(message);
                        //setCookie('print_type_rfsb',print_type,0);  //delete cookie
                        location.reload();
                        return false;
                    }
                }
            });
        }//END getPrintType()



        // check combine
        function checkCombine(SO_LINE) 
        {    
            // url
            var url = './models/checkCombine_conn.php?SO_LINE='+SO_LINE;
            var url_load_grid = '';
            
            $.ajax({
                url: url,
                //async: false,
                type: "POST",
                data: {data: [SO_LINE]},
                dataType: "json",
                beforeSend: function(x) { if (x && x.overrideMimeType) { x.overrideMimeType("application/j-son;charset=UTF-8");} },
                error: function(){ alert("CÓ LỖI CHECK COMBINE. VUI LÒNG LIÊN HỆ QUẢN TRỊ"); },
                success: function(result){

                    if (result.status == false ) {
                        url_load_grid = './models/loadGridSO_conn.php?SO_LINE='+SO_LINE;
                    } else {
                        
                        alert(result.message);

                        if (result.check == 1 ) { // NIKE WORLDON
                            url_load_grid = './models/loadOrderNikeWorldon_conn.php?SO_LINE='+SO_LINE;
                        } else if (result.check == 2 ) { // NIKE TINH LOI
                            url_load_grid = './models/loadOrderNikeTinhLoi_conn.php?SO_LINE='+SO_LINE;
                        }

                        
                    }
                    
                    // console.log('url_load_grid: ' + url_load_grid);
                    // return false;
                    
                    setCookie('checkCombine',result.check,365); //@setcookies
                    loadGridSO(SO_LINE, url_load_grid);

                }
            });
        }


        //@4: get GPM OK
        function getGPM(SO_LINE){
            // call ajax
            var url_get_gpm = './models/getGPM_conn.php?SO_LINE='+SO_LINE;
            $.ajax({
                url: url_get_gpm,
                async: false,
                type: "POST",
                data: {},
                dataType: "json",
                beforeSend: function(x) {
                    if (x && x.overrideMimeType) {
                        x.overrideMimeType("application/j-son;charset=UTF-8");
                    }
                },
                success: function(result){
                    if(result.status){
                        input_gpm = ToolbarMain.getInput("GPM");
                        input_gpm.value = result.data;
                        input_gpm.focus(); // set focus
                    }else{
                        input_gpm = ToolbarMain.getInput("GPM");
                        input_gpm.focus(); // set focus
                    }
                }
            });
        }

        /*@5 minhvo *************/
        function getSizeCBSGrid(){
            if(print_type=='ua_cbs'||print_type=='cbs'){
                if(checkID){
                    
                    so_line_arr= [];
                    SoGrid.forEachRow(function(id){
                        var checked = SoGrid.cells(id,0).getValue();
                        if(checked>0){
                            var so = SoGrid.cells(id,1).getValue();
                            var line = SoGrid.cells(id,2).getValue();
                            so_line_arr.push(so+"-"+line);
                        }
                    });        
                    var jsonObjects = {"so_lines": so_line_arr, print_type : print_type};
                    var url_get_size = './models/getSizeCBSGrid_conn.php?print_type='+print_type;
                    $.ajax({
                        url: url_get_size,
                        async: false,
                        type: "POST",
                        data: {data: JSON.stringify(jsonObjects) },
                        dataType: "json",
                        beforeSend: function(x) {
                            if (x && x.overrideMimeType) {
                            x.overrideMimeType("application/j-son;charset=UTF-8");
                            }
                        },
                        success: function(result) {
                            // check 
                            if(result.status){   
                                loadSizeGrid(result);
                                initMaterialGrid(); 
                                loadMaterialCBSGrid();

                            }else{
                                alert('SO NÀY KHÔNG LẤY ĐƯỢC SIZE VUI LÒNG NHẬP SỐ SIZE!!!');

                                initMaterialGrid();
                                if(print_type=='ua_cbs'){
                                    getBaseRoll(); // get base roll pass to ....
                                }							
                                //updateSize();
                                //updateMaterialNewSize();						
                            }                    
                        },
                        error: function () {
                            alert("Không lấy được size. Vui lòng kiểm tra automail (cột: VIRABLE_BREAKDOWN_INSTRUCTIONS) hoặc liên hệ người nhập size !");
                        }
                    });               
                }
            }        
        }
    
        //@6: minhvo: get material data if ua_cbs
        var itemMain,poMain,rboMain;
        var material_des = [];
        var result_des;
        function getListDes() {
            var url_get_process = './models/get_des.php?item='+poMain;
            // console.log('url_get_process: ' + url_get_process);
            $.ajax({
            url: url_get_process,
                type: "POST",
                data: {data: ''},
                dataType: "json",
                beforeSend: function(x) {
                    if (x && x.overrideMimeType) {
                        x.overrideMimeType("application/j-son;charset=UTF-8");
                    }
                },
                success: function(result) {
                    if(result){
                        result_des = result;
                        if(result.length>0){
                            // update
                            //var check_distinct = 0;
                            for (var i=0; i<MaterialGrid.getRowsNum();i++){
                                var code = MaterialGrid.cellByIndex(i,0).getValue().trim();
                                // console.log('code: ' + code);
                                for (var j=0; j<result.length;j++){
                                    // // if(code!==result[j]['material_code']){
                                    // //     check_distinct = 1;
                                    // // }
                                    //Doan code kiem tra de thay doi j cho phu hop
                                    if(code===result[j]['material_code']){
                                        MaterialGrid.cellByIndex(i,1).setValue(result[j]['material_des']);
                                    }
                                }
                            }
                        }
                    }
                }
            });
        }

        //@6: get base_roll if ua_cbs
        var base_roll = [];
        function getBaseRoll(){
            if(checkID){
                var item = itemMain;
                var url_get_base_roll = './models/get_base_roll_from_item.php?data='+item;
                $.ajax({
                    url: url_get_base_roll,
                    async: false,
                    type: "POST",
                    data: {data:  item},
                    dataType: "json",
                    beforeSend: function(x) { if (x && x.overrideMimeType) { x.overrideMimeType("application/j-son;charset=UTF-8"); } },
                    success: function(result) {
                        // check
                        if(result.status){
                            base_roll = result.data;
                        }else{
                            alert('ITEM NÀY KHÔNG LẤY ĐƯỢC VẬT TƯ VUI LÒNG CẬP NHẬT!!!');
                            return false;
                        }
                    }
                });
            }
        }

        //@7 get size if cbs
        function getMsColor(){
                var url_get_process = './models/getMSColor_conn.php';
                $.ajax({
                url: url_get_process,
                    type: "POST",
                    async: false,
                    data: {data: ''},
                    dataType: "json",
                    beforeSend: function(x) {
                        if (x && x.overrideMimeType) {
                            x.overrideMimeType("application/j-son;charset=UTF-8");
                        }
                    },
                    success: function(result) {
                        if(result){
                            result_ms_color = result;
                        }
                    }
                });
        }

        //@8: get scrap
        function getScrap(RBO){
                var url_get_process = './models/getScrap_conn2.php?RBO='+RBO;
                $.ajax({
                url: url_get_process,
                    type: "POST",
                    async: false,
                    data: {data: ''},
                    dataType: "json",
                    beforeSend: function(x) {
                        if (x && x.overrideMimeType) {
                            x.overrideMimeType("application/j-son;charset=UTF-8");
                        }
                    },
                    success: function(result) {
                        if(result){
                            scrap_percent =  result.scrap_percent;
                        }
                    }

                });
        }

        /** LOADER ALL ************************************************************************/

        //@1:  load Grid SO (GridSO, Form NO, sizeSO, Grid material)
        var itemMain,poMain,rboMain;
        function loadGridSO(SO_LINE, url_load_grid)
        {
            var dataSo={rows:[]};
            
            $.ajax({
                url: url_load_grid,
                //async: false,
                type: "POST",
                data: {data: [SO_LINE]},
                dataType: "json",
                beforeSend: function(x) {
                    if (x && x.overrideMimeType) {
                        x.overrideMimeType("application/j-son;charset=UTF-8");
                    }
                },
                success: function(result){
                    if(result.status){
                        var length = result.data.length;
                        // Check trường hợp Promise date và request date. Nếu trống thì báo hoặc request date > promise date !!!!!!
                        var data_check_date = result.data[0].data;
                        promise_date_check = data_check_date[6]; 
                        var promise_date_format = new Date(promise_date_check);
                        request_date_check = data_check_date[7];
                        var request_date_format = new Date(request_date_check);
                        console.log('promise_date_format: ' + promise_date_format);
                        console.log('request_date_format: ' + request_date_format);

                        if (promise_date_check == '1970-01-01') {
                            var promise_date_alert = confirm('Không lấy được PROMISE DATE, bạn có muốn tiếp tục?');
                            if (!promise_date_alert) {
                                location.reload();
                                return false;
                            }
                        }

                        if (request_date_check == '1970-01-01') {
                            var request_date_alert = confirm('Không lấy được REQUEST DATE, bạn có muốn tiếp tục?');
                            if (!request_date_alert) {
                                location.reload();
                                return false;
                            }
                        }

                        if (promise_date_format < request_date_format) {
                            var greater_than_date = confirm('Ngày REQUEST DATE lớn hơn PROMISE DATE, bạn có muốn tiếp tục?');
                            if (!greater_than_date) {
                                location.reload();
                                return false;
                            }
                        }


                        for(var i = 0;i<length;i++){
                            dataSo.rows.push(result.data[i]);
                        }
                        SoGrid.parse(dataSo,"json");

                        checked_SOLINE = []; // reset checked_SOLINE when filter
                        for (var i=0; i<SoGrid.getRowsNum();i++){
                            so_line = SoGrid.cellByIndex(i,0).getValue().trim();
                            grid_id = SoGrid.getRowId(i);
                            var obj = {so_line:so_line,grid_id:grid_id};
                            checked_SOLINE.push(obj);
                        }
                        // load list item
                        if(checked_SOLINE.length>0){
                            var so = SoGrid.cells(checked_SOLINE[0]['grid_id'],1).getValue();
                            var line = SoGrid.cells(checked_SOLINE[0]['grid_id'],2).getValue();
                            itemMain = SoGrid.cells(checked_SOLINE[0]['grid_id'],5).getValue();
                            poMain = SoGrid.cells(checked_SOLINE[0]['grid_id'],4).getValue();
                            rboMain = SoGrid.cells(checked_SOLINE[0]['grid_id'],10).getValue();
                            heightMain = SoGrid.cells(checked_SOLINE[0]['grid_id'],14).getValue();

                            checkSo = so+"-"+line;
                            checkID = checked_SOLINE[0]['grid_id'];
                            if(print_type=='ua_cbs'||print_type=='cbs'){//ms color
                                initFormNO();
                                initSizeGrid();//ink or size
                                //init MaterialGrid đã được thêm vào trong trong hàm loadMaterialCBSGrid (trong initSizeGrid)
                                // update des
                                if(print_type=='ua_cbs'){
                                    getListDes();
                                }
                            }else if(print_type=='ua_no_cbs'||print_type=='rfid'){
                                initFormNO();
                                console.log("Tui day");
                                initSizeGrid();
                                initMaterialGrid();
                            }else if(print_type=='pvh_rfid' || print_type=='trim'||print_type=='trim_macy'){
                                initFormNO();
                                initMaterialGrid();
                                loadMaterialPVH();
                            }
                        }

                        // So sánh số lượng con nhãn trong automail và tổng số lượng size, Nếu không giống nhau thì báo lỗi.
                        if(print_type=='ua_cbs'||print_type=='cbs'){
                            var po_qty_total_automail = myForm.getItemValue('frm_qty_total');
                            po_qty_total_automail  = Number(po_qty_total_automail);

                            if (po_qty_total_automail !== size_qty_total_check_correct) {
                                alert('Tổng số lượng của SIZE và số lượng trong AUTOMAIL không giống nhau. Vui lòng kiểm tra lại.');
                            }
                        }
                        
                    }else{
                        alert(result.mess);
                        location.reload();
                    }
                },
                error: function () {
                    alert("KHÔNG LOAD ĐƯỢC DATA TỪ MASTER FILE, VUI LÒNG KIỂM TRA LẠI INTERNAL ITEM ĐÃ UPLOAD");
                }
            });
        }	//end @: load Grid SO

        //@2: load material no cbs OK
        function loadMaterialNoCBS()
        {
            if(print_type=='ua_no_cbs'||print_type=='rfid'){
                var index = 0;
                SoGrid.forEachRow(function(id){
                    var size_check_tmp = SoGrid.cellByIndex(index,0).getValue();
                    if(size_check_tmp){
                        var MATERIAL_CODE = SoGrid.cellByIndex(index,16).getValue();
                        var MATERIAL_DES = SoGrid.cellByIndex(index,18).getValue();
                        var MATERIAL_QTY = SoGrid.cellByIndex(index,17).getValue();
                        var newId = MaterialGrid.uid();
                        MaterialGrid.addRow(newId,[MATERIAL_CODE,MATERIAL_DES,MATERIAL_QTY]);
                    }
                    index++;
                });
            }//if
        }//end function

        //@3: load Size no cbs OK
        function loadInkNoCBS(){
            if(print_type=='ua_no_cbs'||print_type=='rfid'){
                var index = 0;
                SoGrid.forEachRow(function(id){
                    var size_check_tmp = SoGrid.cellByIndex(index,0).getValue();
                    if(size_check_tmp){
                        var INK_CODE = SoGrid.cellByIndex(index,13).getValue();
                        var INK_DES = SoGrid.cellByIndex(index,15).getValue();
                        var INK_QTY = SoGrid.cellByIndex(index,14).getValue();
                        var newId = SizeGrid.uid();
                        SizeGrid.addRow(newId,[INK_CODE,INK_DES,INK_QTY]);
                    }
                    index++;
                });
            }//if
        }//end function

        //@4 load size to grids
        function loadSizeGrid(result){
            if(print_type=='ua_cbs'){
                listSize = result.size;
                console.log("list size: "+ listSize );
                dataSize = result.data;
                countSize = listSize.length;
                var list_item_size = [];
                var label_item = rboMain+"-"+itemMain;
                for(var i=0;i<countSize;i++){
                    list_item_size.push(itemMain+listSize[i]);
                }
                console.log('Item 1: ' + itemMain );
                console.log('list_item_size 1: ' + JSON.stringify(list_item_size) );
                // get base roll from item (order item)
                var url_get_roll = './models/get_roll_from_item.php?data='+list_item_size;
                var roll_array =[];
                $.ajax({
                    url: url_get_roll,
                    async: false,
                    type: "POST",
                    data: {data: list_item_size},
                    dataType: "json",
                    beforeSend: function(x) {
                        if (x && x.overrideMimeType) {
                            x.overrideMimeType("application/j-son;charset=UTF-8");
                        }
                    },
                    success: function(result) {
                        // check 
                        if(result.status){                            
                            roll_array = result.data;
                            if(countSize){
                                SizeGrid.clearAll(); // clear and load            
                                for(var k = 0;k<countSize;k++){
                                    var currentSize = listSize[k];
                                    
                                    //currentSize_baseroll = currentSize.toLowerCase();
                                    currentSize_baseroll = currentSize;
                                    var currentSo = checkSo;
                                    if(typeof dataSize[currentSo]!=="undefined"){
                                        if(typeof dataSize[currentSo][currentSize]!== 'undefined'){
                                        qty_so = dataSize[currentSo][currentSize];   
                                        }else{
                                            qty_so = '';
                                        }                              
                                    }else{
                                        qty_so = '';
                                    }
                                    // get roll base
                                    var roll_base = '';
                                    if(typeof roll_array[currentSize_baseroll]!=="undefined"){
                                        roll_base = roll_array[currentSize_baseroll];
                                    } else {
                                        roll_base = roll_array[currentSize];
                                    }

                                    tmp_array = [currentSize,label_item,roll_base,qty_so];
                                    SizeGrid.addRow(k+200,tmp_array); 
                                    // Lấy giá trị tổng số lượng của size để so sánh với số lượng trong automail
                                    size_qty_total_check_correct += Number(qty_so);
                                }
                                var rowID = countSize;
                                
                                for(var h=1;h<=20-countSize;h++){ // 20 SO
                                    //add row  
                                    tmp_array = ['','','',''];
                                    SizeGrid.addRow(rowID+h+300,tmp_array);
                                    
                                }
                            }else{

                            }
                        }else{
                            alert(result.mess);
                            return false;
                        }
                    }
                }); 
            }else if(print_type=='cbs'){
                listSize = result.size;
                dataSize = result.data;//soline array 
                countSize = listSize.length;
                if(countSize){
                    label_item = rboMain+'-'+itemMain;
                    SizeGrid.clearAll(); // clear and load            
                    for(var k = 0;k<countSize;k++){
                        var currentSize = listSize[k];
                        var currentSo = checkSo;
                        if(typeof dataSize[currentSo]!=="undefined"){
                            if(typeof dataSize[currentSo][currentSize]!== 'undefined'){
                                qty_so = dataSize[currentSo][currentSize];   
                            }else{
                                qty_so = '';
                            }                              
                        }else{
                            qty_so = '';
                        }
                        // get roll base
                        
                        // base_roll = 9 ký tự đầu của internal_item + size + 3 ký tự cuối internal_item

                        var roll_base = poMain.substr(0,9)+currentSize+poMain.substr(-3,3);           
                        tmp_array = [currentSize,label_item,roll_base,qty_so];
                        SizeGrid.addRow(k+200,tmp_array); 	
                        // Lấy giá trị tổng số lượng của size để so sánh với số lượng trong automail
                        size_qty_total_check_correct += Number(qty_so);
                    }
                    var rowID = countSize;
                    for(var h=1;h<=9;h++){ // 20 SO
                        //add row  
                        tmp_array = ['','','',''];
                        SizeGrid.addRow(rowID+h+300,tmp_array);
                    }
                }
            }                 
        }  	

        //@5: load material cbs
        result_ms_color = '';
        var total_qty_material = 0;
        var total_qty_material_round = 0;
        function loadMaterialCBSGrid()
        {

            //get scrap_percent
            getScrap(rboMain);
            if(SizeGrid.getRowsNum()>0){
                if(print_type=='ua_cbs'){
                    var index = 0;
                    SizeGrid.forEachRow(function(id){
                        var size_check_tmp = SizeGrid.cellByIndex(index,0).getValue();

                        if(size_check_tmp){
                            var base_roll = SizeGrid.cellByIndex(index,2).getValue();
                            var material_qty_new = SizeGrid.cellByIndex(index,3).getValue();
                            //Sử dụng công thức làm qty * số scrap: lầm tròn lên
                            material_qty_new = Number(material_qty_new*scrap_percent);   
                            //Làm tròn tăng lên
                            material_qty_new = Math.ceil(material_qty_new);  
                            total_qty_material +=material_qty_new;
                            var newId = MaterialGrid.uid();
                            MaterialGrid.addRow(newId,[base_roll,'',material_qty_new]);
                            
                        }
                        index++;
                    });
                    // updateMaterial(); ///@@@@@@
                }else if(print_type=='cbs'){
                    getMsColor();
                    if(result_ms_color.length>0){
                        var index = 0;
                        for(var k=0;k<SizeGrid.getRowsNum();k++){
                            var check_item_color = 0;
                            var size_check_tmp = SizeGrid.cellByIndex(k,0).getValue();//687
                            if(size_check_tmp){
                                var base_roll = SizeGrid.cellByIndex(k,2).getValue();
                                
                                console.log('base roll: ' + base_roll);

                                for(var j=0;j<result_ms_color.length;j++){
                                    var item_color = result_ms_color[j]['item_color'];
                                    if(base_roll.trim()==item_color.trim()){
                                        var material_code = result_ms_color[j]['material_code'];
                                        if(!material_code){
                                            alert('KHÔNG LẤY ĐƯỢC MATERIAL CODE TỪ ITEM COLOR: '+item_color);
                                            location.reload();
                                            return false;
                                        }
                                        var material_des = result_ms_color[j]['material_des'];
                                        var qty = SizeGrid.cellByIndex(k,3).getValue();
                                        if(!qty.trim()){
                                            qty = 0;
                                        }
                                        var material_qty = Math.ceil(qty*scrap_percent);
                                        var newId = MaterialGrid.uid();
                                        total_qty_material+=material_qty;
                                        MaterialGrid.addRow(newId,[material_code,material_des,material_qty]);
                                        check_item_color = 1;
                                        //Nếu trong thì dừng vòng lặp
                                        // if (!material_code && !material_qty) {
                                        //     break;
                                        // }
                                    }
                                }//for 2
                                
                                if(!check_item_color){
                                    alert('ITEM COLOR: '+base_roll+' KHÔNG TỒN TẠI TRÊN HỆ THỐNG!!!');
                                    location.reload();
                                    return false;
                                }
                                total_qty_material_round = total_qty_material;
                            }
                            
                        }//for 1
                    }




                    // if(result_ms_color.length>0){
                    //     var index = 0;
                    //     for(var k=0;k<SizeGrid.getRowsNum();k++){
                    //         var check_item_color = 0;
                    //         var size_check_tmp = SizeGrid.cellByIndex(index,0).getValue();
                    //         if(size_check_tmp){
                    //             var base_roll = SizeGrid.cellByIndex(index,2).getValue();
                    //             for(var j=0;j<result_ms_color.length;j++){
                    //                 var item_color = result_ms_color[j]['item_color'];
                    //                 if(base_roll.trim()==item_color.trim()){
                    //                     var material_code = result_ms_color[j]['material_code'];
                    //                     if(!material_code){
                    //                         alert('KHÔNG LẤY ĐƯỢC MATERIAL CODE TỪ ITEM COLOR: '+item_color);
                    //                         location.reload();
                    //                         return false;
                    //                     }
                    //                     var material_des = result_ms_color[j]['material_des'];
                    //                     var qty = SizeGrid.cellByIndex(index,3).getValue();
                    //                     if(!qty.trim()){
                    //                         qty = 0;
                    //                     }
                    //                     var material_qty = qty*scrap_percent;
                    //                     var newId = MaterialGrid.uid();
                    //                     total_qty_material+=material_qty;
                    //                     MaterialGrid.addRow(newId,[material_code,material_des,Math.round(material_qty)]);
                    //                     check_item_color = 1;
                    //                 }
                    //             }
                    //             if(!check_item_color){
                    //                 alert('ITEM COLOR: '+base_roll+' KHÔNG TỒN TẠI TRÊN HỆ THỐNG!!!');
                    //                 location.reload();
                    //                 return false;
                    //             }
                    //             total_qty_material_round = total_qty_material;
                    //         }
                    //         index++;
                    //     }
                    // }
                }
            }
        }

        //@6: load material PVH, TRIM, TRIM_MACY
        function loadMaterialPVH()
        {
                if(SoGrid.getRowsNum()>0){
                    for(var i=0;i<SoGrid.getRowsNum();i++){
                        if(SoGrid.cellByIndex(i,0).getValue()){
                            var material_code = SoGrid.cellByIndex(i,16).getValue().trim();
                            var material_des = SoGrid.cellByIndex(i,18).getValue().trim();
                            var material_qty = SoGrid.cellByIndex(i,17).getValue().trim();
                            var ink_code = SoGrid.cellByIndex(i,13).getValue().trim();
                            var ink_des = SoGrid.cellByIndex(i,15).getValue().trim();
                            var ink_qty = SoGrid.cellByIndex(i,14).getValue().trim();
                            var uniqueID = MaterialGrid.uid();
                            var data_add = [i+1,material_code,material_des,material_qty,ink_code,ink_des,ink_qty];
                            MaterialGrid.addRow(uniqueID,data_add);
                        }
                    }
                }else{
                    alert("KHÔNG LẤY ĐƯỢC DANH SÁCH VẬT TƯ!");
                    return false;
                }
        }

        /* UPDATE ALL*/

        //update material (ua_cbs)
        //var total_qty_material = 0;
        // var total_qty_material_round;	
        function updateMaterial()
        { 
            if(print_type=='ua_cbs'){
                var size_check = SizeGrid.cellByIndex(0,0).getValue();
                var material_display = [];
                //Trường hợp nếu có size
                var material_code_tmp = '';
                var size_check_tmp_2 = '';
                if(size_check){ 
                    MaterialGrid.forEachRow(function(idMaterial){
                        var material_code = MaterialGrid.cells(idMaterial,0).getValue().trim(); 
                        var material_qty = MaterialGrid.cells(idMaterial,2).getValue(); 
                        total_qty_material += material_qty;	
                        
                    
                    });

                    // delete 
                    MaterialGrid.forEachRow(function(idMaterial){
                        var material_qty = MaterialGrid.cells(idMaterial,2).getValue();
                        if(!material_qty){MaterialGrid.deleteRow(idMaterial)}
                    });
                } 

                // if(total_qty_material){total_qty_material_round = total_qty_material;}
                // else{ total_qty_material_round = 0;}			
                // add 9 row
                // // countSize = MaterialGrid.getRowsNum();
                // // var rowID = countSize;
                // // for(var h=1;h<=8;h++){ // 20 SO
                // // 	//add row  
                // // 	tmp_array = ['','',''];
                // // 	MaterialGrid.addRow(rowID+h+300,tmp_array);
                // // }
            }else if(print_type=='cbs'){
                //Trường hợp này sử dụng công thức hàm khác
            }        
        }

        //create PO_NO    
        function createPrefixNO(so_line_0, print_type) 
        {
            
            var url_create_po_no = './models/createPrefixNO_conn.php?so_line_0='+so_line_0+'&print_type='+print_type;
            $.ajax({
                url: url_create_po_no,
                async: false,//sử dụng bất đồng bộ, cẩn thận khai báo biến
                data: {data: ''},
                dataType: "json",
                beforeSend: function(x) { if (x && x.overrideMimeType) { x.overrideMimeType("application/j-son;charset=UTF-8"); } },
                success: function(result) {
                    if(result.status){
                        // po_no_new = result.po_no_new;
                        console.log('po: ' + result.po_no_new);
                        myForm.setItemValue("frm_no", result.po_no_new );
                    }else{
                        alert(result.message);
                        return false;
                    }
                }
            });
            // return  po_no_new;

        }//end create_PO_NO

        function htmlspecialchars(str) 
        {
            if ( typeof (str) == "string") {
                str = str.replace(/&/g, "&");
                str = str.replace(/"/g, '"');
                str = str.replace(/'/g, "'");
                str = str.replace(/</g, "<");
                str = str.replace(/>/g, ">");
            }
            return str;
        }

        function un_htmlspecialchars(str) 
        {
            if (typeof(str) == "string") {
                str = str.replace(/>/ig, ">");
                str = str.replace(/</ig, "<");
                str = str.replace(/'/g, "'");
                str = str.replace(/"/ig, '"');
                str = str.replace(/&/ig, '&');
            }
            return str;
        }

        /* SAVE DATA */
        function saveDatabase() 
        {
            
            var checkCombine = 0;
            if (getCookie('checkCombine') ) { checkCombine = getCookie('checkCombine'); }
            

            var save_formNO = [];
            var PO_FORM_TYPE = print_type;

            var PO_GPM                  = ToolbarMain.getValue('GPM'); 
            var SO_0                    = SoGrid.cellByIndex(0,1).getValue();
            var LINE_0                  = SoGrid.cellByIndex(0,2).getValue();
            var PO_SO_LINE              = SO_0+'-'+LINE_0;

            var PO_COUNT_SO_LINE = SoGrid.getRowsNum();
            
            // var PO_COUNT_SO_LINE        = SoGrid.cellByIndex(0,24).getValue();
            // PO_COUNT_SO_LINE = Number(PO_COUNT_SO_LINE);

            var PO_NO                   = myForm.getItemValue('frm_no');
                
            //Đoạn code check po no của form
            var po_no_new_tmp = PO_NO;
            if ( PO_FORM_TYPE == 'trim' || PO_FORM_TYPE == 'trim_macy' ) {
                if ( po_no_new_tmp.length < 11   || po_no_new_tmp.length > 17 ) {
                    alert('[ErrorPO01.01]. Độ dài PO NO không đúng. Vui lòng kiểm tra lại!');
                    return false;
                } else {
                    if ( po_no_new_tmp.slice(0,7) !== 'TRIM.RF' ) {//không có TRIM.RF po_no_new_tmp.indexOf('TRIM.RF') == -1
                        alert('[ErrorPO02.01]. PO NO không đúng định dáng. Vui lòng kiểm tra lại!');
                        return false;
                    } else {
                        PO_NO = po_no_new_tmp;
                    }
                }
                
            } else {//các form còn lại
                if ( po_no_new_tmp.length < 6 || po_no_new_tmp.length > 12 ) {
                    alert('[ErrorPO01.02]. Độ dài PO NO không đúng. Vui lòng kiểm tra lại!');
                    return false;
                } else {
                    if ( po_no_new_tmp.slice(0,2) !== 'RF' ) {//không có TRIM.RF po_no_new_tmp.indexOf('TRIM.RF') == -1
                        alert('[ErrorPO02.02]. PO NO không đúng định dáng. Vui lòng kiểm tra lại!');
                        return false;
                    } else {
                        PO_NO = po_no_new_tmp;
                    }
                }
            }
            //end 

            var PO_SAVE_DATE            = myForm.getItemValue('frm_create_date');//w
            var PO_SHIP_TO_CUSTOMER     = myForm.getItemValue('frm_ship_to');
            var PO_ORDERED_DATE         = myForm.getItemValue('frm_order');
            var PO_INTERNAL_ITEM        = myForm.getItemValue('frm_po');
            var PO_ORDER_ITEM           = myForm.getItemValue('frm_item');
            var PO_REQUEST_DATE         = myForm.getItemValue('frm_req');
            var PO_PROMISE_DATE         = myForm.getItemValue('frm_pd');//w
            var PO_LABEL_SIZE           = myForm.getItemValue('frm_size');
            var PO_RBO                  = myForm.getItemValue('frm_rbo');

            var PO_MATERIAL_CODE        = myForm.getItemValue('frm_material');
            var PO_MATERIAL_QTY         = myForm.getItemValue('frm_material_qty');
            PO_MATERIAL_QTY             = Number(PO_MATERIAL_QTY);
            var PO_MATERIAL_DES         = myForm.getItemValue('frm_material_des');//w
            var PO_MATERIAL_REMARK      = myForm.getItemValue('frm_material_remark');
            var PO_INK_CODE             = myForm.getItemValue('frm_ink');
            var PO_INK_QTY              = myForm.getItemValue('frm_ink_qty');
            var PO_INK_DES              = myForm.getItemValue('frm_ink_des');//w
            var PO_INK_REMARK           = myForm.getItemValue('frm_ink_remark');
            var PO_QTY                  = myForm.getItemValue('frm_qty_total');
            PO_QTY                      = Number(PO_QTY);
            var PO_MAIN_SAMPLE_LINE     = myForm.getItemValue('frm_main_sample_line');//w
            var PO_SAMPLE_15PCS         = myForm.getItemValue('frm_sample15');
            var PO_SAMPLE               = myForm.getItemValue('frm_sample');
            PO_SAMPLE                   = Number(PO_SAMPLE);
            //frm_date_received frm_file
            var PO_DATE_RECEIVED        = myForm.getItemValue('frm_date_received');
            var PO_FILE_DATE_RECEIVED   = myForm.getItemValue('frm_file');

            var PO_CS                   = SoGrid.cellByIndex(0,9).getValue();
            var PO_ORDER_TYPE_NAME      = SoGrid.cellByIndex(0,26).getValue();

            //CHECK form data
            if ( !PO_SAVE_DATE ) {
                alert("Ngày tạo đơn hàng không được trống (@1)");
                return false;
            }
            if ( !PO_MATERIAL_DES || !PO_INK_DES ) {
                confirm_des = confirm("Material des hoặc Ink des TRỐNG. Bạn có muốn tiếp tục tạo đơn? * ");
                if(!confirm_des){
                    location.reload();
                    return false;
                }
            }

            var obj_formNo={PO_NO:PO_NO,PO_SO_LINE:PO_SO_LINE,PO_FORM_TYPE:PO_FORM_TYPE,PO_INTERNAL_ITEM:PO_INTERNAL_ITEM,PO_ORDER_ITEM:PO_ORDER_ITEM,
                            PO_GPM:PO_GPM,PO_RBO:PO_RBO,PO_SHIP_TO_CUSTOMER:PO_SHIP_TO_CUSTOMER,PO_CS:PO_CS,PO_QTY:PO_QTY,
                            PO_SAVE_DATE:PO_SAVE_DATE,PO_ORDERED_DATE:PO_ORDERED_DATE,PO_REQUEST_DATE:PO_REQUEST_DATE,PO_PROMISE_DATE:PO_PROMISE_DATE,PO_LABEL_SIZE:PO_LABEL_SIZE,
                            PO_MATERIAL_CODE:PO_MATERIAL_CODE,PO_MATERIAL_QTY:PO_MATERIAL_QTY,PO_MATERIAL_DES:PO_MATERIAL_DES,PO_MATERIAL_REMARK:PO_MATERIAL_REMARK,PO_INK_CODE:PO_INK_CODE,
                            PO_INK_QTY:PO_INK_QTY,PO_INK_DES:PO_INK_DES,PO_INK_REMARK:PO_INK_REMARK,PO_MAIN_SAMPLE_LINE:PO_MAIN_SAMPLE_LINE,PO_SAMPLE_15PCS:PO_SAMPLE_15PCS,
                            PO_SAMPLE:PO_SAMPLE,PO_COUNT_SO_LINE:PO_COUNT_SO_LINE,PO_ORDER_TYPE_NAME:PO_ORDER_TYPE_NAME,PO_DATE_RECEIVED:PO_DATE_RECEIVED,PO_FILE_DATE_RECEIVED:PO_FILE_DATE_RECEIVED}//save to obj_form
            save_formNO.push(obj_formNo);

            //get data grid so
            var save_GridSO = [];
            for (var i=0; i<SoGrid.getRowsNum();i++){
                var SO                  = SoGrid.cellByIndex(i,1).getValue();
                var LINE                = SoGrid.cellByIndex(i,2).getValue();
                var SO_PO_QTY           = SoGrid.cellByIndex(i,3).getValue();
                SO_PO_QTY               = Number(SO_PO_QTY);
                var SO_INTERNAL_ITEM    = SoGrid.cellByIndex(i,4).getValue();
                var SO_ORDER_ITEM       = SoGrid.cellByIndex(i,5).getValue();
                var CS_NAME             = SoGrid.cellByIndex(i,9).getValue();
                var RBO                 = SoGrid.cellByIndex(i,10).getValue();
                var FORM_TYPE           = SoGrid.cellByIndex(i,20).getValue();
                // var COUNT_SO            = SoGrid.cellByIndex(i,24).getValue();
                // COUNT_SO                = Number(COUNT_SO);
                var COUNT_SO = SoGrid.getRowsNum();
                
                var SO_WIDTH            = SoGrid.cellByIndex(i,11).getValue();
                SO_WIDTH                = Number(SO_WIDTH);
                var SO_HEIGHT           = SoGrid.cellByIndex(i,12).getValue();
                SO_HEIGHT               = Number(SO_HEIGHT);
                var PACKING_INSTRUCTIONS       = SoGrid.cellByIndex(i,22).getValue();
                // var INK_CODE            = SoGrid.cellByIndex(i,13).getValue().trim();
                // var INK_QTY             = SoGrid.cellByIndex(i,14).getValue().trim();
                // var INK_DES             = SoGrid.cellByIndex(i,15).getValue().trim();
                // var INK_REMARK          = SoGrid.cellByIndex(i,28).getValue().trim();
                // var MATERIAL_CODE       = SoGrid.cellByIndex(i,16).getValue().trim();
                // var MATERIAL_QTY        = SoGrid.cellByIndex(i,17).getValue().trim();
                // var MATERIAL_DES        = SoGrid.cellByIndex(i,18).getValue().trim();
                // var MATERIAL_REMARK     = SoGrid.cellByIndex(i,27).getValue().trim();
                
                // cho đơn hàng NIKE + WORLDON
                var REMARK_SO_COMBINE     = SoGrid.cellByIndex(i,31).getValue().trim();

                if( !SO || !LINE  ){
                    alert("SOLINE trống. Vui lòng kiểm tra lại(@2)");
                    return false;
                }
                var SO_LINE = SO+'-'+LINE;
                var SO_PO_NO = PO_NO;
                var obj = {SO_PO_NO:SO_PO_NO,SO_LINE:SO_LINE,SO_PO_QTY:SO_PO_QTY,SO_INTERNAL_ITEM:SO_INTERNAL_ITEM,SO_ORDER_ITEM:SO_ORDER_ITEM,SO_WIDTH:SO_WIDTH,SO_HEIGHT:SO_HEIGHT,COUNT_SO:COUNT_SO,PACKING_INSTRUCTIONS:PACKING_INSTRUCTIONS,REMARK_SO_COMBINE:REMARK_SO_COMBINE};
                save_GridSO.push(obj);
            }//end for
            
            //********************************* */
            //1. save form type: rfid, ua_no_cbs
            if (FORM_TYPE == 'rfid' || FORM_TYPE == 'ua_no_cbs' ) {
                //get data INK
                var save_ink = [];
                for (var i=0; i<SizeGrid.getRowsNum();i++){
                    var SO                  = SoGrid.cellByIndex(i,1).getValue();
                    var LINE                = SoGrid.cellByIndex(i,2).getValue();
                    var INK_CODE            = SizeGrid.cellByIndex(i,0).getValue();
                    var INK_QTY             = SizeGrid.cellByIndex(i,2).getValue();
                    INK_QTY                 = Number(INK_QTY);
                    var INK_DES             = SizeGrid.cellByIndex(i,1).getValue();
                    var INK_COUNT           = SizeGrid.getRowsNum();

                    //trường hợp có dòng trống, dừng tại đây
                    if (!INK_CODE && !INK_DES) { //&& !INK_QTY
                        break;
                    }

                    if( !INK_CODE  ){
                        alert("INK CODE trống. Vui lòng kiểm tra lại(@3)");
                        return false;
                    }
                    var INK_SO_LINE = SO+'-'+LINE;
                    var INK_PO_NO = PO_NO;
                    var obj = {INK_PO_NO:INK_PO_NO,INK_SO_LINE:INK_SO_LINE,INK_CODE:INK_CODE,INK_QTY:INK_QTY,INK_DES:INK_DES,INK_COUNT:INK_COUNT};
                    save_ink.push(obj);
                }//end for

                //get data MATERIAL
                var save_material_no_cbs = [];
                for (var i=0; i<MaterialGrid.getRowsNum();i++){
                    var SO                      = SoGrid.cellByIndex(i,1).getValue();
                    var LINE                    = SoGrid.cellByIndex(i,2).getValue();
                    var MN_MATERIAL_CODE        = MaterialGrid.cellByIndex(i,0).getValue();
                    var MN_MATERIAL_QTY         = MaterialGrid.cellByIndex(i,2).getValue();
                    MN_MATERIAL_QTY             = Number(MN_MATERIAL_QTY);
                    var MN_MATERIAL_DES         = MaterialGrid.cellByIndex(i,1).getValue();
                    var MN_PO_SO_LINE           = SO+'-'+LINE;
                    var MN_COUNT                = MaterialGrid.getRowsNum();
                    var MN_PO_NO                = PO_NO;
                    //trường hợp có dòng trống, dừng tại đây
                    if (!MN_MATERIAL_CODE && !MN_MATERIAL_QTY) {
                        alert("MATERIAL CODE hoặc MATERIAL QTY trống. Vui lòng kiểm tra lại(@4)");
                        return false;
                        break;
                    }
                    // 2022-02-09: Bỏ chặn do có một số đơn Material qty = 0 (Beo yêu cầu từ chat)
                    // //dữ liệu khuyết
                    // if( !MN_MATERIAL_CODE || !MN_MATERIAL_QTY  ){
                    //     alert("MATERIAL CODE hoặc MATERIAL QTY trống. Vui lòng kiểm tra lại(@4)");
                    //     return false;
                    // }
                    
                    var obj = {MN_PO_NO:MN_PO_NO,MN_PO_SO_LINE:MN_PO_SO_LINE,MN_MATERIAL_CODE:MN_MATERIAL_CODE,MN_MATERIAL_QTY:MN_MATERIAL_QTY,MN_MATERIAL_DES:MN_MATERIAL_DES,MN_COUNT:MN_COUNT};
                    save_material_no_cbs.push(obj);
                    

                }//end for

                //set jsonObjects
                //var jsonObjects = [];
                var jsonObjects = {
                        "data_formNO": save_formNO,
                        "data_GridSO":save_GridSO,
                        "data_ink":save_ink,
                        "data_material_no_cbs":save_material_no_cbs,
                        "checkCombine": checkCombine
                    };
                

            } //2. save form type: trim, trim_macy and pvh_rfid
            else if (FORM_TYPE == 'trim' || FORM_TYPE == 'trim_macy' || FORM_TYPE == 'pvh_rfid' ) {
                //get data MATERIAL 
                var save_material_ink = [];
                for (var i=0; i<MaterialGrid.getRowsNum();i++){
                    var SO                = SoGrid.cellByIndex(i,1).getValue().trim();
                    var LINE              = SoGrid.cellByIndex(i,2).getValue().trim();
                    var MI_MATERIAL_CODE  = MaterialGrid.cellByIndex(i,1).getValue().trim();
                    var MI_MATERIAL_DES   = MaterialGrid.cellByIndex(i,2).getValue().trim();
                    var MI_MATERIAL_QTY   = MaterialGrid.cellByIndex(i,3).getValue().trim();
                    MI_MATERIAL_QTY       = Number(MI_MATERIAL_QTY);
                    var MI_INK_CODE       = MaterialGrid.cellByIndex(i,4).getValue().trim();
                    var MI_INK_DES        = MaterialGrid.cellByIndex(i,5).getValue().trim();
                    var MI_INK_QTY        = MaterialGrid.cellByIndex(i,6).getValue().trim();
                    MI_INK_QTY            = Number(MI_INK_QTY);
                    var MI_PO_SO_LINE     = SO+'-'+LINE;
                    var MI_COUNT          = MaterialGrid.getRowsNum();
                    var MI_PO_NO          = PO_NO;
                    //trường hợp có dòng trống, dừng tại đây
                    if (!MI_MATERIAL_CODE && !MI_INK_CODE) {
                        break;
                    }
                    //dữ liệu khuyết, báo lỗi
                    if( !MI_MATERIAL_CODE || !MI_MATERIAL_QTY  ){
                        alert("MATERIAL CODE hoặc MATERIAL QTY trống. Vui lòng kiểm tra lại(@5)");
                        return false;
                    } else if (!MI_INK_CODE ){
                        alert("INK CODE trống. Vui lòng kiểm tra lại(@5)");
                        return false;
                    }
                    
                    var obj = {MI_PO_NO:MI_PO_NO,MI_PO_SO_LINE:MI_PO_SO_LINE,MI_MATERIAL_CODE:MI_MATERIAL_CODE,MI_MATERIAL_DES:MI_MATERIAL_DES,MI_MATERIAL_QTY:MI_MATERIAL_QTY,MI_INK_CODE:MI_INK_CODE,MI_INK_DES:MI_INK_DES,MI_INK_QTY:MI_INK_QTY,MI_COUNT:MI_COUNT};
                    save_material_ink.push(obj);
                }//end for
                //set jsonObjects
                var jsonObjects = {
                        "data_formNO": save_formNO,
                        "data_GridSO":save_GridSO,
                        "data_material_ink":save_material_ink
                    };

            }//3. save form type: ua_cbs and cbs
            else if (FORM_TYPE == 'ua_cbs' || FORM_TYPE == 'cbs' ) {
                //get data SIZE 
                var save_size = [];
                var SIZE_COUNT = 0;
                for (var i=0; i<SizeGrid.getRowsNum();i++){
                    var SIZE            = SizeGrid.cellByIndex(i,0).getValue();
                    var BASE_ROLL       = SizeGrid.cellByIndex(i,2).getValue();
                    if( SIZE && BASE_ROLL ){
                        SIZE_COUNT++;
                    }
                }

                for (var i=0; i<SizeGrid.getRowsNum();i++){

                    var SIZE            = SizeGrid.cellByIndex(i,0).getValue();
                    var LABEL           = SizeGrid.cellByIndex(i,1).getValue();
                    var BASE_ROLL       = SizeGrid.cellByIndex(i,2).getValue();
                    var SIZE_QTY        = SizeGrid.cellByIndex(i,3).getValue();
                    SIZE_QTY            = Number(SIZE_QTY);
                    //var SIZE_COUNT      = SizeGrid.getRowsNum();
                    var SIZE_PO_NO      = PO_NO;
                    var SIZE_PO_SO_LINE = PO_SO_LINE;

                    if( !SIZE && !BASE_ROLL ){
                        break;
                    }

                    //@@Tính mực từng size lưu vào data size save, Duyên gửi công thức (20191220): (so luong *  (dai + gap)/1000) * 1.024
                    //Ham làm tròn
                    function roundNumber(num, scale) {
                        if(!("" + num).includes("e")) {
                            return +(Math.round(num + "e+" + scale)  + "e-" + scale);
                        } else {
                            var arr = ("" + num).split("e");
                            var sig = ""
                            if(+arr[1] + scale > 0) {
                            sig = "+";
                            }
                            return +(Math.round(+arr[0] + "e" + sig + (+arr[1] + scale)) + "e-" + scale);
                        }
                    }

                    //get data
                    var gap_size = SoGrid.cells(checkID,19).getValue();
                    gap_size = Number(gap_size);
                    var width_size = SoGrid.cells(checkID,11).getValue();
                    width_size = Number(width_size);
                    var S_INK_QTY = (SIZE_QTY * (width_size + gap_size)/1000) * 1.024;
                    S_INK_QTY = roundNumber(S_INK_QTY,2);

                    if ( !SIZE || !BASE_ROLL ) {
                        alert("SIZE hoặc BASE ROLL trống. Vui lòng kiểm tra lại(@6)");
                        return false;
                    }
                    //SO_LINE_0
                    var obj = {SIZE_PO_NO:SIZE_PO_NO,SIZE_PO_SO_LINE:SIZE_PO_SO_LINE,SIZE:SIZE,LABEL:LABEL,BASE_ROLL:BASE_ROLL,SIZE_QTY:SIZE_QTY,SIZE_COUNT:SIZE_COUNT,S_INK_QTY:S_INK_QTY};
                    save_size.push(obj);
                }//end for

                //get data MATERIAL
                var save_material_cbs = [];
                for (var i=0; i<MaterialGrid.getRowsNum();i++){

                    var M_MATERIAL_CODE       = MaterialGrid.cellByIndex(i,0).getValue();
                    var M_MATERIAL_QTY        = MaterialGrid.cellByIndex(i,2).getValue();
                    M_MATERIAL_QTY            = Number(M_MATERIAL_QTY);
                    var M_MATERIAL_DES        = MaterialGrid.cellByIndex(i,1).getValue();
                    var M_PO_NO               = PO_NO;
                    var M_PO_SO_LINE          = PO_SO_LINE;
                    var M_COUNT               = MaterialGrid.getRowsNum();
                    ////trường hợp có dòng trống, dừng tại đây
                    if(!M_MATERIAL_CODE && !M_MATERIAL_QTY ){
                        break;
                    }


                    if( !M_MATERIAL_CODE || !M_MATERIAL_QTY  ){
                        alert("MATERIAL CODE hoặc MATERIAL QTY trống. Vui lòng kiểm tra lại(@6)");
                        return false;
                    }
                    var obj = {M_PO_NO:M_PO_NO,M_PO_SO_LINE:M_PO_SO_LINE,M_MATERIAL_CODE:M_MATERIAL_CODE,M_MATERIAL_QTY:M_MATERIAL_QTY,M_MATERIAL_DES:M_MATERIAL_DES,M_COUNT:M_COUNT};
                    save_material_cbs.push(obj);
                    
                }//end for


                //set jsonObjects
                var jsonObjects = {
                        "data_formNO": save_formNO,
                        "data_GridSO":save_GridSO,
                        "data_size":save_size,
                        "data_material_cbs":save_material_cbs
                    };

            }
            else {
                alert("KHÔNG CÓ FORM "+FORM_TYPE+". VUI LÒNG CẬP NHẬT FORM MỚI!!!");
                return false;
            }

            sent_jsonObjects = JSON.stringify(jsonObjects);
            console.log('saveDatabase: ' + sent_jsonObjects);
            // return false;
            //handle ajax
            var url_save = './models/saveDatabase_conn.php';
            $.ajax({
                url: url_save,
                async:true,
                type: "POST",
                data: {data:sent_jsonObjects},
                dataType: "json",
                beforeSend: function(x) { if (x && x.overrideMimeType) { x.overrideMimeType("application/j-son;charset=UTF-8"); } },
                success: function(result) {
                    
                    // console.log('result: ' + result);

                    if(result.status == true){
                        ToolbarMain.hideItem('SAVE_NO');
                        ToolbarMain.hideItem('SAVE_NO_N');
                        ToolbarMain.showItem('PRINT_NO');
                        ToolbarMain.showItem('PRINT_NO_N');
                        var is_print = confirm(result.message);
                        //confirm(result.message);
                        if(is_print){
                            printNO(result.PO_NO);
                            //location.reload();
                        }
                    }else{
                        alert(result.message);
                    }
                },
                error: function() {
                    alert('Có lỗi khi save data. Vui lòng liên hệ quản trị! ');
                }

            }); 
            
        }//END savedatabase

        
        /*print*/
        function printNO(PO_NO) 
        {
            var url_print = './models/checkPrintNO_conn.php?PO_NO='+PO_NO;
                $.ajax({
                        url: url_print,
                        type: "POST",
                        data: {},
                        dataType: "json",
                        beforeSend: function(x) {
                            if (x && x.overrideMimeType) {
                                x.overrideMimeType("application/j-son;charset=UTF-8");
                            }
                        },
                        success: function(result) {
                            if(result.status == 1){
                                PRINT_PO_NO = result.PO_NO;
                                var wi = window.open('about:blank', '_blank');
                                wi.location.href = './views/print/printNO.php?PRINT_PO_NO='+PRINT_PO_NO;
                                //var win2 = window.open('about:blank', '_blank');
                                //win2.location.href = './views/print/printNO_Layout.php?PRINT_PO_NO='+PRINT_PO_NO;
                                location.reload();//Chuyển print sang tab mới và reload lại
                            }else{
                                alert(result.message);
                                //location.reload();
                            }
                        },
                        error: function() {
                            alert('Fail Fail Fail ');
                        }

                }); 
        }

        /*VIEW ALL*/
        var viewNOHome;
        function viewNOHome(){

            viewNOHome = LayoutMainHome.cells("a").attachGrid();
            viewNOHome.enableSmartRendering(true);
            viewNOHome.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");		
            viewNOHome.enableMultiselect(true);				
            viewNOHome.init();

            viewNOHome.setNumberFormat("0,000",4,".",","); 
            viewNOHome.setStyle(
                "background-color:navy;color:blue; text-align:center; font-weight:bold;", "","color:red;", ""
            );

            var countAll = 0;
            var countCurrent = 0;
            countNOView();

            var from_date_value = ToolbarMain.getValue("FROM_DATE");
            var to_date_value = ToolbarMain.getValue("TO_DATE");

            viewNOHome.load('./models/viewNOHome_conn.php?from_date_value='+from_date_value+'&to_date_value='+to_date_value);   
    
        }
        
        //@1
        var dhxWins;
        var viewNOGrid;
        function viewNO()
        {
            if(!dhxWins){
                dhxWins= new dhtmlXWindows();// show window form to add length
            }   
            if (!dhxWins.isWindow("windowViewNo")){
                // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
                windowViewNo = dhxWins.createWindow("windowViewNo", 620,65,1274,860);
                windowViewNo.setText("Window View NO");
                /*necessary to hide window instead of remove it*/
                windowViewNo.attachEvent("onClose", function(win){
                    if (win.getId() == "windowViewNo") 
                        win.hide();
                });
                viewNOGrid = windowViewNo.attachGrid();
                viewNOGrid.enableSmartRendering(true);
                viewNOGrid.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter");		
                viewNOGrid.enableMultiselect(true);				
                viewNOGrid.init();
                var from_date_value = ToolbarMain.getValue("FROM_DATE");
                var to_date_value = ToolbarMain.getValue("TO_DATE");
                viewNOGrid.load('./models/viewNO_conn.php?from_date_value='+from_date_value+'&to_date_value='+to_date_value);                
            }else{
                dhxWins.window("windowViewNo").show(); 
            } 
        }

        //@2. view database 1 line (các form đứng k màu)
        var dhxWins_DB1Line;
        var viewDB1LineGrid;
        function viewDB1Line()
        {
            if(!dhxWins_DB1Line){
                dhxWins_DB1Line= new dhtmlXWindows();// show window form to add length
            }   
            if (!dhxWins_DB1Line.isWindow("window_db1line")){
                // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
                window_db1line = dhxWins_DB1Line.createWindow("window_db1line", 10,65,2024,860);
                window_db1line.setText("<a style='font-size:14pt;font-weight:bold;color:red;'>DATABASE 1 LINE</a>");
                /*necessary to hide window instead of remove it*/
                window_db1line.attachEvent("onClose", function(win){
                    if (win.getId() == "window_db1line") 
                        win.hide();
                });
                
            }else{
                dhxWins_DB1Line.window("window_db1line").show(); 
            } 
        }//end viewDB1Line
        

        //@3: view database ms color: form đứng có màu
        var dhxWins_DBMSColor;
        var viewDBMSColorGrid;
        function viewDBMSColor()
        {
            if(!dhxWins_DBMSColor){
                dhxWins_DBMSColor= new dhtmlXWindows();// show window form to add length
            }   
            if (!dhxWins_DBMSColor.isWindow("window_DBMSColor")){
                // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
                window_DBMSColor = dhxWins_DBMSColor.createWindow("window_DBMSColor",10,65,2024,860);
                window_DBMSColor.setText("<a style='font-size:14pt;font-weight:bold;color:red;'>DATABASE MS COLOR</a>");
                /*necessary to hide window instead of remove it*/
                window_DBMSColor.attachEvent("onClose", function(win){
                    if (win.getId() == "window_DBMSColor") 
                        win.hide();
                });

            }else{
                dhxWins_DBMSColor.window("window_DBMSColor").show(); 
            } 
        }

        //@4: view database trim/pvh: form ngang
        var dhxWins_DBTrim;
        var viewDBTrimGrid;
        function viewDBTrim()
        {
            if(!dhxWins_DBTrim){
                dhxWins_DBTrim= new dhtmlXWindows();// show window form to add length
            }   
            if (!dhxWins_DBTrim.isWindow("window_DBTrim")){
                // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
                window_DBTrim = dhxWins_DBTrim.createWindow("window_DBTrim", 10,65,2024,860);
                window_DBTrim.setText("<a style='font-size:14pt;font-weight:bold;color:red;'>DATABASE TRIM/PVH</a>");
                /*necessary to hide window instead of remove it*/
                window_DBTrim.attachEvent("onClose", function(win){
                    if (win.getId() == "window_DBTrim")
                        win.hide();
                });

            }else{
                dhxWins_DBTrim.window("window_DBTrim").show(); 
            } 
        }

        //@5: view database setting form
        var dhxWins_SettingForm;
        var viewSettingForm;
        function viewSettingForm()
        {
            if(!dhxWins_SettingForm){
                dhxWins_SettingForm= new dhtmlXWindows();// show window form to add length
            }   
            if (!dhxWins_SettingForm.isWindow("window_SettingForm")){
                // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
                window_SettingForm = dhxWins_SettingForm.createWindow("window_SettingForm", 1000,70,1024,860);
                window_SettingForm.setText("<a style='font-size:14pt;font-weight:bold;color:red;'>DATABASE SETTING FORM</a>");
                /*necessary to hide window instead of remove it*/
                window_SettingForm.attachEvent("onClose", function(win){
                    if (win.getId() == "window_SettingForm")
                        win.hide();
                });

            }else{
                dhxWins_SettingForm.window("window_SettingForm").show(); 
            } 
        }

        //@6: view scrap
        var dhxWins_Scrap;
        var viewScrapGrid;
        function viewScrap()
        {

            if(!dhxWins_Scrap){
                dhxWins_Scrap= new dhtmlXWindows();// show window form to add length
            }   
            if (!dhxWins_Scrap.isWindow("window_Scrap")){
                // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
                window_Scrap = dhxWins_Scrap.createWindow("window_Scrap", 1390,70,650,550);
                window_Scrap.setText("<a style='font-size:14pt;font-weight:bold;color:red;'>SCRAP VALUE</a>");
                /*necessary to hide window instead of remove it*/
                window_Scrap.attachEvent("onClose", function(win){
                    if (win.getId() == "window_Scrap")
                        win.hide();
                });

                viewScrapGrid = window_Scrap.attachGrid();
                viewScrapGrid.enableSmartRendering(true);
                viewScrapGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter");	
                //viewScrapGrid.setInitWidths("30,*,100");          //the widths of columns  
                //viewScrapGrid.setColAlign("center,center,center");       //the alignment of columns   
                //viewScrapGrid.setColTypes("ro,ed,ed");                //the types of columns  
                // // viewScrapGrid.setColSorting("int,str,str");       //the sorting types   
                // // viewScrapGrid.enableStableSorting(true);

                viewScrapGrid.enableMultiselect(true);				
                viewScrapGrid.init();
                viewScrapGrid.load('./models/viewScrap_conn.php');

            }else{
                dhxWins_Scrap.window("window_Scrap").show(); 
            } 
        }

        //@7
        var dhxWins_user;
        var viewUsersGrid;
        function viewUsers()
        {
            if(!dhxWins_user){
                dhxWins_user= new dhtmlXWindows();// show window form to add length
            }   
            if (!dhxWins_user.isWindow("window_item")){

                window_item = dhxWins_user.createWindow("window_item", 620,65,600,700);
                window_item.setText("Window View Users");
                /*necessary to hide window instead of remove it*/
                window_item.attachEvent("onClose", function(win){ if (win.getId() == "window_item") win.hide(); });
                viewUsersGrid = window_item.attachGrid();
                viewUsersGrid.enableSmartRendering(true);
                viewUsersGrid.attachHeader(",#text_filter,#text_filter,#text_filter");
                viewUsersGrid.enableMultiselect(true);				
                viewUsersGrid.init();
                
                viewUsersGrid.load('./models/viewUsers_conn.php');   

            }else{
                dhxWins_user.window("window_item").show(); 
            } 
        }

        function createUser()
        {
            location.href = "./views/user/update.php";
        }

        /*UPLOAD DATA  ALL*/

        //@01. Upload DB 1 line
        var dhxWins_DB1Line;
        var dhxWins_DB1Line;
        function uploadDB1Line() {
            if(!dhxWins_DB1Line){ dhxWins_DB1Line= new dhtmlXWindows(); }

            var id = "WindowsDetail";
            var w = 400;	var h = 100;	var x = Number(($(window).width()-400)/2);	var y = Number(($(window).height()-50)/2);
            var Popup = dhxWins_DB1Line.createWindow(id, x, y, w, h);
            dhxWins_DB1Line.window(id).setText("Import Database 1 Line");

            Popup.attachHTMLString('<div style="width:500%;margin:20px ">' +
            '<form action="./models/uploadDB1Line_conn.php" enctype="multipart/form-data" method="post">' +
                '<input id="file" name="file" type="file"/>' +
                '<input name="submit" type="submit" value="Upload" />' +
            '</form></div></div>'); 
        }

        //@02. Upload DB ms color
        var dhxWins_uploadDBMSColor;
        function uploadDBMSColor()
        {
            if(!dhxWins_uploadDBMSColor){
                dhxWins_uploadDBMSColor= new dhtmlXWindows();// show window form to add length
            }
            if (!dhxWins_uploadDBMSColor.isWindow("windowItem")){
                // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window
                windowItem = dhxWins_uploadDBMSColor.createWindow("windowItem", 890,130,395,288);
                windowItem.setText("Upload Database Setting Form");
                /*necessary to hide window instead of remove it*/
                windowItem.attachEvent("onClose", function(win){
                    if (win.getId() == "windowItem")
                        win.hide();
                });
                formData = [
                    {type: "fieldset", label: "Uploader", list:[
                        {type: "upload", name: "myFiles", autoStart: true, inputWidth: 330, url: "./models/uploadDBMSColor_conn.php"}
                    ]}
                ];
                ItemForm = windowItem.attachForm(formData, true);
                ItemForm.attachEvent("onFileAdd",function(realName){
                    // your code here
                    dhxWins_uploadDBMSColor.window("windowItem").progressOn();
                });
                ItemForm.attachEvent('onUploadFail', function(state,extra){
                    alert(extra.mess);
                    var myUploader = ItemForm.getUploader('myFiles');
                    myUploader.clear();
                    dhxWins_uploadDBMSColor.window("windowItem").progressOff();
                });
                ItemForm.attachEvent('onUploadFile', function(state,fileName,extra){
                    alert(extra.mess);
                    dhxWins_uploadDBMSColor.window("windowItem").progressOff();
                    location.reload();
                });

            }else{
                dhxWins_uploadDBMSColor.window("windowItem").show();
            }
        }

        //@03. Upload DB trim/pvh
        var dhxWins_uploadDBTrim;
        function uploadDBTrim()
        {
            if(!dhxWins_uploadDBTrim){
                dhxWins_uploadDBTrim= new dhtmlXWindows();// show window form to add length
            }
            if (!dhxWins_uploadDBTrim.isWindow("window_uploadDBTrim")){

                window_uploadDBTrim = dhxWins_uploadDBTrim.createWindow("window_uploadDBTrim", 140,130,395,288);
                window_uploadDBTrim.setText("Upload Database Trim/PVH");
                /*necessary to hide window instead of remove it*/
                window_uploadDBTrim.attachEvent("onClose", function(win){
                    if (win.getId() == "window_uploadDBTrim")
                        win.hide();
                });
                formData = [
                    {type: "fieldset", label: "Uploader", list:[
                        {type: "upload", name: "myFiles", autoStart: true, inputWidth: 330, url: "./models/uploadDBTrim_conn.php"}
                    ]}
                ];
                ItemForm = window_uploadDBTrim.attachForm(formData, true);
                ItemForm.attachEvent("onFileAdd",function(realName){
                    dhxWins_uploadDBTrim.window("window_uploadDBTrim").progressOn();
                });

                ItemForm.attachEvent('onUploadFail', function(state,filename,extra){
                    alert('OK (1)');
                    //alert(extra.mess);
                    var myUploader = ItemForm.getUploader('myFiles');
                    myUploader.clear();
                    dhxWins_uploadDBTrim.window("window_uploadDBTrim").progressOff();
                    location.reload();
                });

                ItemForm.attachEvent('onUploadFile', function(state,filename,extra){
                    //alert('OK (2)');
                    alert(extra.mess);
                    dhxWins_uploadDBTrim.window("window_uploadDBTrim").progressOff();
                    location.reload();
                });

            }else{
                dhxWins_uploadDBTrim.window("window_uploadDBTrim").show();
            }
        }


        // import
        function uploadDBTrim2() 
        {

            var dhxWins;
            if(!dhxWins){ dhxWins= new dhtmlXWindows(); }

            var id = "WindowsDetail";
            var w = 400;
            var h = 100;
            var x = Number(($(window).width()-400)/2);
            var y = Number(($(window).height()-50)/2);
            var Popup = dhxWins.createWindow(id, x, y, w, h);
            dhxWins.window(id).setText("Import Receiving Data");
            Popup.attachHTMLString(
                '<div style="width:500%;margin:20px">' +
                    '<form action="./models/uploadDBTrim_conn.php" enctype="multipart/form-data" method="post" accept-charset="utf-8">' +
                        '<input type="file" name="file" id="file" class="form-control filestyle" value="value" data-icon="false"  />' +
                        '<input type="submit" name="submit" value="Upload" id="importfile-id" class="btn btn-block btn-primary"  />' +
                    '</form>' +
                '</div>'
            );
        }

        //@04: Upload setting form
        var dhxWins_settingform;
        function uploadSettingForm()
        {
            if(!dhxWins_settingform){
                dhxWins_settingform= new dhtmlXWindows();// show window form to add length
            }
            if (!dhxWins_settingform.isWindow("windowItem")){
                // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window
                windowItem = dhxWins_settingform.createWindow("windowItem", 890,130,395,288);
                windowItem.setText("Upload Database Setting Form");
                /*necessary to hide window instead of remove it*/
                windowItem.attachEvent("onClose", function(win){
                    if (win.getId() == "windowItem")
                        win.hide();
                });
                formData = [
                    {type: "fieldset", label: "Uploader", list:[
                        {type: "upload", name: "myFiles", autoStart: true, inputWidth: 330, url: "./models/uploadSettingForm_conn.php"}
                    ]}
                ];
                ItemForm = windowItem.attachForm(formData, true);
                ItemForm.attachEvent("onFileAdd",function(realName){
                    // your code here
                    dhxWins_settingform.window("windowItem").progressOn();
                });
                ItemForm.attachEvent('onUploadFail', function(state,extra){
                    alert(extra.mess);
                    var myUploader = ItemForm.getUploader('myFiles');
                    myUploader.clear();
                    dhxWins_settingform.window("windowItem").progressOff();
                });
                ItemForm.attachEvent('onUploadFile', function(state,fileName,extra){
                    alert(extra.mess);
                    dhxWins_settingform.window("windowItem").progressOff();
                    location.reload();
                });

            }else{
                dhxWins_settingform.window("windowItem").show();
            }
        }

        /* ******** UA *********************/
        function updateUA()
        {
            UAGrid.attachEvent("onEnter", function(id,ind){
                // your code here
                var url_update = './models/updateUA_conn.php';
                var item = UAGrid.cells(id,1).getValue();
                item = item.trim();
                var size = UAGrid.cells(id,2).getValue();
                size = size.trim();
                var base_roll = UAGrid.cells(id,3).getValue();
                base_roll = base_roll.trim();
                var objUA = {
                    item:item,
                    size:size,
                    base_roll:base_roll,
                    idUA:id
                };			
                // var tesst = JSON.stringify(objUA);
                $.ajax({
                    url: url_update,
                    type: "POST",
                    data: {data: JSON.stringify(objUA)},
                    dataType: "json",
                    beforeSend: function(x) {
                        if (x && x.overrideMimeType) {
                        x.overrideMimeType("application/j-son;charset=UTF-8");
                        }
                    },
                    success: function(result) {
                        if(result.status){
                            alert('Update dữ liệu thành công!!!!');
                        }else{
                            alert(result.mess);
                        }
                    }
                });
            });
        }

        var dhxWins_uaFile;
        function uploadUAFile() 
        {
            if(!dhxWins_uaFile){
                dhxWins_uaFile= new dhtmlXWindows();// show window form to add length
            }
            if (!dhxWins_uaFile.isWindow("windowItem")){
                // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window
                windowItem = dhxWins_uaFile.createWindow("windowItem", 890,130,395,288);
                windowItem.setText("Upload Database Setting Form");
                /*necessary to hide window instead of remove it*/
                windowItem.attachEvent("onClose", function(win){
                    if (win.getId() == "windowItem")
                        win.hide();
                });
                formData = [
                    {type: "fieldset", label: "Uploader", list:[
                        {type: "upload", name: "myFiles", autoStart: true, inputWidth: 330, url: "./models/uploadUAFile_conn.php"}
                    ]}
                ];
                ItemForm = windowItem.attachForm(formData, true);
                ItemForm.attachEvent("onFileAdd",function(realName){
                    // your code here
                    dhxWins_uaFile.window("windowItem").progressOn();
                });
                ItemForm.attachEvent('onUploadFail', function(state,extra){
                    alert(extra.mess);
                    var myUploader = ItemForm.getUploader('myFiles');
                    myUploader.clear();
                    dhxWins_uaFile.window("windowItem").progressOff();
                });
                ItemForm.attachEvent('onUploadFile', function(state,fileName,extra){
                    alert(extra.mess);
                    dhxWins_uaFile.window("windowItem").progressOff();
                    location.reload();
                });

            }else{
                dhxWins_uaFile.window("windowItem").show();
            }
        }
    
        function viewUA()
        {		
            if(!dhxWinsUA){
                dhxWinsUA= new dhtmlXWindows();// show window form to add length
            }
            if (!dhxWinsUA.isWindow("windowViewUA")){
                // var win = myWins.createWindow(string id, int x, int y, int width, int height); // Creating New Window              
                windowViewUA = dhxWinsUA.createWindow("windowViewUA", 1013,65,505,720);
                dhxWinsUA.window("windowViewUA").progressOn();
                windowViewUA.setText("Window View UA");
                /*necessary to hide window instead of remove it*/
                windowViewUA.attachEvent("onClose", function(win){
                    if (win.getId() == "windowViewUA") 
                        win.hide();
                });
                UAGrid= windowViewUA.attachGrid();
                UAGrid.enableSmartRendering(true);
                UAGrid.attachHeader(",#text_filter,#text_filter,#text_filter,#text_filter");	
                UAGrid.setImagePath("./assets/dhtmlx/skins/skyblue/imgs/");	
                UAGrid.setHeader('<input type="button" id="DELETE" value="DELETE" onclick="deleteUA()">,ITEM,SIZE,BASE_ROLL,ID');
                UAGrid.setInitWidths("80,125,90,165,80");
                UAGrid.setColAlign("left,left,left,left,left");
                UAGrid.setColTypes("ch,ed,ed,ed,ed");
                UAGrid.setColSorting("na,str,str,str,str");
                UAGrid.init();  
                UAGrid.load('./models/viewUA_conn.php',function(){				
                    updateUA();
                    init_dhxUAToolbar();
                    //UAGrid.setHeader("<div style='width:100%; text-align:left;'>A</div>,B,C");
                }); 
            }else{
                dhxWinsUA.window("windowViewUA").show(); 
            } 
            dhxWinsUA.window("windowViewUA").progressOff();
        }

        function getCookie(cname) 
        {
            
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }

            return false;

        }

        function countNOView() {

            var url = './models/countNOView_conn.php';
            $.ajax({
                url: url,
                type: "POST",
                data: {data: ''},
                dataType: "json",
                beforeSend: function(x) {
                    if (x && x.overrideMimeType) {
                    x.overrideMimeType("application/j-son;charset=UTF-8");
                    }
                },
                success: function(result) {
                    if(result.status){
                        console.log(JSON.stringify(result));
                        countAll = result.countAll;
                        countCurrent = result.countCurrent;

                        LayoutMainHome.cells("a").setText("<span style='color:red;'>TỔNG ĐƠN HÀNG:</span> <span style='color:red;font-size:14px;'>" +countAll + "</span> && <span style='color:red;'>ĐƠN HÀNG TRONG NGÀY:</span><span style='color:red;font-size:14px;'> " + countCurrent + "</span>");

                    }
                }
            });
        }

        // 20220214: Trường hợp PD trống ==> tạo PD giả để remark đến lệnh sx dựa vào các điều kiện của Planning
        
        function loadRBOLT()
        {

            // close if exist
                if(dhxWins2){ dhxWins2.window("Windows").close(); }

            // create
                dhxWins2= new dhtmlXWindows(); 

            if (!dhxWins2.isWindow("Windows")){

                // init win
                    var id = "Windows";
                    var w = 960;
                    var h = 600;
                    var x = Number(($(window).width()-w)/2);
                    var y = Number(($(window).height()-h)/2);
                    var Popup = dhxWins2.createWindow(id, x, y, w, h);

                // init grid
                    grid = dhxWins2.window(id).attachGrid();

                // close
                    Popup.attachEvent("onClose", function(win){ if (win.getId() == "Windows") win.hide(); });

                // title
                    var currentTime = new Date();
                    var dd = String(currentTime.getDate()).padStart(2, '0');
                    var mm = String(currentTime.getMonth() + 1).padStart(2, '0'); //January is 0!
                    var yyyy = currentTime.getFullYear();
                    today = yyyy + '-' + mm + '-' + dd;
                    dhxWins2.window(id).setText("RBO LT (PD Planning) - "+today);

                // init grid
                    grid.attachHeader(",#text_filter,#text_filter,#text_filter");
                    grid.setRowTextStyle("1", "background-color: red; font-family: arial;");
                    grid.init();
                    grid.enableSmartRendering(true); // false to disable

                // load data
                    grid.clearAll();    
                    grid.loadXML("./models/handleTools_conn.php?event=loadRBOLT",function(){
                        // load last row
                        var state=grid.getStateOfView();
                        if(state[2]>0) grid.showRow(grid.getRowId(state[2]-1));

                        // // toolbar
                        // var RBOLTToolbar = dhxWins2.window("Windows").attachToolbar();
                        // RBOLTToolbar.setIconset("awesome");
                        // RBOLTToolbar.addText("RBO_LT_Toolbar",0,"<a style='font-size:12pt;font-weight:bold;color:blue;'>Thực hiện:</a>");
                        // RBOLTToolbar.addButton("imports_rbo_lt",4, "Imports", "fa fa-upload", null);

                        // RBOLTToolbar.attachEvent("onClick", function(id) {
                        //     //1. upload
                        //     if (id == "imports_rbo_lt") {
                        //         importsRBOLT();
                        //     } 
                        // });



                    });

                // check and save auto
                    grid.attachEvent("onCheckbox", function(rId,cInd,state){

                        var rbo = grid.cells(rId,1).getValue();
                        var lt = grid.cells(rId,2).getValue();

                        //json data encode
                        var jsonObjects = { 
                            "rbo": rbo, 
                            "lt": lt
                        };

                        if (cInd == 5 ) { // save
                            var url = "./models/handleTools_conn.php?event=saveRBOLT";
                            handleResults(jsonObjects, url);    
                        } else if (cInd == 6 ) { // del
                            var conf = confirm("Bạn chắc chắn muốn XÓA RBO: " + rbo + "?" );
                            if (conf ) {
                                var url = "./models/handleTools_conn.php?event=delRBOLT";
                                handleResults(jsonObjects, url );
                            }
                        }




                    });

            } else {
                dhxWins2.window("Windows").show(); 
            }

        }

        function handleResults(jsonObjects, url )
        {
            // check 
            if (jsonObjects && url ) {

                //excute with ajax
                    $.ajax({
                        type: "POST",
                        data: { data: JSON.stringify(jsonObjects) },
                        url: url,
                        dataType: 'json',
                        beforeSend: function(x) { if (x && x.overrideMimeType) { x.overrideMimeType("application/j-son;charset=UTF-8");} },
                        success: function(data) {

                            alert(data.message );
                            location.reload();
                                
                        },
                        error: function(xhr, status, error) {
                            alert('Error. Vui lòng liên hệ quản trị hệ thống!');
                            location.reload();
                            return false;
                        }
                    });

            }

        }

        
        


    }//end DocumentStart()
    ////////////////////////////////////////////////////////////////////////////////////////////////





</script>
<!-- <script src="./assets/js/handleSOLINE.js"></script> -->

<body>
</body>
</html>
