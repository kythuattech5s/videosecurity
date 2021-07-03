<input type="hidden" value="<?php echo $id ?>" name="id">
<div class="row">
<?php 
$arr = exifSettings();
 ?>
<?php foreach ($arr as $key => $value) { ?>

		<div class="col-md-6 col-xs-12 form-group">
		<label for="" title="<?php echo $value["note"] ?>"><?php echo $value["n"] ?><span><?php echo @$value["note"]?"(".$value["note"].")":"" ?></span></label>
		<?php 
				$_val = $info[$key];
				if(!@$_val || $_val==""){
					$_val = array_key_exists("rv",$value)?$value["rv"]:"";
				}

				$_val = is_array($_val)?implode(",", $_val):$_val;
				?>
		<?php  if($value["s"]==0){ ?>
			<input type="text" readonly name='<?php echo $key ?>' class="form-control" value="<?php echo $_val; ?>">
		<?php  } else{ ?>

			<?php if(array_key_exists("v", $value) && is_array($value["v"])){ ?>
				
				<select class="form-control" name='<?php echo $key ?>'>
					<?php foreach($value["v"] as $k =>$v) { ?>
						<option <?php $_val == $k?"selected":"" ?> value="<?php echo $k ?>"><?php echo $v ?></option>
					<?php } ?>
				</select>
			<?php } else { ?>
				
		<input type="text" name='<?php echo $key ?>' class="form-control" value="<?php echo $_val; ?>">
		<?php  } ?>

	

		<?php  } ?>
</div>

<?php  } ?>
	
	<?php 
	foreach ($info2 as $key => $value) { ?>
		<div class="col-md-6 col-xs-12 form-group">
			<label for="" title="<?php echo $value["note"] ?>"><?php echo $value["n"] ?><span><?php echo @$value["note"]?"(".$value["note"].")":"" ?></span></label>
			<?php if(!@$value['com'] || $value['com']==1) { ?>
			<input type="text" name='<?php echo $key ?>' class="form-control" value="<?php echo $value["v"]; ?>">
			<?php } else if($value['com']==2){ ?>
				<textarea class="form-control" name='<?php echo $key ?>'><?php echo $value["v"]; ?></textarea>
			<?php } ?>
		</div>
	<?php }

	 ?>



</div>