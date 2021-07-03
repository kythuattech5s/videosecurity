<div class="bgloading">
	<div class="load-bar">
		<div class="bar"></div>
		<div class="bar"></div>
		<div class="bar"></div>
	</div>
</div>
<script type="text/javascript">
	$(document).ajaxStart(function() {
		$('.bgloading').fadeIn(300);
	});
	$(document).ajaxComplete(function(event, xhr, settings) {
		$('.bgloading').delay(300).fadeOut(400);
	});
	
</script>
<style type="text/css">
.bgloading{
	display:none;
}
.load-bar {
    position: relative;
    width: 100%;
    height: 3px;
    background-color: #fdba2c;
}
.bar {
  content: "";
  display: inline;
  position: absolute;
  width: 0;
  height: 100%;
  left: 50%;
  text-align: center;
}
.bar:nth-child(1) {
  background-color: #00923f;
  animation: loading 3s linear infinite;
}
.bar:nth-child(2) {
  background-color: #e96a0c;
  animation: loading 3s linear 1s infinite;
}
.bar:nth-child(3) {
  background-color: #245af3;
  animation: loading 3s linear 2s infinite;
}
@keyframes loading {
    from {left: 50%; width: 0;z-index:100;}
    33.3333% {left: 0; width: 100%;z-index: 10;}
    to {left: 0; width: 100%;}
}
</style>