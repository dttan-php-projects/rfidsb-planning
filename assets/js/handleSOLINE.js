//@1. get SOLINE INPUT (from user): OK
function getSOLINE() {
    //get Soline input
    input_so = ToolbarMain.getInput("SO");
    input_so.focus(); // set focus
    input_so.onkeypress = function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);

            if (keycode == '13') {

                var getSOLINE = $(this).val();
                getSOLINE = getSOLINE.trim();

                if (!getSOLINE.length > 0) {
                    alert("[ERROR 01.01]. BẠN CHƯA NHẬP SOLINE");
                    return false;
                } else {
                    checkSOLINE(getSOLINE); //ok
                }

            }

        } //input

} //END getSOLINE()

//@2: check SOLINE input: OK
function checkSOLINE(SO_LINE) {
    var url_check = './views/checkSOLINE.php?SO_LINE=' + SO_LINE;
    $.ajax({
        url: url_check,
        type: "POST",
        data: { data: [SO_LINE] },
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


                if (status == 1) { //true
                    ITEM_VNSO = result.result_vnso.ITEM;

                    //alert("item_vnso LA : "+item_vnso);

                    if (checkSOExist == 1) { //true
                        confirm_exist = confirm(result.message + " , BẠN CÓ MUỐN TIẾP TỤC TẠO LỆNH!!!");
                        if (!confirm_exist) {
                            location.reload();
                            return false;
                        }

                    }
                    alert("LOAD OK. DA CHON TIEP TUC");
                    getPrintType(SO_LINE, ITEM_VNSO);

                } else { //status == false
                    alert(error + " : " + message);
                    location.reload();
                    //return false;
                }

            } //end success



    });

} //END checkSOLINE()


//@3. get Print type: OK
function getPrintType(SO_LINE, ITEM_VNSO) {
    var url_get_print_type = './views/getPrintType.php?ITEM_VNSO=' + ITEM_VNSO;
    $.ajax({
        url: url_get_print_type,
        //async: false,
        type: "POST",
        data: { data: [ITEM_VNSO] },
        dataType: "json",
        beforeSend: function(x) {
            if (x && x.overrideMimeType) {
                x.overrideMimeType("application/j-son;charset=UTF-8");
            }
        },
        error: function() {
            alert("KHÔNG TÌM THẤY LOẠI FORM, VUI LÒNG LIÊN HỆ QUẢN TRỊ");
        },
        success: function(result) {
            status = result.status_item;
            message = result.message;
            print_type = result.result_item;
            //alert(status+message +'va '+ print_type);
            //alert(message);
            if (status == 1) {
                //print_type = result.result_item;
                //console.log(print_type);
                var print_type_text = '';
                if (print_type == 'ua_cbs') {
                    print_type_text = 'UNDER ARMOUR CBS';
                } else if (print_type == 'cbs') {
                    print_type_text = 'COLOR BY SIZE';
                } else if (print_type == 'rfid') {
                    print_type_text = 'RFID';
                } else if (print_type == 'pvh_rfid') {
                    print_type_text = 'PVH RFID';
                } else if (print_type == 'trim') {
                    print_type_text = 'TRIM';
                } else if (print_type == 'trim_macy') {
                    print_type_text = "TRIM MACY'S";
                }
                // set cookies
                setCookie('print_type_rfsb', print_type, 365); //@setcookies

                ToolbarMain.setItemText("Title", "<a style='font-size:20pt;font-weight:bold'>" + print_type_text + "</a>");

                initLayout();
                // init to load Grid 
                initSoGrid();

                // load grid
                loadGridSO(SO_LINE);
            } else {
                alert(message);
                //setCookie('print_type_rfsb',print_type,0);  //delete cookie
                location.reload();
                return false;
            }
        }
    });
} //END getPrintType()

//@4: get GPM
function getGPM(SO_LINE) {
    // call ajax
    var url_get_gpm = './models/getGPM_conn.php?SO_LINE=' + SO_LINE;
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
        success: function(result) {
            if (result.status) {
                input_gpm = ToolbarMain.getInput("GPM");
                input_gpm.value = result.data;
                input_gpm.focus(); // set focus
            } else {
                input_gpm = ToolbarMain.getInput("GPM");
                input_gpm.focus(); // set focus
            }
        }
    });
}

