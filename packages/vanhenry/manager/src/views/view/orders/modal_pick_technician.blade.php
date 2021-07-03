<p class="title-modal">Chọn Kỹ thuật viên</p>
<button class="closeModal"><i class="fa fa-times" aria-hidden="true"></i></button>
<div class="choose-agency-technician">
     <form action="/esystem/confirmTechnician" method="POST" id="confirmTechnician">
          @csrf
          <div class="row">
               <div class="col-lg-12">
                    <div class="child">
                         <p class="title">Kỹ thuật viên</p>
                         <input type="text" id="searchTechnician" placeholder="Nhập tên kỹ thuật viên muốn chuyển đơn...">
                         <div>
                              <ul class="technician">
                                   @foreach($technicians as $key => $technician)
                                   <li>
                                        <label for="technician-{{$key}}">
                                             <input type="checkbox" 
                                             @if($technician->OrderUserTechnician->first() !== null)
                                                  checked
                                             @endif 
                                             name="technician[]" id="technician-{{$key}}" value="{{$technician->id}}">
                                             {{$technician->name}}
                                        </label>
                                        <select name="technician_service_id[]" class="select2" style="width:100%">
                                                  <option value="">Loại dịch vụ</option>
                                             @foreach($services as $service)
                                                  <option value="{{Support::show($service,'id')}}"
                                                  @if($technician->OrderUserTechnician->first() !== null)
                                                       @if($technician->OrderUserTechnician->first()->service->id == $service->id)
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