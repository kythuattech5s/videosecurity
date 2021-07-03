<p style="display: inline-block;margin: 3px 0px 0px 5px;padding:2px;background: #f3f3f3">
(
<?php $countpcb =  \App\OrderPcb::where("status",1)->count(); ?>
<span style="color: red;font-weight: bold;">{{$countpcb}}</span> <a href="esystem/view/order_pcbs"> đơn PCB</a> - 
<?php $countpcb =  \App\OrderStencil::where("status",1)->count(); ?>
<span style="color: red;font-weight: bold;">{{$countpcb}}</span> <a href="esystem/view/order_stencils"> đơn Stencil</a>
)
</p>