//@5: load grid LIST SO: OK
function initSoGrid() {

    if (print_type == 'rfid' || print_type == 'cbs' || print_type == 'ua_cbs' || print_type == 'ua_no_cbs') {
        SoGrid = LayoutMain.cells("d").attachGrid();
    } else {
        SoGrid = LayoutMain.cells("c").attachGrid();
    }

    SoGrid.setImagePath("./assets/dhtmlx/skins/skyblue/imgs/");
    SoGrid.setHeader(",SO,LINE,QTY,PO,ITEM,PD,REQ,ORDER,CS,RBO,RIBBON,INK,WIDTH,HEIGHT,GAP,FORM TYPE,SAMPLE,PACKING INSTR,BILL TO CUSTOMER,COUNT SO,SHIP TO,PAPER TYPE,QTY PAPER,INK TYPE,QTY INK,PAPER DES,INK DES,ORDER TYPE NAME"); //sets the headers of columns
    //SO 1,LINE 2,QTY 3,PO 4,ITEM 5,PD 6,REQ 7,ORDER 8,CS 9,RBO 10,RIBBON 11,INK 12,WIDTH 13,HEIGHT 14,GAP 15,FORM TYPE 16,SAMPLE 17,PACKING INSTR 18,BILL TO CUSTOMER 19,COUNT SO 20,SHIP TO 21,PAPER TYPE 22,QTY_PAPER 23,INK TYPE 24,QTY_INK 25,PAPER DES 26,INK DES 27,ORDER TYPE NAME 28 
    SoGrid.setColumnIds(",SO,LINE,QTY,PO,ITEM,PD,REQ,ORDER,CS,RBO,RIBBON,INK,WIDTH,HEIGHT,GAP,FORM TYPE,SAMPLE,PACKING INSTR,BILL TO CUSTOMER,COUNT SO,SHIP TO,PAPER TYPE,QTY_PAPER,INK TYPE,QTY_INK,PAPER DES,INK DES,ORDER TYPE NAME"); //sets the columns' ids
    SoGrid.setInitWidths("30,70,50,60,160,130,90,90,90,160,120,120,120,120,120,120,120,120,120,150,120,120,120,100,120,60,120,120,120"); //sets the initial widths of columns
    SoGrid.setColAlign("left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left,left"); //sets the alignment of columns
    SoGrid.setColTypes("ch,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed"); //sets the types of columns
    SoGrid.setColSorting("na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na,na"); //sets the sorting types of columns
    //SoGrid.enableSmartRendering(true);
    SoGrid.init();
}

//@5: Load Form
var formNO;
var myForm;

function initFromNO2() {
    formNO = [
        { type: "settings", position: "label-top" },
        {
            type: "fieldset",
            name: "calculator",
            label: "Calculator",
            list: [
                { type: "input", name: 'firstNum', label: 'First number:' },
                { type: "input", name: "secNum", label: "Second number:" },
                { type: "input", name: "resNum", label: "Result:" },
                { type: "newcolumn" },
                { type: "button", name: "plus", width: 20, offsetTop: 2, value: "+" },
                { type: "button", name: "minus", width: 20, offsetTop: 10, value: "-" },
                { type: "button", name: "multiply", width: 20, offsetTop: 10, value: "*" },
                { type: "button", name: "divide", width: 20, offsetTop: 10, value: "/" }
            ]
        }
    ];
    myForm = LayoutMain.cells("a").attachForm(formNO);
}

