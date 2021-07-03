<p class="title-modal">Chọn Đại lý</p>
<button class="closeModal"><i class="fa fa-times" aria-hidden="true"></i></button>
<div class="choose-agency-technician">
     <form action="/esystem/confirmAgency" method="POST" id="confirmAgency">
          @csrf
          <div class="row">
               <div class="col-lg-12">
                    <div class="child">
                         <p class="title">Đại lý</p>
                         <input type="text" id="searchAgency" placeholder="Nhập tên đại lý muốn chuyển đơn...">
                         <div>
                              <ul class="agency">
                                   @foreach($agencies as $key => $agency)
                                   <li>
                                        <label for="agency-{{$key}}">
                                             <input type="checkbox" name="agency[]" 
                                             @if($agency->OrderUserAgency->first() !== null) 
                                                  checked
                                             @endif 
                                             id="agency-{{$key}}" name="agency[]" value="{{$agency->id}}">
                                             {{$agency->name}}
                                        </label>
                                        <select name="agency_service_id[]" class="select2" style="width:100%">
                                                  <option value="">Loại dịch vụ</option>
                                             @foreach($services as $service)
                                                  <option value="{{Support::show($service,'id')}}" 
                                                 @if($agency->OrderUserAgency->first() !== null)
                                                       @if($agency->OrderUserAgency->first()->service->id == $service->id)
                                                            selected
                                                       @endif
                                                  @endif
                                                  >{{Support::show($service,'name')}}</option>
                                             @endforeach
                                        </select>
                                   </li>
                                   @endforeach
                              </ul>
                         </div>
                    </div>
               </div>
          </div>
          <div class="submitForm">
               <button type="submit">Xác nhận</button>
          </div>
     </form>
</div>