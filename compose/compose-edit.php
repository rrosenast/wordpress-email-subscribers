<?php

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
	die('You are not allowed to call this page directly.');
}

?>

<div class="wrap">
	<?php
	$did = isset($_GET['did']) ? $_GET['did'] : '0';
	es_cls_security::es_check_number($did);

	// First check if ID exist with requested ID
	$result = es_cls_compose::es_template_count($did);
	if ($result != '1') {
		?><div class="error fade">
			<p><strong>
				<?php echo __( 'Oops, selected details does not exists.', ES_TDOMAIN ); ?>
			</strong></p>
		</div><?php
	} else {
		$es_errors = array();
		$es_success = '';
		$es_error_found = FALSE;

		$data = array();
		$data = es_cls_compose::es_template_select($did);

		// Preset the form fields
		$form = array(
			'es_templ_id' => $data['es_templ_id'],
			'es_templ_heading' => stripslashes($data['es_templ_heading']),
			'es_templ_body' => stripslashes($data['es_templ_body']),
			'es_templ_status' => $data['es_templ_status'],
			'es_email_type' => $data['es_email_type']
		);
	}

	// Form submitted, check the data
	if (isset($_POST['es_form_submit']) && $_POST['es_form_submit'] == 'yes') {
		//	Just security thingy that wordpress offers us
		check_admin_referer('es_form_edit');

		$form['es_templ_heading'] = isset($_POST['es_templ_heading']) ? $_POST['es_templ_heading'] : '';
		if ($form['es_templ_heading'] == '') {
			$es_errors[] = __('Please enter template heading.', 'email-subscribers');
			$es_error_found = TRUE;
		}
		$form['es_templ_body'] = isset($_POST['es_templ_body']) ? $_POST['es_templ_body'] : '';
		$form['es_templ_status'] = isset($_POST['es_templ_status']) ? $_POST['es_templ_status'] : '';
		$form['es_email_type'] = isset($_POST['es_email_type']) ? $_POST['es_email_type'] : '';
		$form['es_templ_id'] = isset($_POST['es_templ_id']) ? $_POST['es_templ_id'] : '0';

		//	No errors found, we can add this Group to the table
		if ($es_error_found == FALSE) {	
			$action = "";
			$action = es_cls_compose::es_template_ins($form, $action = "update");
			if($action == "sus") {
				$es_success = __( 'Template successfully updated. ', ES_TDOMAIN );
			}
		}
	}

	if ($es_error_found == TRUE && isset($es_errors[0]) == TRUE) {
		?><div class="error fade"><p><strong><?php echo $es_errors[0]; ?></strong></p></div><?php
	}

	if ($es_error_found == FALSE && strlen($es_success) > 0) {
		?>
		<div class="notice notice-success is-dismissible">
			<p><strong>
				<?php echo $es_success; ?>
			</strong></p>
		</div>
		<?php
	}

	?>

	<div class="form-wrap">
		<h2>
			<?php echo __( 'Edit Email', ES_TDOMAIN ); ?>
			<a class="add-new-h2" href="<?php echo ES_ADMINURL; ?>?page=es-compose&amp;ac=add"><?php echo __( 'Add New', ES_TDOMAIN ); ?></a>
			<a class="add-new-h2" target="_blank" href="<?php echo ES_FAV; ?>"><?php echo __( 'Help', ES_TDOMAIN ); ?></a>
		</h2>
		<form name="es_form" method="post" action="#" onsubmit="return _es_submit()">
			<label for="tag-link"><?php echo __( 'Select your Mail Type', ES_TDOMAIN ); ?></label>
			<select name="es_email_type" id="es_email_type">

				<!--XTEC ************ MODIFICAT - Localization support-->
				<!--2015.10.06 @dgras-->
			  	<option value='Static Template' <?php if($form['es_email_type']=='Static Template') { echo 'selected="selected"' ; } ?>><?php _e('Static Template (For Newsletter Email)', ES_TDOMAIN) ?></option>
			  	<option value='Dynamic Template' <?php if($form['es_email_type']=='Dynamic Template') { echo 'selected="selected"' ; } ?>><?php _e('Dynamic Template (For Notification Email)', ES_TDOMAIN)?></option>
				<!--************ ORIGINAL-->
				<!--				
				<option value='Static Template' <?php if( $form['es_email_type'] == 'Static Template' ) { echo 'selected="selected"' ; } ?>><?php echo __( 'Static Template (For Newsletter Emails)', ES_TDOMAIN ); ?></option>
				<option value='Dynamic Template' <?php if( $form['es_email_type'] == 'Dynamic Template' ) { echo 'selected="selected"' ; } ?>><?php echo __( 'Dynamic Template (For Post Notification Emails)', ES_TDOMAIN ); ?></option>
				-->
				<!--************ FI-->

			</select>
			<p></p>

			<label for="tag-link"><?php echo __( 'Enter Mail Subject', ES_TDOMAIN ); ?></label>
			<input name="es_templ_heading" type="text" id="es_templ_heading" value="<?php echo esc_html(stripslashes($form['es_templ_heading'])); ?>" size="80" maxlength="225" />
			<p><?php echo __( 'Keyword: ###POSTTITLE###', ES_TDOMAIN ); ?></p>

			<label for="tag-link"><?php echo __( 'Enter Content for your Mail', ES_TDOMAIN ); ?></label>
			<?php $settings_body = array( 'textarea_rows' => 25 ); ?>
			<?php wp_editor(stripslashes($form['es_templ_body']), "es_templ_body", $settings_body);?>
			<p>

				<!-- XTEC ************ MODIFICAT - Hidden external link -->
				<!-- 2017.02.15 @xaviernietosanchez -->
				<?php echo sprintf(__( '%s : ###NAME###, ###EMAIL###, ###DATE###, ###POSTTITLE###, ###POSTLINK###, ###POSTLINK-WITHTITLE###, ###POSTLINK-ONLY###, ###POSTIMAGE###, ###POSTDESC###, ###POSTFULL###', ES_TDOMAIN ), __( 'Available Keywords', ES_TDOMAIN ) ); ?><br />
				<!-- ************ ORIGINAL -->
				<!--
				<?php echo sprintf(__( '%s : ###NAME###, ###EMAIL###, ###DATE###, ###POSTTITLE###, ###POSTLINK###, ###POSTLINK-WITHTITLE###, ###POSTLINK-ONLY###, ###POSTIMAGE###, ###POSTDESC###, ###POSTFULL###', ES_TDOMAIN ), '<a href="http://www.icegram.com/documentation/es-what-are-static-templates-and-dynamic-templates/" target="_blank">' . __( 'Available Keywords', ES_TDOMAIN ) . '</a>' ); ?><br />
				-->
				<!-- ************ FI -->
			</p>

			<div class="template_status" style="display:none;">
				<label for="tag-link"><?php echo __( 'Status', ES_TDOMAIN ); ?></label>
				<select name="es_templ_status" id="es_templ_status">

					<!--XTEC ************ MODIFICAT - Modify the visiblity if the user is not a xtec_super_admin -->
					<!-- 2015.10.01 @dgras-->
		  		  	<option value='Published' <?php if($form['es_templ_status']=='Published') { echo 'selected="selected"' ; } ?> ><?php _e('Published', 'email-subscribers'); ?></option>
					<!--************ ORIGINAL	-->
					<!--
					<option value='Published' <?php if( $form['es_templ_status'] == 'Published' ) { echo 'selected="selected"' ; } ?>><?php echo __( 'Published', ES_TDOMAIN ); ?></option>
					-->
					<!--************ FI-->

				</select>
				<p><?php echo __( 'Please select your mail status', ES_TDOMAIN ); ?></p>
			</div>
			<input type="hidden" name="es_form_submit" value="yes"/>
			<input type="hidden" name="es_templ_id" id="es_templ_id" value="<?php echo $form['es_templ_id']; ?>"/>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php echo __( 'Save', ES_TDOMAIN ); ?>" />
			</p>
			<?php wp_nonce_field('es_form_edit'); ?>
		</form>
	</div>

	<!--XTEC ************ MODIFICAT - Modify the visiblity if the user is not a xtec_super_admin -->
	<!-- 2015.10.01 @dgras-->
	<?php if(is_xtec_super_admin()) : ?>
		<p class="description"><?php echo ES_OFFICIAL; ?></p>
	<?php endif; ?>
	<!--************ ORIGINAL	-->
	<!--
	<p class="description"><?php echo ES_OFFICIAL; ?></p>
	-->
	<!--************ FI-->

</div>