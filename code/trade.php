<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
if (!$USER->IsAdmin()) {
LocalRedirect('/');
}

\Bitrix\Main\Loader::includeModule('iblock');
$row = 1;
$IBLOCK_ID = 10;

$el = new CIBlockElement;
$arProps = [];

$rsProp = CIBlockPropertyEnum::GetList(
    ["SORT" => "ASC", "VALUE" => "ASC"],
    ['IBLOCK_ID' => $IBLOCK_ID]
);
while ($arProp = $rsProp->Fetch()) {
    $key = trim($arProp['VALUE']);
    $arProps[$arProp['PROPERTY_CODE']][$key] = $arProp['ID'];
}

$rsElements = CIBlockElement::GetList([], ['IBLOCK_ID' => $IBLOCK_ID], false, false, ['ID']);
while ($element = $rsElements->GetNext()) {
    CIBlockElement::Delete($element['ID']);
}

function stripQuotes($text) {
  $unquoted = preg_replace('/^(\'(.*)\'|"(.*)")$/', '$2$3', $text);
  return $unquoted;
}

if (($handle = fopen("trades.csv", "r")) !== false) {
    while (($data = fgetcsv($handle, 1000, ";")) !== false) {
        if ($row == 1) {
            $row++;
            continue;
        }

        $row++;

        $PROP['ORGANIZATION'] = $data[1];
        $PROP['OFFICE'] = $data[2];
        $PROP['PERSON'] = $data[3];
        $PROP['TYPE_TRADE'] = $data[5];
        $PROP['BEGIN_DATE'] = $data[7];
        $PROP['END_DATE'] = $data[8];
        $PROP['LOT_NUMBER'] = $data[9];
        $PROP['STATUS'] = $data[10];
        $PROP['POSITION_NUMBER'] = $data[11];
        $PROP['POSITION_NAME'] = $data[12];
        $PROP['CURRENCY'] = $data[13];
        $PROP['TOTAL_SUM'] = $data[14];
        $PROP['EFFECT'] = $data[15];
        $PROP['EFFECTIVITY'] = $data[16];
        $PROP['NUMBER_OF_BIDDERS'] = $data[17];
        $PROP['NUMBER_OF_BIDS'] = $data[18];
        $PROP['WINNER_AND_URL'] = $data[19];
        $PROP['CLOSING_DATE'] = $data[20];

        foreach ($PROP as $key => &$value) {
            $value = trim($value);
            $value = str_replace('\n', '', $value);
            $value = str_replace(['«', '»'], '"', $value);
            if ($arProps[$key]) {
                $arSimilar = [];
                foreach ($arProps[$key] as $propKey => $propVal) {
                    similar_text($propKey, $value, $similar);
                    if ($similar > 75) {
                        $arSimilar[$similar] = $propVal;
                    }
                }
                ksort($arSimilar);
                $value = array_pop($arSimilar);
            }
        }

        $arTradeNumberUrl = trim($data[4]);
        $arTradeNumberUrl = str_replace('=ГИПЕРССЫЛКА', '', $arTradeNumberUrl);
        $arTradeNumberUrl = substr($arTradeNumberUrl, 1, -1);
        $arTradeNumberUrl = explode(';', $arTradeNumberUrl);
        $PROP['TRADE_URL'] = stripQuotes($arTradeNumberUrl[0]);
        $PROP['TRADE_NUMBER'] = stripQuotes($arTradeNumberUrl[1]);

        $arWinnerAndUrl = trim($data[19]);
        $arWinnerAndUrl = str_replace('=ГИПЕРССЫЛКА', '', $arWinnerAndUrl);
        $arWinnerAndUrl = substr($arWinnerAndUrl, 1, -1);
        $arWinnerAndUrl = explode(';', $arWinnerAndUrl);
        $PROP['WINNER_URL'] = stripQuotes($arWinnerAndUrl[0]);
        $PROP['WINNER'] = stripQuotes($arWinnerAndUrl[1]);

        $arLoadProductArray = [
            'MODIFIED_BY' => $USER->GetID(),
            'IBLOCK_SECTION_ID' => false,
            'IBLOCK_ID' => $IBLOCK_ID,
            'PROPERTY_VALUES' => $PROP,
            'NAME' => $data[6],
            'ACTIVE' => $data[20] ? 'N' : 'Y',
        ];

        if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
            echo 'Добавлен элемент с ID : ' . $PRODUCT_ID . '<br>';
        } else {
            echo 'Error: ' . $el->LAST_ERROR . '<br>';
        }

    }
    fclose($handle);
}