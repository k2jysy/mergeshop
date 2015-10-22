<div class="modal fade" id="myPattern" tabindex="-1" >
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Pattern Manager</h4>
			</div>
			<div class="modal-body">
				<span class="spinner"></span>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-xs btn-danger" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div id="content" class="page-wrapper">
	<form method="post" id="form">
		<div class="wpo-customize" id="wpo-customize">
			<div class="wrapper">
				<div id="customize-form">
					<!-- THEMENAME -->
					<p>
						<span class="badge">
							<?php echo $this->l('Theme');?>: <?php echo TEXTDOMAIN; ?>
						</span>
						<?php 
							$url_back = '';
							if(class_exists('Options_Framework')){
								$url_back = admin_url('themes.php?page=options-framework');
							}else{
								$url_back = admin_url();
							}
						?>
						<a  href="<?php echo $url_back;?>" class="label label-default pull-right">
							<?php echo $this->l('Back');?>
						</a>
					</p>
					<!-- END THEMENAME -->

					<!-- BUTTON ACTION -->
					<div class="buttons-group livetheme-action">
						<a class="livetheme-save btn btn-primary btn-xs" type="submit" href="#" >
							<?php echo $this->l('Submit');?>
						</a>
						<a class="livetheme-delete btn btn-danger btn-xs" type="submit" href="#" >
							<?php echo $this->l('Delete');?>
						</a>
						<a data-toggle="modal" data-target="#myPattern" class="livetheme-pattner btn btn-default btn-xs btn-success" type="submit" href="#">
							<?php echo $this->l('Pattern Manager');?>
						</a>
					</div>
					<!-- END BUTTON ACTION -->
					<hr>

					<!-- SELECT ACTION -->
					<div class="groups clearfix">
						<div class="form-group pull-left">
							<label><?php echo $this->l('Css Profiles:'); ?></label>
							<select name="saved_file" id="saved-files" style="width:auto;">
								<option value=""> ------------- </option>
								<?php foreach( $files as $file ){ $file = str_replace( ".css","", $file); ?>
									<option value="<?php echo $file;?>"><?php echo $file; ?></option>
								<?php } ?>
							</select>
						</div>

						<div class="form-group ">
							<label class="show-for-notexisted"><?php echo $this->l('And Named This: '); ?></label>
							<label class="show-for-existed"><?php echo $this->l('And Rename To:'); ?></label>
							<input type="text" name="newfile" id="newfile" style="width:120px;" />
						</div>
					</div>
					<!-- END SELECT ACTION -->

					<!-- TAB INPUT -->
					<div class="clearfix" id="customize-body">
						<!-- TAB TITLE -->
						<ul class="nav nav-tabs" id="myTab">
						<?php $i=0; ?>
						<?php foreach( $xmlselectors as $for => $output ) { ?>
						    <li <?php if($i==0) echo 'class="active"'; ?>>
						    	<a data-toggle="tab" href="#tab-<?php echo $for ?>">
						    		<?php echo $this->l( $output['label'] );?>
						    	</a>
						    </li>
					    <?php
						    	$i++;
						    }// End foreach
					    ?>
						</ul>
						<!-- END TAB TITLE -->

						<!-- TAB CONTENT -->
						<div class="tab-content" >
						<?php $i=0; ?>
						<?php foreach( $xmlselectors as $for => $output ) { //var_dump($output);die;?>
							<div class="tab-pane <?php if($i==0) echo 'active'; ?>" id="tab-<?php echo $for; ?>">
								<?php if( !empty( $output ) ){?>
								<!-- ACCORDION -->
								<div class="accordion panel-group"  id="custom-accordion<?php echo $for; ?>">
									<?php $i=0; foreach ( $output['groups'] as $group ) { ?>
										<div class="accordion-group panel panel-default">
											<div 	class="accordion-heading panel-heading"
													data-toggle="collapse"
													data-parent="#custom-accordion<?php echo $for; ?>"
													href="#collapse<?php echo $group['match'];?>" >
												<a class="accordion-toggle">
		                               				<?php echo $group['header']; ?>
			                              		</a>
			                              	</div>
		                              		<!-- ACCORDION ITEM CONTENT -->
											<div id="collapse<?php echo $group['match'];?>"
													class="accordion-body panel-collapse collapse <?php if( $i++ ==0) { ?> in <?php } ?>">
												<div class="accordion-inner panel-body clearfix">
													<?php foreach ($group['selector'] as $item ) { ?>

													<?php  if (isset($item['type'])&&$item['type']=="image") { ?>
														<div class="form-group background-images">
															<label><?php echo $item['label']?></label>
															<a class="clear-bg label label-success" href="#">
																Clear
															</a>
															<input value="" type="hidden" name="customize[<?php echo $group['match'];?>][]" data-match="<?php echo $group['match'];?>" type="text" class="input-setting" data-selector="<?php echo $item['selector']?>" data-attrs="background-image">
															<div class="clearfix"></div>
															<p>
																<em style="font-size:10px"><?php echo $this->l('Those Images in folder YOURTHEME/images/bg/') ?></em>
															</p>
															<div class="bi-wrapper clearfix">
																<?php foreach ( $patterns as $pattern ){ ?>
																<div style="background:url('<?php echo $backgroundImageURL.$pattern;?>') no-repeat center center;" class="pull-left" data-image="<?php echo $backgroundImageURL.$pattern;?>" data-name="<?php echo $pattern; ?>" data-val="<?php echo $pattern; ?>">

																</div>
																<?php } ?>
															</div>
														</div>
													<?php } elseif( $item['type'] == "fontsize" ) { ?>
														<div class="form-group">
															<label><?php echo $item['label']?></label>
															<select name="customize[<?php echo $group['match'];?>][]" data-match="<?php echo $group['match']?>" type="text" class="input-setting enable" data-selector="<?php echo $item['selector']?>" data-attrs="<?php echo $item['attrs']?>">
																<option value="">Inherit</option>
																<?php for( $i=9; $i<=16; $i++ ) { ?>
																<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
																<?php } ?>
															</select>
															<a href="#" class="clear-bg label label-success">Clear</a>
														</div>
													<?php } else { ?>
														<div class="form-group">
															<label><?php echo $item['label']?></label>
															<input value="" size="10" name="customize[<?php echo $group['match']?>][]" data-match="<?php echo $group['match']?>" type="text" class="input-setting enable" data-selector="<?php echo $item['selector']?>" data-attrs="<?php echo $item['attrs']?>">
															<a href="#" class="clear-bg label label-success">Clear</a>
														</div>
													<?php } ?>


													<?php }//end foreach ?>
												</div>
											</div>
											<!-- END ACCORDION ITEM CONTENT -->
										</div>
									<?php } ?>
								</div>
								<!-- END ACCORDION -->
								<?php }//end if ?>
							</div>
						<?php $i++; ?>
						<?php }// end if ?>
						</div>
						<!-- END TAB CONTENT -->
					</div>
					<!-- END TAB INPUT -->
				</div>
			</div>
		</div>
	</form>

	<div id="main-preview">
		<iframe src="<?php echo home_url(); ?>"></iframe>
	</div>
</div>


<script>
	jQuery('#wrapper').WPO_livetheme({
		customizeURL:'<?php echo FRAMEWORK_CUSTOMZIME_STYLE_URI; ?>'
	});
</script>