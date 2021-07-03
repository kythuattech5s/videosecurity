@foreach($attributes as $attribute)
     <p class="attribute-name">{{$attribute->name}}</p>
     @foreach($attribute->values as $value)
     <li class="clazzli clazzli-{{$value->id}}">
          <label>
               <input type="checkbox" value="{{$value->id}}" @if(in_array($value->id,$valueInProduct->toArray())) checked @endif/>
               {{$value->name}}
          </label>
     </li>
     @endforeach
@endforeach