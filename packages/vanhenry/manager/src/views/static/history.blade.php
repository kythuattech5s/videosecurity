<div class="history bgfff" style="overflow: hidden;">
            <h3>Nhật ký hoạt động</h3>
             <div id="no-more-tables">
              <table class="col-md-12 table-bordered table-striped table-condensed cf p0">
              <thead class="cf">
                <tr>
                  <th>STT</th>
                  <th>Người dùng</th>
                  <th>Ghi chú</th>
                  <th>Thời gian</th>
                </tr>
              </thead>
              <tbody>
              <?php $his =  \vanhenry\manager\model\HHistory::orderBy('id','desc')->paginate(10); $i=1;?>
              @foreach($his as $h)
                <tr>
                  <td data-title="STT">{{$i}}</td>
                  <td data-title="Người dùng">{{$h->username}}</td>
                  <td data-title="Ghi chú">{{$h->content}}</td>
                  <td data-title="Thời gian">{{$h->created_at}}</td>
                </tr>
                <?php $i++; ?>
              @endforeach
              </tbody>
            </table>
          </div>
          <div>
            <div class="tac">
              {{-- <button class="more"><i class="fa fa-chevron-circle-down" aria-hidden="true"></i>Xem thêm</button> --}}
              {{$his->appends(request()->input())->links()}}
            </div>
          </div>
          </div>