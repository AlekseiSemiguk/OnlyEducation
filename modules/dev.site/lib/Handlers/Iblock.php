<?php

namespace Dev\Site\Handlers;


class Iblock
{

    public function AddLog(&$arFields)
    {

		global $USER;
		$IBLOCKLOG_ID = 4;

		// проверяем, не сработало ли событие на блоке LOG
		$iBlockId = $arFields['IBLOCK_ID'];
		if ($iBlockId == $IBLOCKLOG_ID) {
			return;
		}

		function getSectionsNameRecursiveFind ($element, &$arName) {
			if ($element['IBLOCK_SECTION_ID']) {
				$res = \CIBlockSection::GetByID($element['IBLOCK_SECTION_ID']);
				$ar_res = $res->GetNext();
				$arName[] = $ar_res['NAME'];
				getSectionsNameRecursiveFind ($ar_res, $arName);
			}
			else {
				return;
			}
		}

		$iBlock = \CIBlock::GetByID($iBlockId);
		$ar_iBlock = $iBlock->GetNext();
		$nameSectionLog = $ar_iBlock['NAME']."(".$ar_iBlock['CODE'].")";

		// проверяем наличие раздела, если отсутствует - создаем новый
		$arFilter = Array('IBLOCK_ID'=>$IBLOCKLOG_ID, 'CODE' => $ar_iBlock['CODE']);
		$section = \CIBlockSection::GetList("", $arFilter);
		if ($sectionLog = $section->GetNext())
		{
			$SECTION_ID = $sectionLog["ID"];
		} else {
			$bs = new \CIBlockSection;
			$arFieldsSection = Array(
			  "IBLOCK_SECTION_ID" => false,
			  "IBLOCK_ID" => $IBLOCKLOG_ID,
			  "NAME" => $nameSectionLog,
			  "CODE" => $ar_iBlock['CODE'],
			  );
		  	$SECTION_ID = $bs->Add($arFieldsSection);
		}

		$activeFromLog = new \Bitrix\Main\Type\Date;

		$element = \CIBlockElement::GetByID($arFields['ID']);
		$ar_element = $element->GetNext();

		// формируем описание анонса в виде: имя ИБ-> имя раздела -> ... -> имя элемента
		$arName = [];
		$arName[] = $ar_element['NAME'];
		getSectionsNameRecursiveFind ($ar_element, $arName);
		$arName[] = $ar_element['IBLOCK_NAME'];

		$arName = array_reverse($arName);
		$previewText = implode("->", $arName);

		$el = new \CIBlockElement;
		$arLoadProductArray = [
            'MODIFIED_BY' => $USER->GetID(),
            'IBLOCK_SECTION_ID' => $SECTION_ID,
            'IBLOCK_ID' => $IBLOCKLOG_ID,
            'NAME' => $arFields['ID'],
			'ACTIVE_FROM' => $activeFromLog,
        	'PREVIEW_TEXT'=> $previewText,
        ];


		$arFilter = Array('IBLOCK_ID'=>$IBLOCKLOG_ID, 'NAME' => $arFields['ID']);
		$elementInLogAr = \CIBlockElement::GetList("", $arFilter);
		if ($elementInLog = $elementInLogAr->GetNext())
		{
			$ELEMENT_ID = $elementInLog["ID"];
			$el->Update($ELEMENT_ID, $arLoadProductArray);
		} else {
			$el->Add($arLoadProductArray); 
		}

    }

