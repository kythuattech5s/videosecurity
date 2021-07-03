<td data-title="{{$show->note}}" style="text-align: left">



	<?php $user = \App\User::find(FCHelper::ep($dataItem,$show->name)); ?>

	<p ><b>{{$user!=null?$user->name:''}}</b></p>

	<p ><a style="color:#00923f" href="tel:">{{$user!=null?$user->phone:''}}</a></p>

	<p ><a style="color:#00923f" href="mailto:{{$user!=null?$user->email:''}}"> {{$user!=null?$user->email:''}}</a></p>

	<p >{{$user!=null?$user->address:''}}</p>

	







</td>