function initFromNO() {
    if (print_type == 'ua_cbs' || print_type == 'ua_no_cbs' || print_type == 'cbs' || print_type == 'rfid' || print_type == 'pvh_rfid' || print_type == 'trim' || print_type == 'trim_macy') {


        var frm_qty = SoGrid.cells(checkID, 3).getValue();
        var frm_po = SoGrid.cells(checkID, 4).getValue();
        var frm_item = SoGrid.cells(checkID, 5).getValue();
        var frm_pd = SoGrid.cells(checkID, 6).getValue();
        var frm_req = SoGrid.cells(checkID, 7).getValue();
        var frm_order = SoGrid.cells(checkID, 8).getValue();
        var frm_cs = SoGrid.cells(checkID, 9).getValue();
        var frm_rbo = SoGrid.cells(checkID, 10).getValue();
        var frm_ribbon = SoGrid.cells(checkID, 11).getValue();
        var frm_ink = SoGrid.cells(checkID, 12).getValue();
        var frm_width = SoGrid.cells(checkID, 13).getValue();
        if (!frm_width) {
            frm_width = 0;
        }
        var frm_gap = SoGrid.cells(checkID, 15).getValue();
        if (!frm_gap) {
            frm_gap = 0;
        }
        var form_type = print_type;
        var SAMPLE = SoGrid.cells(checkID, 17).getValue();
        var PACKING_INSTRUCTIONS = SoGrid.cells(checkID, 18).getValue();
        // get d126
        var frm_width = SoGrid.cells(checkID, 13).getValue();
        if (!frm_width) {
            frm_width = 0;
        }
        var frm_height = SoGrid.cells(checkID, 14).getValue();
        var frm_gap = SoGrid.cells(checkID, 15).getValue();
        if (!frm_gap) {
            frm_gap = 0;
        }
        frm_width = Number(frm_width);
        frm_gap = Number(frm_gap);
        var frm_d126 = 0;
        var frm_d126_round = 0;
        if (print_type == 'ua_cbs' || print_type == 'cbs') {
            frm_d126 = total_qty_material_round * (frm_width + frm_gap) / 1000;
            frm_d126_round = Math.round(frm_d126);
        }
        var bill_to_customer = SoGrid.cells(checkID, 19).getValue();
        var ink_type = SoGrid.cells(checkID, 24).getValue();
        var order_type_name = SoGrid.cells(checkID, 28).getValue();
        objForm = { qty: frm_qty, po: frm_po, item: frm_item, pd: frm_pd, req: frm_req, order: frm_order, cs: frm_cs, rbo: frm_rbo, ribbon: frm_ribbon, ink: frm_ink, width: frm_width, height: frm_height, gap: frm_gap, form_type: form_type, SAMPLE: SAMPLE, PACKING_INSTRUCTIONS: PACKING_INSTRUCTIONS, frm_d126: frm_d126_round, bill_to_customer: bill_to_customer, ink_type: ink_type, order_type_name: order_type_name };

        LayoutMain.cells("a").attachURL('./views/formNO.php', true, objForm);
        if (print_type == 'trim_macy') {
            objForm.paper_type = SoGrid.cells(checkID, 22).getValue();
        }

        LayoutMain.attachEvent("onContentLoaded", function(id) {
            $("#frm_sample").val(SAMPLE);
            if (SAMPLE == 0) { // don mau
                $("#frm_remark_4").val(PACKING_INSTRUCTIONS);
            } else if (SAMPLE == 1) {
                $("#frm_remark_4").val('CO MAU');
            } else if (SAMPLE == 2) {
                $("#frm_remark_4").val('');
            }
            $('#frm_sample').change(function() {
                var order_type = $(this).val();
                if (order_type == 0) { // don mau
                    $("#frm_remark_4").val(PACKING_INSTRUCTIONS);
                } else if (order_type == 1) {
                    $("#frm_remark_4").val('CO MAU');
                } else if (order_type == 2) {
                    $("#frm_remark_4").val('');
                }
            });
            if (rboMain.indexOf('NIKE') !== -1 && print_type == 'rfid') {
                // nhap GPM
                input_so_line = ToolbarMain.getInput("SO");
                getGPM(input_so_line.value);
            }
        });
        //}			
    }
}

