<div id="folder-modal" class="modal fade" role="dialog">
  	<div class="modal-dialog">
    	<div class="modal-content">
    	<form onsubmit = "return MediaManager.submitNewFolder(this);" action="{{$admincp}}/media/createDir" method="post">
    		{{csrf_field()}}
	      	<div class="modal-header">
	       		<button type="button" class="close" data-dismiss="modal">&times;</button>
	        	<h4 class="modal-title">Nhập tên folder mới</h4>
	      	</div>
	      	<div class="modal-body">
	        	<input type="text" name="folder_name" class="form-control">
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" data-dismiss="modal">Đóng</button>
	        	<button type="submit">Lưu</button>
	      	</div>
	      	</form>
    	</div>
  	</div>
</div>