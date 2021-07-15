<?php

if (\Bitrix\Main\Loader::includeModule('dev.site'))
{
	AddEventHandler("iblock", "OnAfterIBlockElementAdd", Array("\\Dev\\Site\\Handlers\\Iblock", "AddLog"));
	AddEventHandler("iblock", "OnAfterIBlockElementUpdate",  Array("\\Dev\\Site\\Handlers\\Iblock", "AddLog"));
}




