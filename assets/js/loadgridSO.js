//@TanDoan03: input SO (scan barcode input, input keyboard)
function loadGridSO(SO_LINE){
    var dataSo={rows:[]};		
    var url_load_grid = RootDataPath+'grid_so_new.php'; 
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
                    itemMain = SoGrid.cells(checked_SOLINE[0]['grid_id'],5).getValue();
                    poMain = SoGrid.cells(checked_SOLINE[0]['grid_id'],4).getValue();
                    rboMain = SoGrid.cells(checked_SOLINE[0]['grid_id'],10).getValue();	
                    heightMain = SoGrid.cells(checked_SOLINE[0]['grid_id'],14).getValue();	
                    // get scrap from RBO
                    if(rboMain.toUpperCase().indexOf('NIKE')!==-1){
                        scap_percent = 1.2;
                    }else if(rboMain.toUpperCase().indexOf('UNIQLO')!==-1||rboMain.toUpperCase().indexOf('FAST RETAILING')!==-1){
                        scap_percent = 1.034;
                    }else if((rboMain.toUpperCase().indexOf('MUJI')!==-1||rboMain.toUpperCase().indexOf('RYOHIN KEIKAKU')!==-1)&&(Number(heightMain)>100)){
                        scap_percent = 1.034;
                    }else{
                        scap_percent = 1.014;
                    }
                    var so = SoGrid.cells(checked_SOLINE[0]['grid_id'],1).getValue();
                    var line = SoGrid.cells(checked_SOLINE[0]['grid_id'],2).getValue();
                    checkSo = so+"-"+line;
                    checkID = checked_SOLINE[0]['grid_id'];
                    if(print_type=='ua_cbs'||print_type=='cbs'){
                        initsizeGrid();
                        initNO();	
                        // update des
                        if(print_type=='ua_cbs'){
                            getListDes();
                        }							
                    }else if(print_type=='ua_no_cbs'||print_type=='rfid'){
                        initMaterialGrid();
                        loadMaterialNoCBS();					                
                        initNO();
                    }else if(print_type=='pvh_rfid'){
                        initMaterialGrid();
                        loadMaterialPVH();
                        initNO();
                    }else if(print_type=='trim'||print_type=='trim_macy'){
                        initMaterialGrid();
                        loadMaterialPVH();
                        initNO();
                    }
                }							
            }else{
                alert(result.mess);
                location.reload();
            }				
        }
    });
}	