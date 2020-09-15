<?php
/*
	Plugin Name: Fluent Forms email labels
	Plugin URI: https://github.com/shakyjake/fluent-forms-email-labels
	description: Allows you to output field labels on notification emails by using [[labels.your_field_name]]
	Version: 1.0
	Author: Jake Nicholson
	Author URI: https://github.com/shakyjake
	License: MIT
*/

function fluent_labelly_process_fields($emailBody, $fields){

	foreach($fields as $field){

		if($field->element === 'container'){
			$cols = $field->columns;
			foreach($cols as $col){
				$emailBody = fluent_labelly_process_fields($emailBody, $col->fields);
			}
		} else {

			if(!empty($field->attributes->name)){
				if(strpos($emailBody, '[[labels.' . $field->attributes->name . ']]') !== false){
					if(!empty($field->settings->label)){
						$emailBody = str_replace('[[labels.' . $field->attributes->name . ']]', $field->settings->label, $emailBody);
					} else if(!empty($field->settings->admin_field_label)){
						$emailBody = str_replace('[[labels.' . $field->attributes->name . ']]', $field->settings->admin_field_label, $emailBody);
					}
				}

			}

		}

		if(strpos($emailBody, '[[labels.') === false){
			return $emailBody;
		}
		
	}

	return $emailBody;

}

function fluent_labelly($emailBody, $entryId, $submittedData, $form){

	$fields = json_decode($form->form_fields)->fields;

	$emailBody = fluent_labelly_process_fields($emailBody, $fields);

	return $emailBody;

}

add_filter('fluentform_submission_message_parse', 'fluent_labelly', 20, 4);

?>
