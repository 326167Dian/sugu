<!-- Datepicker -->

<link rel="stylesheet" href="datepicker/themes/base/jquery.ui.all.css">
	<script src="datepicker/js/jquery-1.7.2.js"></script>
	<script src="datepicker/ui/jquery.ui.core.js"></script>
	<script src="datepicker/ui/jquery.ui.widget.js"></script>
	<script src="datepicker/ui/jquery.ui.datepicker.js"></script>
	<script>
	$(function() {
		$( "#tanggal" ).datepicker({
			changeMonth: true,
			changeYear: true
		});
	});
</script>  

<input type='text' id='tanggal'>