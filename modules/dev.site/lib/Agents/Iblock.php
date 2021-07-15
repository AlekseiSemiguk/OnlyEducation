<?php

namespace Dev\Site\Agents;


class Iblock
{
    public static function ClearOldLogs()
    {
		\Bitrix\Main\Loader::includeModule('iblock');
		global $DB;
		
		$IBLOCKLOG_ID = 4;
		
		$arFilter = Array('IBLOCK_ID'=>$IBLOCKLOG_ID);
		$element_list = \CIBlockElement::GetList(Array("TIMESTAMP_X"=>"DESC"), $arFilter);
		
		$arElementsId = [];
		
			while ($element = $element_list->Fetch())
			{
				$arElementsId [] = $element["ID"];
			} 
		
		array_splice($arElementsId, 0, 10);
		
		foreach ($arElementsId as $value) {
			$DB->StartTransaction();
			if(!\CIBlockElement::Delete($value))
			{
				$DB->Rollback();
			}
			else
				$DB->Commit();
		}

		return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';

    }

    public static function example()
    {
        global $DB;
        if (\Bitrix\Main\Loader::includeModule('iblock')) {
            $iblockId = \Only\Site\Helpers\IBlock::getIblockID('QUARRIES_SEARCH', 'SYSTEM');
            $format = $DB->DateFormatToPHP(\CLang::GetDateFormat('SHORT'));
            $rsLogs = \CIBlockElement::GetList(['TIMESTAMP_X' => 'ASC'], [
                'IBLOCK_ID' => $iblockId,
                '<TIMESTAMP_X' => date($format, strtotime('-1 months')),
            ], false, false, ['ID', 'IBLOCK_ID']);
            while ($arLog = $rsLogs->Fetch()) {
                \CIBlockElement::Delete($arLog['ID']);
            }
        }
        return '\\' . __CLASS__ . '::' . __FUNCTION__ . '();';
    }
}
