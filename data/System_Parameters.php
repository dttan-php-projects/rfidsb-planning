
<!-- set var for script all -->
<script>
var widthScreen = screen.width;
var widthSo = 690;
var widthForm = 1400;
var widthMaterial = 390;
var widthInk = 300;
var width_list = 500;
var width_Order = 690;
var heightOrder = 720; //720 ok
var height_listSO = 300;
var widthSoSelect = 500;
if(widthScreen<=1600){
        widthSo = 388;
        widthSoSelect = 260;
        widthForm = 1224;
        widthMaterial = 310;
        widthInk = 280;
        heightOrder = 620;
}

var PATH_ROOT = '<?php echo $_SERVER['SERVER_NAME']."/longhau/planning/rfidsb/"; ?>';
var PATH_DATA = PATH_ROOT+'<?php echo "data/"; ?>';
var PATH_VIEW = PATH_ROOT+'<?php echo "views/"; ?>';
var PATH_MODEL = PATH_ROOT+'<?php echo "models/"; ?>';

var LayoutMain;
var LayoutB;
var SoForm;
var SoGrid;

var SizeInkGrid;
var MaterialGrid;

var checked_SOLINE = [];
var noGrid;
var print_type ='';
var po_no_new;

    <?php  
        $date = getdate();
        $day = $date['mday'];
        $month = $date['month'];
        $year = $date['year'];
        
        $hour = $date['hours'];
        $minute = $date['minutes'];
        $second = $date['seconds'];

        $today = $day."-".$month.",".$year." ".$hour.":".$minute;

    ?>

</script>


<!-- // ********************* @MinhVo -->