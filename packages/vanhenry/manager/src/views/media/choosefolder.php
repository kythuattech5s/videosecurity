<div id="list-folder-modal" class="modal fade" role="dialog">
  	<div class="modal-dialog">
    	<div class="modal-content">
    	<form onsubmit = "$('#list-folder-modal').modal('hide');return MediaManager.<?php echo (!@$type || $type!="move")?"copyFile":"moveFile"; ?>($(this).find('a.active'));" action="#" method="post">
	      	<div class="modal-header">
	       		<button type="button" class="close" data-dismiss="modal">&times;</button>
	        	<h4 class="modal-title">Chọn Folder</h4>
	      	</div>
	      	<div class="modal-body">
	        	<?php echo $folders; ?>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" data-dismiss="modal">Đóng</button>
	        	<button type="submit">Lưu</button>
	      	</div>
	      	</form>
    	</div>
  	</div>
</div>