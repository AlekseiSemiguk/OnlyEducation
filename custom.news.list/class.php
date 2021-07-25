<?php

use Bitrix\Main\Loader,
	Bitrix\Iblock;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

class CCustomNewsListComponent extends CBitrixComponent
{

	public function onPrepareComponentParams($arParams)
	{
		if(!isset($arParams["CACHE_TIME"]))
			$arParams["CACHE_TIME"] = 36000000;

		$arParams["IBLOCK_TYPE"] = trim($arParams["IBLOCK_TYPE"]);
		if($arParams["IBLOCK_TYPE"] == '')
			$arParams["IBLOCK_TYPE"] = "news";

		$arParams["IBLOCK_ID"] = trim($arParams["IBLOCK_ID"]);
		$arParams["PARENT_SECTION"] = intval($arParams["PARENT_SECTION"]);
		$arParams["INCLUDE_SUBSECTIONS"] = $arParams["INCLUDE_SUBSECTIONS"]!="N";
		$arParams["SET_LAST_MODIFIED"] = $arParams["SET_LAST_MODIFIED"]==="Y";
		
		$arParams["SORT_BY1"] = trim($arParams["SORT_BY1"]);
		if($arParams["SORT_BY1"] == '')
			$arParams["SORT_BY1"] = "ACTIVE_FROM";
		if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER1"]))
			$arParams["SORT_ORDER1"]="DESC";
		
		if($arParams["SORT_BY2"] == '')
		{
			if (mb_strtoupper($arParams["SORT_BY1"]) == 'SORT')
			{
				$arParams["SORT_BY2"] = "ID";
				$arParams["SORT_ORDER2"] = "DESC";
			}
			else
			{
				$arParams["SORT_BY2"] = "SORT";
			}
		}
		if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["SORT_ORDER2"]))
			$arParams["SORT_ORDER2"]="ASC";

		if($arParams["FILTER_NAME"] == '' || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
		{
			$arrFilter = array();
		}
		else
		{
			$arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
			if(!is_array($arrFilter))
				$arrFilter = array();
		}

		$arParams["CHECK_DATES"] = $arParams["CHECK_DATES"]!="N";
		
		if(!is_array($arParams["FIELD_CODE"]))
			$arParams["FIELD_CODE"] = array();
		foreach($arParams["FIELD_CODE"] as $key=>$val)
			if(!$val)
				unset($arParams["FIELD_CODE"][$key]);
		
		if(empty($arParams["PROPERTY_CODE"]) || !is_array($arParams["PROPERTY_CODE"]))
			$arParams["PROPERTY_CODE"] = array();
		foreach($arParams["PROPERTY_CODE"] as $key=>$val)
			if($val==="")
				unset($arParams["PROPERTY_CODE"][$key]);

		if(empty($arParams["PROPERTY_CODE_VALIDATE_EMAIL"]) || !is_array($arParams["PROPERTY_CODE_VALIDATE_EMAIL"]))
			$arParams["PROPERTY_CODE_VALIDATE_EMAIL"] = array();
		foreach($arParams["PROPERTY_CODE_VALIDATE_EMAIL"] as $key=>$val)
			if($val==="")
				unset($arParams["PROPERTY_CODE_VALIDATE_EMAIL"][$key]);

		$arParams["DETAIL_URL"]=trim($arParams["DETAIL_URL"]);
		
		$arParams["NEWS_COUNT"] = intval($arParams["NEWS_COUNT"]);
		if($arParams["NEWS_COUNT"]<=0)
			$arParams["NEWS_COUNT"] = 20;

		$arParams["CACHE_FILTER"] = $arParams["CACHE_FILTER"]=="Y";
		if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
			$arParams["CACHE_TIME"] = 0;

		$arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N"; 
		$arParams["SET_BROWSER_TITLE"] = (isset($arParams["SET_BROWSER_TITLE"]) && $arParams["SET_BROWSER_TITLE"] === 'N' ? 'N' : 'Y');
		$arParams["SET_META_KEYWORDS"] = (isset($arParams["SET_META_KEYWORDS"]) && $arParams["SET_META_KEYWORDS"] === 'N' ? 'N' : 'Y');
		$arParams["SET_META_DESCRIPTION"] = (isset($arParams["SET_META_DESCRIPTION"]) && $arParams["SET_META_DESCRIPTION"] === 'N' ? 'N' : 'Y');
		$arParams["ADD_SECTIONS_CHAIN"] = $arParams["ADD_SECTIONS_CHAIN"]!="N"; //Turn on by default
		$arParams["INCLUDE_IBLOCK_INTO_CHAIN"] = $arParams["INCLUDE_IBLOCK_INTO_CHAIN"]!="N";
		$arParams["STRICT_SECTION_CHECK"] = (isset($arParams["STRICT_SECTION_CHECK"]) && $arParams["STRICT_SECTION_CHECK"] === "Y");
		$arParams["ACTIVE_DATE_FORMAT"] = trim($arParams["ACTIVE_DATE_FORMAT"]);
		if($arParams["ACTIVE_DATE_FORMAT"] == '')
			$arParams["ACTIVE_DATE_FORMAT"] = $DB->DateFormatToPHP(CSite::GetDateFormat("SHORT"));
		$arParams["PREVIEW_TRUNCATE_LEN"] = intval($arParams["PREVIEW_TRUNCATE_LEN"]);
		$arParams["HIDE_LINK_WHEN_NO_DETAIL"] = $arParams["HIDE_LINK_WHEN_NO_DETAIL"]=="Y";

		$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
		$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
		$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
		$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]=="Y";
		$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
		$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
		$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
		$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]=="Y";
		$arParams["CHECK_PERMISSIONS"] = $arParams["CHECK_PERMISSIONS"]!="N";

		if($arParams["DISPLAY_TOP_PAGER"] || $arParams["DISPLAY_BOTTOM_PAGER"])
		{
			$arNavParams = array(
				"nPageSize" => $arParams["NEWS_COUNT"],
				"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
				"bShowAll" => $arParams["PAGER_SHOW_ALL"],
			);
			$arNavigation = CDBResult::GetNavParams($arNavParams);
			if($arNavigation["PAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
				$arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];
		}
		else
		{
			$arNavParams = array(
				"nTopCount" => $arParams["NEWS_COUNT"],
				"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
			);
			$arNavigation = false;
		}
		
		if (empty($arParams["PAGER_PARAMS_NAME"]) || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PAGER_PARAMS_NAME"]))
		{
			$pagerParameters = array();
		}
		else
		{
			$pagerParameters = $GLOBALS[$arParams["PAGER_PARAMS_NAME"]];
			if (!is_array($pagerParameters))
				$pagerParameters = array();
		}
		
		$arParams["USE_PERMISSIONS"] = $arParams["USE_PERMISSIONS"]=="Y";
		if(!is_array($arParams["GROUP_PERMISSIONS"]))
			$arParams["GROUP_PERMISSIONS"] = array(1);
		
		$bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
		if($arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"]))
		{
			$arUserGroupArray = $USER->GetUserGroupArray();
			foreach($arParams["GROUP_PERMISSIONS"] as $PERM)
			{
				if(in_array($PERM, $arUserGroupArray))
				{
					$bUSER_HAVE_ACCESS = true;
					break;
				}
			}
		}

		$this->arNavParams = $arNavParams;
		$this->arNavigation = $arNavigation;
		$this->bUSER_HAVE_ACCESS = $bUSER_HAVE_ACCESS;
		$this->arrFilter = $arrFilter;
		$this->pagerParameters = $pagerParameters;
		return $arParams;
	}

	public function executeComponent()
    {
		global $USER;

		$arParams = $this->arParams;

		if($this->startResultCache(false, array(($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups()), $this->bUSER_HAVE_ACCESS, $this->arNavigation, $this->arrFilter, $this->pagerParameters)))
		{
			if(!Loader::includeModule("iblock"))
			{
				$this->abortResultCache();
				ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
				return;
			}

			if($arParams["IBLOCK_ID"] > 0)
			{
				$this->typeList = "oneIBlock";
				$rsIBlock = CIBlock::GetList(array(), array(
					"ACTIVE" => "Y",
					"ID" => $arParams["IBLOCK_ID"],
				));

				if (!$this->setArResultIfOneIBlock($rsIBlock))
					return;
			}
			else
			{
				$this->typeList = "someIBlock";
				$rsIBlock = CIBlock::GetList(array(), array(
					"ACTIVE" => "Y",
					"TYPE" => $arParams["IBLOCK_TYPE"],
				));

				if (!$this->setArResultIfSomeIBlock($rsIBlock))
					return;
			}

			$this->setResultCacheKeys(array(
				"ID",
				"IBLOCK_TYPE_ID",
				"LIST_PAGE_URL",
				"NAV_CACHED_DATA",
				"NAME",
				"SECTION",
				"ELEMENTS",
				"IPROPERTY_VALUES",
				"ITEMS_TIMESTAMP_X",
			));

			$this->includeComponentTemplate();
		}

		$arResult = & $this->arResult;

		$this->setTemplateCachedData($arResult["NAV_CACHED_DATA"]);

		if ($this->typeList == "oneIBlock")
		{ 
			$this->addIntranetToolbar();

			$this->ipropertyExists = (!empty($arResult["IPROPERTY_VALUES"]) && is_array($arResult["IPROPERTY_VALUES"]));
			$this->iproperty = ($this->ipropertyExists ? $arResult["IPROPERTY_VALUES"] : array());

			$this->setTitle();
			$this->setMetaTags();
			$this->addTitleInChain();
		}


    }

	protected function setArResultIfOneIBlock($rsIBlock)
	{
		$arParams = $this->arParams;
		$arResult = $rsIBlock->GetNext();

		if (!$arResult)
		{
			$this->get404IfNoIblock();
			return false;
		}

		$iblockid = $arResult["ID"];
		$this->setCommonArResultAndArFilter($arResult, $arParams, $iblockid);

		$rsElement = $this->getItemsAndElements($arResult["ITEMS"], $arResult["ELEMENTS"], $iblockid);

		$this->setNavParams($rsElement, $arResult);

		$this->arResult = $arResult;

		return true;
	}

	protected function setArResultIfSomeIBlock($rsIBlock)
	{
		$arParams = $this->arParams;
		$arResult = array();

		$iblockid = "";
		$this->setCommonArResultAndArFilter($arResult, $arParams, $iblockid);

		while ($arRes = $rsIBlock->GetNext())
		{
			$arResult["IBLOCKS"][$arRes["ID"]] = $arRes;
			$this->arFilter["IBLOCK_ID"] = $arRes["ID"];
			$this->getItemsAndElements($arResult["ITEMS"][$arRes["ID"]], $arResult["ELEMENTS"][$arRes["ID"]], $arRes["ID"]);
		}

		if (!$arResult["IBLOCKS"])
		{
			$this->get404IfNoIblock();
			return false;
		}

		$this->arResult = $arResult;

		return true;
	}

	protected function get404IfNoIblock()
	{
		$arParams =& $this->arParams;
		$this->abortResultCache();
		Iblock\Component\Tools::process404(
			trim($arParams["MESSAGE_404"]) ?: GetMessage("T_NEWS_NEWS_NA")
			,true
			,$arParams["SET_STATUS_404"] === "Y"
			,$arParams["SHOW_404"] === "Y"
			,$arParams["FILE_404"]
		);
	}

	protected function setCommonArResultAndArFilter(& $arResult, & $arParams, $iblockid)
	{
		$arResult["USER_HAVE_ACCESS"] = $bUSER_HAVE_ACCESS;

		//SELECT
		$arSelect = array_merge($arParams["FIELD_CODE"], array(
			"ID",
			"IBLOCK_ID",
			"IBLOCK_SECTION_ID",
			"NAME",
			"ACTIVE_FROM",
			"TIMESTAMP_X",
			"DETAIL_PAGE_URL",
			"LIST_PAGE_URL",
			"DETAIL_TEXT",
			"DETAIL_TEXT_TYPE",
			"PREVIEW_TEXT",
			"PREVIEW_TEXT_TYPE",
			"PREVIEW_PICTURE",
		));
		$bGetProperty = !empty($arParams["PROPERTY_CODE"]);

		//WHERE
		$arFilter = array (
			"IBLOCK_ID" => $iblockid,
			"IBLOCK_LID" => SITE_ID,
			"ACTIVE" => "Y",
			"CHECK_PERMISSIONS" => $arParams['CHECK_PERMISSIONS'] ? "Y" : "N",
		);
	
		if($arParams["CHECK_DATES"])
			$arFilter["ACTIVE_DATE"] = "Y";
	
		$PARENT_SECTION = CIBlockFindTools::GetSectionID(
			$arParams["PARENT_SECTION"],
			$arParams["PARENT_SECTION_CODE"],
			array(
				"GLOBAL_ACTIVE" => "Y",
				"IBLOCK_ID" => $iblockid,
			)
		);
	
		if (
			$arParams["STRICT_SECTION_CHECK"]
			&& (
				$arParams["PARENT_SECTION"] > 0
				|| $arParams["PARENT_SECTION_CODE"] <> ''
			)
		)
		{
			if ($PARENT_SECTION <= 0)
			{
				$this->abortResultCache();
				Iblock\Component\Tools::process404(
					trim($arParams["MESSAGE_404"]) ?: GetMessage("T_NEWS_NEWS_NA")
					,true
					,$arParams["SET_STATUS_404"] === "Y"
					,$arParams["SHOW_404"] === "Y"
					,$arParams["FILE_404"]
				);
				return;
			}
		}
	
		$arParams["PARENT_SECTION"] = $PARENT_SECTION;
	
		if($arParams["PARENT_SECTION"]>0)
		{
			$arFilter["SECTION_ID"] = $arParams["PARENT_SECTION"];
			if($arParams["INCLUDE_SUBSECTIONS"])
				$arFilter["INCLUDE_SUBSECTIONS"] = "Y";
	
			$arResult["SECTION"]= array("PATH" => array());
			$rsPath = CIBlockSection::GetNavChain($arResult["ID"], $arParams["PARENT_SECTION"]);
			$rsPath->SetUrlTemplates("", $arParams["SECTION_URL"], $arParams["IBLOCK_URL"]);
			while($arPath = $rsPath->GetNext())
			{
				$ipropValues = new Iblock\InheritedProperty\SectionValues($arParams["IBLOCK_ID"], $arPath["ID"]);
				$arPath["IPROPERTY_VALUES"] = $ipropValues->getValues();
				$arResult["SECTION"]["PATH"][] = $arPath;
			}
	
			$ipropValues = new Iblock\InheritedProperty\SectionValues($arResult["ID"], $arParams["PARENT_SECTION"]);
			$arResult["IPROPERTY_VALUES"] = $ipropValues->getValues();
		}
		else
		{
			$arResult["SECTION"]= false;
		}

		//ORDER BY
		$arSort = array(
			$arParams["SORT_BY1"]=>$arParams["SORT_ORDER1"],
			$arParams["SORT_BY2"]=>$arParams["SORT_ORDER2"],
		);
		if(!array_key_exists("ID", $arSort))
			$arSort["ID"] = "DESC";
	
		$shortSelect = array('ID', 'IBLOCK_ID');
		foreach (array_keys($arSort) as $index)
		{
			if (!in_array($index, $shortSelect))
			{
				$shortSelect[] = $index;
			}
		}
	
		$arResult["ITEMS"] = array();
		$arResult["ELEMENTS"] = array();
		$this->arSelect = $arSelect;
		$this->arSort = $arSort;
		$this->shortSelect = $shortSelect;
		$this->arFilter = $arFilter;
		$this->bGetProperty = $bGetProperty;

	}

	protected function getItemsAndElements(& $arrayForItems, & $arrayForElements, $curIblock_id)
	{
		$rsElement = CIBlockElement::GetList($this->arSort, array_merge($this->arFilter, $this->arrFilter), false, $this->arNavParams, $this->shortSelect);
		while ($row = $rsElement->Fetch())
		{
			$id = (int)$row['ID'];
			$arrayForItems[$id] = $row;
			$arrayForElements[] = $id;
		}
		unset($row);
	
		if (!empty($arrayForItems))
		{
			$elementFilter = array(
				"IBLOCK_ID" => $curIblock_id,
				"IBLOCK_LID" => SITE_ID,
				"ID" => $arrayForElements,
			);
	
			$obParser = new CTextParser;
			$iterator = CIBlockElement::GetList(array(), $elementFilter, false, false, $this->arSelect);
			$iterator->SetUrlTemplates($this->arParams["DETAIL_URL"], "", $this->arParams["IBLOCK_URL"]);
			while ($arItem = $iterator->GetNext())
			{
				$arButtons = CIBlock::GetPanelButtons(
					$arItem["IBLOCK_ID"],
					$arItem["ID"],
					0,
					array("SECTION_BUTTONS" => false, "SESSID" => false)
				);
				$arItem["EDIT_LINK"] = $arButtons["edit"]["edit_element"]["ACTION_URL"];
				$arItem["DELETE_LINK"] = $arButtons["edit"]["delete_element"]["ACTION_URL"];
	
				if ($this->arParams["PREVIEW_TRUNCATE_LEN"] > 0)
					$arItem["PREVIEW_TEXT"] = $obParser->html_cut($arItem["PREVIEW_TEXT"], $this->arParams["PREVIEW_TRUNCATE_LEN"]);

				if ($arItem["ACTIVE_FROM"] <> '')
					$arItem["DISPLAY_ACTIVE_FROM"] = CIBlockFormatProperties::DateFormat($this->arParams["ACTIVE_DATE_FORMAT"], MakeTimeStamp($arItem["ACTIVE_FROM"], CSite::GetDateFormat()));
				else
					$arItem["DISPLAY_ACTIVE_FROM"] = "";
	
				Iblock\InheritedProperty\ElementValues::queue($arItem["IBLOCK_ID"], $arItem["ID"]);
	
				$arItem["FIELDS"] = array();
	
				if ($this->bGetProperty)
				{
					$arItem["PROPERTIES"] = array();
				}
				$arItem["DISPLAY_PROPERTIES"] = array();

				$id = (int)$arItem["ID"];
				$arrayForItems[$id] = $arItem;
			}
			unset($obElement);
			unset($iterator);
	
			if ($this->bGetProperty)
			{
				unset($elementFilter['IBLOCK_LID']);
				CIBlockElement::GetPropertyValuesArray(
					$arrayForItems,
					$curIblock_id,
					$elementFilter
				);
			}
		}
	
		$arrayForItems = array_values($arrayForItems);
	
		foreach ($arrayForItems as &$arItem)
		{
			if ($this->bGetProperty)
			{
				foreach ($this->arParams["PROPERTY_CODE"] as $pid)
				{
					$prop = &$arItem["PROPERTIES"][$pid];
					if (
						(is_array($prop["VALUE"]) && count($prop["VALUE"]) > 0)
						|| (!is_array($prop["VALUE"]) && $prop["VALUE"] <> '')
					)
					{
						if (in_array($pid, $this->arParams[PROPERTY_CODE_VALIDATE_EMAIL]))
						{
							$prop["VALUE"] = (check_email($prop["VALUE"])) ? $prop["VALUE"] : "e-mail не указан";
						}
						$arItem["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arItem, $prop, "news_out");
					}
				}
			}
	
			$ipropValues = new Iblock\InheritedProperty\ElementValues($arItem["IBLOCK_ID"], $arItem["ID"]);
			$arItem["IPROPERTY_VALUES"] = $ipropValues->getValues();
			Iblock\Component\Tools::getFieldImageData(
				$arItem,
				array('PREVIEW_PICTURE', 'DETAIL_PICTURE'),
				Iblock\Component\Tools::IPROPERTY_ENTITY_ELEMENT,
				'IPROPERTY_VALUES'
			);
	
			foreach($this->arParams["FIELD_CODE"] as $code)
				if(array_key_exists($code, $arItem))
					$arItem["FIELDS"][$code] = $arItem[$code];
		}
		unset($arItem);

		return $rsElement;
	}

	protected function setNavParams($rsElement, & $arResult)
	{
		$navComponentParameters = array();

		if ($this->arParams["PAGER_BASE_LINK_ENABLE"] === "Y")
		{
			$pagerBaseLink = trim($this->arParams["PAGER_BASE_LINK"]);
			if ($pagerBaseLink === "")
			{
				if (
					$arResult["SECTION"]
					&& $arResult["SECTION"]["PATH"]
					&& $arResult["SECTION"]["PATH"][0]
					&& $arResult["SECTION"]["PATH"][0]["~SECTION_PAGE_URL"]
				)
				{
					$pagerBaseLink = $arResult["SECTION"]["PATH"][0]["~SECTION_PAGE_URL"];
				}
				elseif (
					isset($arItem) && isset($arItem["~LIST_PAGE_URL"])
				)
				{
					$pagerBaseLink = $arItem["~LIST_PAGE_URL"];
				}
			}
	
			if ($pagerParameters && isset($pagerParameters["BASE_LINK"]))
			{
				$pagerBaseLink = $pagerParameters["BASE_LINK"];
				unset($pagerParameters["BASE_LINK"]);
			}
	
			$navComponentParameters["BASE_LINK"] = CHTTP::urlAddParams($pagerBaseLink, $pagerParameters, array("encode"=>true));
		}
	
		$arResult["NAV_STRING"] = $rsElement->GetPageNavStringEx(
			$navComponentObject,
			$arParams["PAGER_TITLE"],
			$arParams["PAGER_TEMPLATE"],
			$arParams["PAGER_SHOW_ALWAYS"],
			$this,
			$navComponentParameters
		);
		$arResult["NAV_CACHED_DATA"] = null;
		$arResult["NAV_RESULT"] = $rsElement;
		$arResult["NAV_PARAM"] = $navComponentParameters;
	}

	protected function addIntranetToolbar()
	{
		global $USER, $APPLICATION;
		$arParams = & $this->arParams;
		$arResult = & $this->arResult;

		if($USER->IsAuthorized())
		{
			if(
				$APPLICATION->GetShowIncludeAreas()
				|| (is_object($GLOBALS["INTRANET_TOOLBAR"]) && $arParams["INTRANET_TOOLBAR"]!=="N")
			)
			{
				if(Loader::includeModule("iblock"))
				{
					$arButtons = CIBlock::GetPanelButtons(
						$arResult["ID"],
						0,
						$arParams["PARENT_SECTION"],
						array("SECTION_BUTTONS"=>false)
					);
	
					if($APPLICATION->GetShowIncludeAreas())
						$this->addIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));

					if(
						is_array($arButtons["intranet"])
						&& is_object($INTRANET_TOOLBAR)
						&& $arParams["INTRANET_TOOLBAR"]!=="N"
					)
					{
						$APPLICATION->AddHeadScript('/bitrix/js/main/utils.js');
						foreach($arButtons["intranet"] as $arButton)
							$INTRANET_TOOLBAR->AddButton($arButton);
					}
	
					$this->arButtons = $arButtons;

				}
			}
		}

	}

	protected function setTitle()
	{
		global $USER, $APPLICATION;
		$arParams = & $this->arParams;
		$arResult = & $this->arResult;

		$this->arTitleOptions = null;

		if($arParams["SET_TITLE"])
		{
			if($USER->IsAuthorized())
			{
				$this->arTitleOptions = array(
					'ADMIN_EDIT_LINK' => $this->arButtons["submenu"]["edit_iblock"]["ACTION"],
					'PUBLIC_EDIT_LINK' => "",
					'COMPONENT_NAME' => $this->getName(),
				);
			}

			if ($this->ipropertyExists && $this->iproperty["SECTION_PAGE_TITLE"] != "")
				$APPLICATION->SetTitle($this->iproperty["SECTION_PAGE_TITLE"], $this->arTitleOptions);
			elseif(isset($arResult["NAME"]))
				$APPLICATION->SetTitle($arResult["NAME"], $this->arTitleOptions);
		}
	}


	protected function setMetaTags()
	{
		global $APPLICATION;
		$arParams = & $this->arParams;
		$iproperty = & $this->iproperty;
		$arTitleOptions = & $this->arTitleOptions;

		if ($this->ipropertyExists)
		{
			if ($arParams["SET_BROWSER_TITLE"] === 'Y' && $iproperty["SECTION_META_TITLE"] != "")
				$APPLICATION->SetPageProperty("title", $iproperty["SECTION_META_TITLE"], $arTitleOptions);

			if ($arParams["SET_META_KEYWORDS"] === 'Y' && $iproperty["SECTION_META_KEYWORDS"] != "")
				$APPLICATION->SetPageProperty("keywords", $iproperty["SECTION_META_KEYWORDS"], $arTitleOptions);

			if ($arParams["SET_META_DESCRIPTION"] === 'Y' && $iproperty["SECTION_META_DESCRIPTION"] != "")
				$APPLICATION->SetPageProperty("description", $iproperty["SECTION_META_DESCRIPTION"], $arTitleOptions);
		}
	}

	protected function addTitleInChain()
	{
		global $APPLICATION;
		$arParams = & $this->arParams;
		$arResult = & $this->arResult;

		if($arParams["INCLUDE_IBLOCK_INTO_CHAIN"] && isset($arResult["NAME"]))
		{
			if($arParams["ADD_SECTIONS_CHAIN"] && is_array($arResult["SECTION"]))
				$APPLICATION->AddChainItem(
					$arResult["NAME"]
					,$arParams["IBLOCK_URL"] <> ''? $arParams["IBLOCK_URL"]: $arResult["LIST_PAGE_URL"]
				);
			else
				$APPLICATION->AddChainItem($arResult["NAME"]);
		}
	
		if($arParams["ADD_SECTIONS_CHAIN"] && is_array($arResult["SECTION"]))
		{
			foreach($arResult["SECTION"]["PATH"] as $arPath)
			{
				if ($arPath["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"] != "")
					$APPLICATION->AddChainItem($arPath["IPROPERTY_VALUES"]["SECTION_PAGE_TITLE"], $arPath["~SECTION_PAGE_URL"]);
				else
					$APPLICATION->AddChainItem($arPath["NAME"], $arPath["~SECTION_PAGE_URL"]);
			}
		}
	}

}