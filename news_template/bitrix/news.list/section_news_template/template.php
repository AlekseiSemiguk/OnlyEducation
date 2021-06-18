<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<div id="barba-wrapper">
	<h1><?=$arResult["SECTION"]["PATH"]["0"]["NAME"]?></h1><br>
<div class="article-list">
<?foreach($arResult["ITEMS"] as $arItem):?>
	<?
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<a class="article-item article-list__item" id="<?=$this->GetEditAreaId($arItem['ID']);?>" href="<?=$arItem["DETAIL_PAGE_URL"]?>" data-anim="anim-3">
        <div class="article-item__background"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>"
                                                   alt="<?=$arItem["PREVIEW_PICTURE"]["ALT"]?>"/></div>
        <div class="article-item__wrapper">
            <div class="article-item__title"><?=$arItem["NAME"]?></div>
            <div class="article-item__content"><?=$arItem["PREVIEW_TEXT"]?>
            </div>
        </div>
    </a>
<?endforeach;?>



</div>
<p><a class="article-card__button" href="<?=$arResult["SECTION"]["PATH"]["0"]["LIST_PAGE_URL"]?>">Назад к списку всех услуг</a></p><br><br>
</div>
