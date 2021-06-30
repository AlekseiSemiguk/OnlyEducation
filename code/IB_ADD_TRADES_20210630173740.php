<?php

namespace Sprint\Migration;


class IB_ADD_TRADES_20210630173740 extends Version
{

    protected $description = "Добавляет миграцию для ИБ Закупки";

    public function up() {

        $helper = new HelperManager();

        $arIBlockType = array(
            'ID' => 'TRADE_RU',
            'SECTIONS' => 'Y',
            'IN_RSS' => 'N',
            'SORT' => 100,
            'LANG' => array(
                'ru' => array(
                    'NAME' => 'Торги',
                    'SECTION_NAME' => 'Разделы',
                    'ELEMENT_NAME' => 'Элементы'
                ),
                'en' => array(
                    'NAME' => 'Trades',
                    'SECTION_NAME' => 'Sections',
                    'ELEMENT_NAME' => 'Elements'
                ),
            )
        );


        $helper->Iblock()->addIblockTypeIfNotExists($arIBlockType);

        $iIBlockID = $helper->Iblock()->addIblockIfNotExists(array(
            'LID' => 's1',
            'IBLOCK_TYPE_ID' => 'TRADE_RU',
            'CODE' => 'TRADES',
            'NAME' => 'Закупки'
        ));
        $arProps = array(
            array(
                'NAME' => 'Организатор',
                'CODE' => 'ORGANIZATION',
                'PROPERTY_TYPE' => 'L',
                'LIST_TYPE' => 'L',
                'VALUES' => [
                    [
                        'XML_ID' => 'NNK',
                        'VALUE' => 'АО "ННК"'
                    ],
                    [
                        'XML_ID' => 'NNK-TUMEN',
                        'VALUE' => 'ООО "ННК-Тюмень"'
                    ],
                    [
                        'XML_ID' => 'NNK-IMUSHESTVO',
                        'VALUE' => 'ООО "ННК-Имущество"'
                    ]
                ]
            ),
            array(
                'NAME' => 'Подразделение',
                'CODE' => 'OFFICE',
                'PROPERTY_TYPE' => 'L',
                'LIST_TYPE' => 'L',
                'VALUES' => [
                    [
                        'XML_ID' => 'PAVLOVSK',
                        'VALUE' => 'Павловск Неруд'
                    ],
                    [
                        'XML_ID' => 'TUMEN',
                        'VALUE' => 'Тюмень Основной'
                    ],
                    [
                        'XML_ID' => 'TOMSK',
                        'VALUE' => 'Юрга Первый'
                    ]
                ]
            ),
            array(
                'NAME' => 'Тип процедуры',
                'CODE' => 'TYPE_TRADE',
                'PROPERTY_TYPE' => 'L',
                'LIST_TYPE' => 'L',
                'VALUES' => [
                    [
                        'XML_ID' => 'OFFERS',
                        'VALUE' => 'Запрос предложений'
                    ],
                    [
                        'XML_ID' => 'FREE_BUY',
                        'VALUE' => 'Свободная покупка'
                    ]
                ]
            ),
            array(
                'NAME' => 'Ответственное лицо',
                'CODE' => 'PERSON',
                'PROPERTY_TYPE' => 'S'
            ),
            array(
                'NAME' => 'Номер закупки',
                'CODE' => 'TRADE_NUMBER',
                'PROPERTY_TYPE' => 'S'
            ),
            array(
                'NAME' => 'Ссылка на закупку',
                'CODE' => 'TRADE_URL',
                'PROPERTY_TYPE' => 'S'
            ),
            array(
                'NAME' => 'Дата начала',
                'CODE' => 'BEGIN_DATE',
                'PROPERTY_TYPE' => 'S',
                'USER_TYPE' => 'Date'
            ),
            array(
                'NAME' => 'Дата завершения',
                'CODE' => 'END_DATE',
                'PROPERTY_TYPE' => 'S',
                'USER_TYPE' => 'Date'
            ),
            array(
                'NAME' => 'Номер лота',
                'CODE' => 'LOT_NUMBER',
                'PROPERTY_TYPE' => 'S'
            ),
            array(
                'NAME' => 'Статус процедуры',
                'CODE' => 'STATUS',
                'PROPERTY_TYPE' => 'L',
                'LIST_TYPE' => 'L',
                'VALUES' => [
                    [
                        'XML_ID' => 'END',
                        'VALUE' => 'Торги завершены, протокол разослан'
                    ],
                    [
                        'XML_ID' => 'PUBLIC',
                        'VALUE' => 'Торги объявлены'
                    ],
                    [
                        'XML_ID' => 'START',
                        'VALUE' => 'Идёт прием заявок'
                    ]
                  ]
            ),
            array(
                'NAME' => 'Номер закупочной позиции',
                'CODE' => 'POSITION_NUMBER',
                'PROPERTY_TYPE' => 'S'
            ),
            array(
                'NAME' => 'Наименование закупочной позиции',
                'CODE' => 'POSITION_NAME',
                'PROPERTY_TYPE' => 'S'
            ),
            array(
                'NAME' => 'Валюта',
                'CODE' => 'CURRENCY',
                'PROPERTY_TYPE' => 'L',
                'LIST_TYPE' => 'L',
                'VALUES' => [
                    [
                        'XML_ID' => 'RUB',
                        'VALUE' => 'руб.'
                    ],
                    [
                        'XML_ID' => 'USD',
                        'VALUE' => 'доллар США'
                    ]
                ]
            ),
            array(
                'NAME' => 'Итоговая сумма',
                'CODE' => 'TOTAL_SUM',
                'PROPERTY_TYPE' => 'S'
            ),
            array(
                'NAME' => 'Суммарный эффект',
                'CODE' => 'EFFECT',
                'PROPERTY_TYPE' => 'S'
            ),
            array(
                'NAME' => 'Эффективность',
                'CODE' => 'EFFECTIVITY',
                'PROPERTY_TYPE' => 'S'
            ),
            array(
                'NAME' => 'Количество участников',
                'CODE' => 'NUMBER_OF_BIDDERS',
                'PROPERTY_TYPE' => 'S'
            ),
            array(
                'NAME' => 'Количество предложений',
                'CODE' => 'NUMBER_OF_BIDS',
                'PROPERTY_TYPE' => 'S'
            ),
            array(
                'NAME' => 'Победитель',
                'CODE' => 'WINNER',
                'PROPERTY_TYPE' => 'S'
            ),
            array(
                'NAME' => 'Ссылка на победителя',
                'CODE' => 'WINNER_URL',
                'PROPERTY_TYPE' => 'S'
            ),
            array(
                'NAME' => 'Дата завершения процедуры',
                'CODE' => 'CLOSING_DATE',
                'PROPERTY_TYPE' => 'S'
            )              
        );
        if ($iIBlockID) {
            foreach ($arProps as $arProp) {
                $helper->Iblock()->addPropertyIfNotExists($iIBlockID, $arProp);
            }
            $helper->AdminIblock()->buildElementForm($iIBlockID, [
                'Закупка' => [
                    'ACTIVE',
                    'PROPERTY_ORGANIZATION',
                    'PROPERTY_OFFICE',
                    'NAME'=>'Предмет закупки',
                    'PROPERTY_PERSON',
                    'PROPERTY_TRADE_NUMBER',
                    'PROPERTY_TRADE_URL',
					'PROPERTY_TYPE_TRADE',
                    'PROPERTY_BEGIN_DATE',
                    'PROPERTY_END_DATE',
                    'PROPERTY_LOT_NUMBER',
                    'PROPERTY_STATUS',
                    'PROPERTY_POSITION_NUMBER',
                    'PROPERTY_POSITION_NAME'
                ],
                'Итоги закупки' => [
                    'PROPERTY_CURRENCY',
                    'PROPERTY_TOTAL_SUM',
                    'PROPERTY_EFFECT',
                    'PROPERTY_EFFECTIVITY',
                    'PROPERTY_NUMBER_OF_BIDDERS',
                    'PROPERTY_NUMBER_OF_BIDS',
                    'PROPERTY_WINNER',
                    'PROPERTY_WINNER_URL',
                    'PROPERTY_CLOSING_DATE'
            ]
            ]);
        }

    }

    public function down() {
        $helper = new HelperManager();

        $helper->Iblock()->deleteIblockIfExists('TRADES', 'TRADE_RU');

    }

}
