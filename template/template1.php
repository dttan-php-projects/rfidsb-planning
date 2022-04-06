<?php 
    require_once (PATH_MODEL . "/__connection.php");
    require_once (PATH_MODEL . "/User_rfid_conn.php");
    require_once (PATH_MODEL . "/automail_conn.php");
    require_once (PATH_MODEL . "/checkSOExist_conn.php");

	function InitPage($CodePage, $Title)
	{
        //if($CheckUser) CheckRole($CodePage); dhx_web skyblue
        checkCookie();

        $HTMLStringTitle = " <!DOCTYPE html>
                            <html>
                            <head>
                                <title>$Title</title>
                                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\"/>
                                <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
                                <script src=\"./assets/dhtmlx/codebase/dhtmlx.js\" type=\"text/javascript\"></script> 
                                <link rel=\"stylesheet\" type=\"text/css\" href=\"./assets/dhtmlx/skins/skyblue/dhtmlx.css\">   

                                <link rel=\"stylesheet\" type=\"text/css\" href=\"./assets/font-awesome/css/font-awesome.min.css\">   

                                <script src=\"./assets/js/jquery-1.10.1.min.js\"></script> 
                                <script src=\"./assets/js/cookie.js\"></script> 
                                <!-- <script src=\"./assets/js/loader_rfidsb_layout.js\"></script>
                                 <script src=\"./assets/js/loader_rfidsb_toolbar.js\"></script> -->
                                
                                <link rel=\"icon\" href=\"./assets/images/Logo.ico\" type=\"image/x-icon\">
                            </head>
                            <style>
                                html, body {
                                    width: 100%;
                                    height: 100%;
                                    padding: 0;
                                    margin: 0;
                                    font-family: \"Source Sans Pro\",\"Helvetica Neue\",Helvetica;
                                    background-repeat: no-repeat;
                                    background-size: 100%;
                                }
                            
                            </style>";
                            
        $HTMLStringScript = '
            <script>
                '.GetHeaderTitle($CodePage).'
                var MainMenu;
                var ToolbarMain;
                var TitleHeader = "' . $Title . '";
                $(document).ready(function(){
                    $("body").html("<div style=\"height: 30px;background:#205670;font-weight:bold\"><div id=\"menuObj\"></div></div><div style=\"position:absolute;width:100%;top:35;background:white\"><div id=\"ToolbarBottom\"></div></div>" + $("body").html());

                    MainMenu = new dhtmlXMenuObject({
                            parent: "menuObj",
                            icons_path: "./assets/dhtmlx/common/imgs_Menu/",
                            json: "./assets/json/Menu.json",
                            top_text: HeaderTile
                    });

                    ToolbarMain = new dhtmlXToolbarObject({
                        parent: "ToolbarBottom",
                        // icons_path: "./assets/dhtmlx/common/imgs/",
                        align: "left",
                        icons_size: 18,
                        iconset: "awesome"
                    });

                    ToolbarMain.addText("Title", null, "<a style=\'font-size:20pt;font-weight:bold\'>'. $Title .'</a>");
                    ToolbarMain.addSeparator("Space", null);
                    ToolbarMain.addSpacer("Title");
                    
                    DocumentStart();
                });
                

                String.prototype.replaceAll = function(search, replacement) {
                    if(this.indexOf(search) !== -1)
                    {
                        var target = this;
                        return target.replace(new RegExp(search, \'g\'), replacement);
                    } else return this;
                    
                };


                Date.prototype.addDate = function(n){
                    this.setDate(this.getDate() + n);
                    return this;
                };

                function getUrl(sParam) {
                    var sPageURL = window.location.search.substring(1),
                        sURLVariables = sPageURL.split(\'&\'),
                        sParameterName,
                        i;
            
                    for (i = 0; i < sURLVariables.length; i++) {
                        sParameterName = sURLVariables[i].split(\'=\');
            
                        if (sParameterName[0] === sParam) {
                            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                        }
                    }
                };

                function AjaxAsync(urlsend,dtsend,typeSend = "GET", datatype = "html")
                {
                    var it_works;

                    $.ajax({
                        url: urlsend,
                        type: typeSend.toUpperCase(),
                        dataType: datatype.toUpperCase(),
                        cache: false,
                        data: dtsend,
                        success: function(string){	
                            it_works = string;
                        },
                        error: function (){
                            it_works = \'ERROR\';
                        },
                        async: false
                    });
                    return it_works;
                }

                Date.prototype.yyyymmdd = function() {
                    var mm = this.getMonth() + 1; // getMonth() is zero-based
                    var dd = this.getDate();
                    var hh = this.getHours();
                    var MM = this.getMinutes();
        
                    return [this.getFullYear(),(mm>9 ? \'\' : \'0\') + mm,(dd>9 ? \'\' : \'0\') + dd
                            ].join(\'-\') + " " + [(hh>9 ? \'\' : \'0\') + hh,(MM>9 ? \'\' : \'0\') + MM].join(\':\');
                };
            </script>';
            echo $HTMLStringTitle . $HTMLStringScript;
    }

    function checkCookie(){
        
		if(!isset($_COOKIE["VNRISIntranet"])) {
			header('Location: ./views/user/login.php');
        } 
        else {
				header('Content-type: text/html; charset=utf-8');
		}
	}
    
    function getAutomailUpdated()
    {
        $result = 'loading...';
        $data = toQueryAll(getConnection("au_avery"), "SELECT `STATUS`, `CREATEDDATE` FROM autoload_log ORDER BY ID DESC;" );
        if (!empty($data[0]) ) {
            $data = $data[0];
            $status = $data['STATUS'];
            $created_date = $data['CREATEDDATE'];

            if ($status == 'OK' ) {
                $result = $created_date;
            } else {

                $dataOK = toQueryAll(getConnection("au_avery"), "SELECT `STATUS`, `CREATEDDATE` FROM autoload_log WHERE `STATUS`='OK' ORDER BY ID DESC;" );
                $created_date_OK = '';
                if (!empty($dataOK) ) {
                    $dataOK = $dataOK[0];
                    $created_date_OK = $dataOK['CREATEDDATE'];
                }

                // 01: Không save được
				if ($status == 'ERR_01' ) {
					$result = "$created_date_OK. (ERR 01 (UPDATE) lúc $created_date)";
				} else if ($status == 'ERR_02' ) { // có rỗng dữ liệu PACKING,...
					$result = "$created_date_OK. (ERR 02 (EMPTY DATA) lúc $created_date)";
				} else if ($status == 'ERR_03' ) { // File không đọc được
					$result = "$created_date_OK. (ERR 03 (File Lỗi) lúc $created_date)";
				} 
            }
            
        }

        return $result;
    }

    function getToday(){
        $date = getdate();
        $day = $date['mday'];
        $month = $date['month'];
        $year = $date['year'];
        
        $hour = $date['hours'];
        $minute = $date['minutes'];
        $second = $date['seconds'];

        $today = $day."-".$month.",".$year." ".$hour.":".$minute;
        return $today;
    }
    
    function GetHeaderTitle($urlRedirect = '')
    {
        $automail_updated = getAutomailUpdated();
        if (empty($automail_updated) ) {
            $automail_updated = 'loading...';
        } 

        $automail_updated = 'Automail updated: '.$automail_updated;

        if (strpos($automail_updated, 'ERR') !== false ) {
            $automail_updated = '<span style=\"color:red;\">'.$automail_updated.'</span>';
        }
        

        if(!isset($_COOKIE["VNRISIntranet"])){
            if(!empty($urlRedirect)){
                return 'var HeaderTile = "'.$automail_updated.'<a style=\"color:blue;font-style:italic;padding-left:10px\">Hi Guest | <a href=\"../views/user/login.php?URL='.$urlRedirect.'\">Login</a></a>";var UserVNRIS = "";';
            }else{
                return 'var HeaderTile = "'.$automail_updated.'<a style=\"color:blue;font-style:italic;padding-left:10px\">Hi Guest | <a href=\"../views/user/login.php\">Login</a></a>";var UserVNRIS = "";';
            }			
        } 
        else {
            return 'var HeaderTile = "'.$automail_updated.'<a style=\"color:blue;font-style:italic;padding-left:10px\">Hi '.$_COOKIE["VNRISIntranet"].' | <a href=\"./views/user/logout.php\">Logout</a></a>";var UserVNRIS = "'.$_COOKIE["VNRISIntranet"].'";';
        }


    }
?>