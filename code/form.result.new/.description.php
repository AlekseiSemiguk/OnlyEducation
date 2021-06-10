<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("FEEDBACK_FORM_RESULT_NEW_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("FEEDBACK_FORM_RESULT_NEW_COMPONENT_DESCR"),
	"ICON" => "/images/comp_result_new.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "service",
		"CHILD" => array(
			"ID" => "form",
			"NAME" => GetMessage("FEEDBACK_FORM_SERVICE"),
			"CHILD" => array(
				"ID" => "form_cmpx",
			),
		)
	),
);
?>