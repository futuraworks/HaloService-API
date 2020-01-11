<script type="text/javascript" src="plugin/jquery/js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="plugin/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("img").click(function(){
			var src = $(this).attr('src');
			var alt = $(this).attr('alt');
			$('#ModalImg').modal('show');
			$('#ModalImg #ModalTitle').text(alt);
			$('#ModalImg img').attr('src',src);
			$('#modal_img').show();
			$('#modal_vid').hide();
		});
		$(".vidPlayer").click(function(){
			var src = $(this).attr('data-url');
			var type = $(this).attr('data-type');
			var alt = $(this).attr('data-alt');
			$('#ModalImg').modal('show');
			$('#ModalImg #ModalTitle').text(alt);
			$('#modal_img').hide();
			$('#modal_vid').remove();
			$('.modal-body').append("<video id='modal_vid' controls width='100%' height='auto'><source src='"+src+"' type='"+type+"'></video>");
		});
		$('#ModalImg').on('hidden.bs.modal', function () {
			$('#modal_vid').remove();
		});
	});
</script>