//@6: 
function initsizeGrid() {
    if (checkID) {
        if (print_type == 'ua_cbs' || print_type == 'cbs') {
            SizeGrid = LayoutMain.cells("b").attachGrid();
            SizeGrid.setImagePath("/dhtmlx5F/skins/skyblue/imgs/");
            SizeGrid.setHeader("SIZE,LABEL,BASE ROLL,QTY"); //sets the headers of columns
            // SoGrid.attachHeader("#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,,#text_filter,#text_filter");	//the method takes the columns' filters as a parameter
            SizeGrid.setColumnIds("SIZE,LABEL,BASE ROLL,QTY"); //sets the columns' ids
            SizeGrid.setInitWidths("75,195,130,*"); //sets the initial widths of columns
            SizeGrid.setColAlign("left,left,left,left"); //sets the alignment of columns
            SizeGrid.setColTypes("ed,ed,ed,ed"); //sets the types of columns
            SizeGrid.setColSorting("na,na,na,na"); //sets the sorting types of columns
            //SizeGrid.enableSmartRendering(true);
            SizeGrid.init();
            // get SIZE
            getSizeFromSO();
        } else if (print_type == 'ua_no_cbs' || print_type == 'rfid') {
            LayoutMain.cells('b').setText("LIST INK");
            SizeGrid = LayoutMain.cells("b").attachGrid();
            SizeGrid.setImagePath("/dhtmlx5F/skins/skyblue/imgs/");
            SizeGrid.setHeader("INK CODE,INK DES,QTY"); //sets the headers of columns
            // SoGrid.attachHeader("#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,,#text_filter,#text_filter");	//the method takes the columns' filters as a parameter
            SizeGrid.setColumnIds("INK CODE,INK DES,QTY"); //sets the columns' ids
            SizeGrid.setInitWidths("130,*,80"); //sets the initial widths of columns
            SizeGrid.setColAlign("left,left,left"); //sets the alignment of columns
            SizeGrid.setColTypes("ed,ed,ed"); //sets the types of columns
            SizeGrid.setColSorting("na,na,na"); //sets the sorting types of columns
            //SizeGrid.enableSmartRendering(true);
            SizeGrid.init();
            // load to size , xem list size hien tai la list ink
            var internal_item = SoGrid.cells(checkID, 4).getValue();
            var gap = SoGrid.cells(checkID, 15).getValue();
            var material_qty = MaterialGrid.cellByIndex(0, 2).getValue();
            var load_size_grid = RootDataPath + 'grid_size_no_cbs.php?internal_item=' + internal_item + '&gap=' + gap + '&material_qty=' + material_qty;
            SizeGrid.load(load_size_grid, function() { //takes the path to your data feed 

            });
        }
    }
}

//@7: 
function initMaterialGrid() {
    if (print_type == 'ua_cbs' || print_type == 'ua_no_cbs' || print_type == 'cbs' || print_type == 'rfid') {
        MaterialGrid = LayoutMain.cells("c").attachGrid();
        MaterialGrid.setImagePath("/dhtmlx5F/skins/skyblue/imgs/");
        MaterialGrid.setHeader("MATERIAL,DESCRIPTION,QTY"); //sets the headers of columns
        // SoGrid.attachHeader("#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,,#text_filter,#text_filter");	//the method takes the columns' filters as a parameter
        MaterialGrid.setColumnIds("MATERIAL,DESCRIPTION,QTY"); //sets the columns' ids
        MaterialGrid.setInitWidths("130,*,80"); //sets the initial widths of columns
        MaterialGrid.setColAlign("left,left,left"); //sets the alignment of columns
        MaterialGrid.setColTypes("ed,ed,ed"); //sets the types of columns
        MaterialGrid.setColSorting("str,str,str"); //sets the sorting types of columns
        //MaterialGrid.enableSmartRendering(true);
        MaterialGrid.init();
    } else if (print_type == 'pvh_rfid' || print_type == 'trim' || print_type == 'trim_macy') {
        MaterialGrid = LayoutMain.cells("b").attachGrid();
        MaterialGrid.setImagePath("/dhtmlx5F/skins/skyblue/imgs/");
        MaterialGrid.setHeader("STT,PAPER TYPE,QTY,INK TYPE,QTY"); //sets the headers of columns
        // SoGrid.attachHeader("#text_filter,#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,,#text_filter,#text_filter");	//the method takes the columns' filters as a parameter
        MaterialGrid.setColumnIds("STT,PAPER TYPE,QTY,INK TYPE,QTY"); //sets the columns' ids
        MaterialGrid.setInitWidths("60,130,80,130,*"); //sets the initial widths of columns
        MaterialGrid.setColAlign("left,left,left,left,left"); //sets the alignment of columns
        MaterialGrid.setColTypes("ed,ed,ed,ed,ed"); //sets the types of columns
        MaterialGrid.setColSorting("str,str,str,str,str"); //sets the sorting types of columns
        //MaterialGrid.enableSmartRendering(true);
        MaterialGrid.init();
    }
}



