<?php
use Web\Url;
use Web\Assets;
use Web\Request;
use Stan\Stan;
use Session\Session;
use Core\View;

$stan = Stan::getInstance();
?>
</div>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js" type="text/javascript"></script>
		<script src="/assets/teacher/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="/assets/teacher/js/raphael.min.js" type="text/javascript"></script>
		<script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js" type="text/javascript"></script>
		<script src="/assets/teacher/js/modernizr.custom.js" type="text/javascript"></script>
		<script src="/assets/teacher/js/moment.js" type="text/javascript"></script>
	
		<?php if(Url::detectUri() == "/") : ?>
			<script src="assets/teacher/js/jquery.easy-pie-chart.js" type="text/javascript"></script>
			<script src="assets/teacher/js/jquery.sparkline.min.js" type="text/javascript"></script>
			<script src="assets/teacher/js/morris.min.js" type="text/javascript"></script>
			<script src="assets/teacher/js/raphael.min.js" type="text/javascript"></script>
		<?php endif; ?>

		<script src="/assets/teacher/js/main.js" type="text/javascript"></script>
		<script src="/assets/teacher/js/respond.js" type="text/javascript"></script>
		<script src="/assets/teacher/js/bootbox.min.js" type="text/javascript"></script>
		<script src="/assets/js/ajax.builder.js" type="text/javascript"></script>
		
		<?php if(@!is_null($js)): ?>
			<?= Assets::script($js); ?>
		<?php endif; ?>
	</body>
</html>
