<div class="gallery-acceptance">
    <p class="title-info fs-24 fw-b mb-15">Hình ảnh nghiệm thu</p>
    <form action="/esystem/findImgAcceptance" class="filter-acceptance ajaxform" style="padding:10px" method="POST" accept-charset="UTF-8" data-success="CALLBACK_AJAX.callBackFilterImgs">
        @csrf
        <input type="hidden" value="{{Support::show($order,'id')}}" name="order_id">
        <label for="" style="display:flex;align-items:center">
            <p>Chọn ngày</p>
            <input type="text" class="single-date" name="date" value="{{date('d/m/Y',strtotime($imgAcceptance->date))}}" style="width: 100%; border: 1px solid #e0e0e0; padding: 5px; border-radius: 5px;">
        </label>
    </form>
    <?php $imgs = json_decode($imgAcceptance->imgs); ?>
    <div class="gallery-img">
        @foreach($imgs as $img)
        <a href="{{$img->path.$img->file_name}}" class="fancybox" rel="group">
            <img src="{{$img->path.$img->file_name}}">
        </a>
        @endforeach
    </div>
</div>