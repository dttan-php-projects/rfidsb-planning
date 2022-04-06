<?php
    $no = $_GET['PO_NO'];
?>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    var no = <?php echo "'".$no."'" ?>

    function delete_no(no){
		confirm_delete = confirm("Bạn có muốn XÓA "+no);
		if(confirm_delete){
			var url_delete = './models/deleteNO_conn.php';
			$.ajax({
			url: url_delete,
				type: "POST",
				data: {data: no},
				dataType: "json",
				beforeSend: function(x) {
					if (x && x.overrideMimeType) {
					x.overrideMimeType("application/j-son;charset=UTF-8");
					}
				},
				success: function(result) {
					if(result.status){
						// reload
						viewNOGrid.forEachRow(function(id){
							if(viewNOGrid.cells(id,2).getValue()===no){
								viewNOGrid.deleteRow(id);
							}
						});
					}else{
						alert('Có Lỗi trong quá trình XÓA '+no);
					}
				}
			});
		}else{
			
		}
	}

    delete_no(no);
</script>