<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>
<?foreach($arResult["ITEMS"] as $arItem):?>
<?

$res = CIBlockElement::GetByID($arItem['ID']);
$ar_res = $res->GetNext();

$sect_id = $ar_res['IBLOCK_SECTION_ID'];


$arSections[$sect_id]["ITEMS"][] =  $arItem;


if (!array_key_exists("URL", $arSections[$sect_id]) && !array_key_exists("NAME", $arSections[$sect_id])) {
$res_sect = CIBlockSection::GetByID($sect_id);
$ar_res_sect = $res_sect->GetNext();
$arSections[$sect_id]["URL"] =  $ar_res_sect['SECTION_PAGE_URL'];
$arSections[$sect_id]["NAME"] =  $ar_res_sect['NAME'];
};

?>
<?endforeach;?>

<?
$arResult['ARR_SECTIONS'] = $arSections;

?>