//@: load Grid SO (GridSO, Form NO, sizeSO, Grid material)
var itemMain, poMain, rboMain;

function loadGridSO(SO_LINE) {
    var dataSo = { rows: [] };
    var url_load_grid = './models/loadGridSO_conn.php?SO_LINE=' + SO_LINE;
    $.ajax({
        url: url_load_grid,
        //async: false,
        type: "POST",
        data: { data: [SO_LINE] },
        dataType: "json",
        beforeSend: function(x) {
            if (x && x.overrideMimeType) {
                x.overrideMimeType("application/j-son;charset=UTF-8");
            }
        },
        success: function(result) {
            if (result.status) {
                var length = result.data.length;
                for (var i = 0; i < length; i++) {
                    dataSo.rows.push(result.data[i]);
                }
                SoGrid.parse(dataSo, "json");

                checked_SOLINE = []; // reset checked_SOLINE when filter
                for (var i = 0; i < SoGrid.getRowsNum(); i++) {
                    so_line = SoGrid.cellByIndex(i, 0).getValue().trim();
                    grid_id = SoGrid.getRowId(i);
                    var obj = { so_line: so_line, grid_id: grid_id };
                    checked_SOLINE.push(obj);
                }
                // load list item
                if (checked_SOLINE.length > 0) {
                    itemMain = SoGrid.cells(checked_SOLINE[0]['grid_id'], 5).getValue();
                    poMain = SoGrid.cells(checked_SOLINE[0]['grid_id'], 4).getValue();
                    rboMain = SoGrid.cells(checked_SOLINE[0]['grid_id'], 10).getValue();
                    heightMain = SoGrid.cells(checked_SOLINE[0]['grid_id'], 14).getValue();
                    // get scrap from RBO
                    if (rboMain.toUpperCase().indexOf('NIKE') !== -1) {
                        scap_percent = 1.2;
                    } else if (rboMain.toUpperCase().indexOf('UNIQLO') !== -1 || rboMain.toUpperCase().indexOf('FAST RETAILING') !== -1) {
                        scap_percent = 1.034;
                    } else if ((rboMain.toUpperCase().indexOf('MUJI') !== -1 || rboMain.toUpperCase().indexOf('RYOHIN KEIKAKU') !== -1) && (Number(heightMain) > 100)) {
                        scap_percent = 1.034;
                    } else {
                        scap_percent = 1.014;
                    }
                    var so = SoGrid.cells(checked_SOLINE[0]['grid_id'], 1).getValue();
                    var line = SoGrid.cells(checked_SOLINE[0]['grid_id'], 2).getValue();
                    checkSo = so + "-" + line;
                    checkID = checked_SOLINE[0]['grid_id'];
                    if (print_type == 'ua_cbs' || print_type == 'cbs') {
                        // initsizeGrid();
                        initFromNO2();
                        // update des
                        if (print_type == 'ua_cbs') {
                            // getListDes();
                        }
                    } else if (print_type == 'ua_no_cbs' || print_type == 'rfid') {
                        // initMaterialGrid();
                        // loadMaterialNoCBS();					                
                        // initNO();
                        initFromNO2();
                    } else if (print_type == 'pvh_rfid') {
                        // initMaterialGrid();
                        // loadMaterialPVH();
                        // initNO();
                        initFromNO2();
                    } else if (print_type == 'trim' || print_type == 'trim_macy') {
                        // initMaterialGrid();
                        // loadMaterialPVH();
                        // initNO();
                        initFromNO2();
                    }
                }
            } else {
                alert(result.mess);
                location.reload();
            }
        }
    });
} //end @: load Grid SO