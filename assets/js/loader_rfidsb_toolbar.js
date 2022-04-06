
        function initToolbar() 
        {
            if (print_type == '') {
                
            }
            ToolbarMain.addInput("SO",3,""); 

            ToolbarMain.addText("", 4, "GPM");
            ToolbarMain.addInput("GPM",5, ""); 

            ToolbarMain.addText("", 6, "FROM DATE");
            ToolbarMain.addInput("FROM_DATE",7,""); 
            ToolbarMain.addText("", 8, "TO DATE");
            ToolbarMain.addInput("TO_DATE",9,""); 

            var from_date_input = ToolbarMain.getInput("FROM_DATE");
            var to_date_input = ToolbarMain.getInput("TO_DATE");
            var so_search_input = ToolbarMain.getInput("SO");
            myCalendar = new dhtmlXCalendarObject([from_date_input,to_date_input]);
            myCalendar.setDateFormat("%d-%M-%y");
            ToolbarMain.addSpacer("TO_DATE");
                
            ToolbarMain.addButton("SAVE_NO",21, "Save No", "save.gif", null);

            ToolbarMain.addButton("PRINT_NO",13, "Print No", "print.gif");

            ToolbarMain.addButton("VIEW_NO",23, "View No", "open.gif");

            //Report and Export...
            ToolbarMain.addButton("REPORT_NO",23, "Report", "xlsx.gif");
            ToolbarMain.addButton("EXPORT_NO",24, "Export", "xlsx.gif");


            var from_date_value = ToolbarMain.getValue("FROM_DATE");
            var to_date_value = ToolbarMain.getValue("TO_DATE");

            ToolbarMain.attachEvent("onClick", function(name) {
                //1. Save No
                if (name == "SAVE_NO") {
                    alert("You choose save No");
                    //viewOracle();
                }
                //2. Print No
                else if (name == "PRINT_NO") {
                    alert("You choose print No");
                    //window.open('upload_oracle_download.php', '_blank');
                }
                //3. View No
                else if (name == "VIEW_NO") {
                    alert("You choose View No");
                    window.open('index.php?ctrl=site&action=OK', '_blank');
                }
                //4. Report
                else if (name == "REPORT_NO") {
                    alert("You choose Report No");
                }
                //5. Export All No
                else if(name == "EXPORT_NO") {
                    if(!from_date_value||!to_date_value){
                        alert('VUI LÒNG CHỌN KHOẢNG NGÀY ĐỂ EXPORT DỮ LIỆU');
                        return false;
                    }
                    var url_export = RootDataPath+'report_no_all_new.php?from_date_value='+from_date_value+'&to_date_value='+to_date_value;
                    document.location.href = url_export;
                }    
            });
        }//end initToolbar