    function OnBeforeIBlockElementAddHandler(&$arFields)
    {
        $iQuality = 95;
        $iWidth = 1000;
        $iHeight = 1000;
        /*
         * Получаем пользовательские свойства
         */
        $dbIblockProps = \Bitrix\Iblock\PropertyTable::getList(array(
            'select' => array('*'),
            'filter' => array('IBLOCK_ID' => $arFields['IBLOCK_ID'])
        ));
        /*
         * Выбираем только свойства типа ФАЙЛ (F)
         */
        $arUserFields = [];
        while ($arIblockProps = $dbIblockProps->Fetch()) {
            if ($arIblockProps['PROPERTY_TYPE'] == 'F') {
                $arUserFields[] = $arIblockProps['ID'];
            }
        }
        /*
         * Перебираем и масштабируем изображения
         */
        foreach ($arUserFields as $iFieldId) {
            foreach ($arFields['PROPERTY_VALUES'][$iFieldId] as &$file) {
                if (!empty($file['VALUE']['tmp_name'])) {
                    $sTempName = $file['VALUE']['tmp_name'] . '_temp';
                    $res = \CAllFile::ResizeImageFile(
                        $file['VALUE']['tmp_name'],
                        $sTempName,
                        array("width" => $iWidth, "height" => $iHeight),
                        BX_RESIZE_IMAGE_PROPORTIONAL_ALT,
                        false,
                        $iQuality);
                    if ($res) {
                        rename($sTempName, $file['VALUE']['tmp_name']);
                    }
                }
            }
        }

        if ($arFields['CODE'] == 'brochures') {
            $RU_IBLOCK_ID = \Only\Site\Helpers\IBlock::getIblockID('DOCUMENTS', 'CONTENT_RU');
            $EN_IBLOCK_ID = \Only\Site\Helpers\IBlock::getIblockID('DOCUMENTS', 'CONTENT_EN');
            if ($arFields['IBLOCK_ID'] == $RU_IBLOCK_ID || $arFields['IBLOCK_ID'] == $EN_IBLOCK_ID) {
                \CModule::IncludeModule('iblock');
                $arFiles = [];
                foreach ($arFields['PROPERTY_VALUES'] as $id => &$arValues) {
                    $arProp = \CIBlockProperty::GetByID($id, $arFields['IBLOCK_ID'])->Fetch();
                    if ($arProp['PROPERTY_TYPE'] == 'F' && $arProp['CODE'] == 'FILE') {
                        $key_index = 0;
                        while (isset($arValues['n' . $key_index])) {
                            $arFiles[] = $arValues['n' . $key_index++];
                        }
                    } elseif ($arProp['PROPERTY_TYPE'] == 'L' && $arProp['CODE'] == 'OTHER_LANG' && $arValues[0]['VALUE']) {
                        $arValues[0]['VALUE'] = null;
                        if (!empty($arFiles)) {
                            $OTHER_IBLOCK_ID = $RU_IBLOCK_ID == $arFields['IBLOCK_ID'] ? $EN_IBLOCK_ID : $RU_IBLOCK_ID;
                            $arOtherElement = \CIBlockElement::GetList([],
                                [
                                    'IBLOCK_ID' => $OTHER_IBLOCK_ID,
                                    'CODE' => $arFields['CODE']
                                ], false, false, ['ID'])
                                ->Fetch();
                            if ($arOtherElement) {
                                /** @noinspection PhpDynamicAsStaticMethodCallInspection */
                                \CIBlockElement::SetPropertyValues($arOtherElement['ID'], $OTHER_IBLOCK_ID, $arFiles, 'FILE');
                            }
                        }
                    } elseif ($arProp['PROPERTY_TYPE'] == 'E') {
                        $elementIds = [];
                        foreach ($arValues as &$arValue) {
                            if ($arValue['VALUE']) {
                                $elementIds[] = $arValue['VALUE'];
                                $arValue['VALUE'] = null;
                            }
                        }
                        if (!empty($arFiles && !empty($elementIds))) {
                            $rsElement = \CIBlockElement::GetList([],
                                [
                                    'IBLOCK_ID' => \Only\Site\Helpers\IBlock::getIblockID('PRODUCTS', 'CATALOG_' . $RU_IBLOCK_ID == $arFields['IBLOCK_ID'] ? '_RU' : '_EN'),
                                    'ID' => $elementIds
                                ], false, false, ['ID', 'IBLOCK_ID', 'NAME']);
                            while ($arElement = $rsElement->Fetch()) {
                                /** @noinspection PhpDynamicAsStaticMethodCallInspection */
                                \CIBlockElement::SetPropertyValues($arElement['ID'], $arElement['IBLOCK_ID'], $arFiles, 'FILE');
                            }
                        }
                    }
                }
            }
        }
    }

}
