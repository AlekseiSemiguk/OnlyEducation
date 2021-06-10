<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<?= $arResult["FORM_HEADER"] ?>

<div class="contact-form">
    <div class="contact-form__head">
        <div class="contact-form__head-title"><?= $arResult["FORM_TITLE"] ?></div>
        <div class="contact-form__head-text"><?= $arResult["FORM_DESCRIPTION"] ?></div>
    </div>
    <div class="contact-form__form">
        <div class="contact-form__form-inputs">
            <? foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion) : ?>

                <? if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] != 'textarea') : ?>
                    <div class="input contact-form__input"><label class="input__label" for="<?= $FIELD_SID ?>">
                            <div class="input__label-text"><?= $arQuestion['CAPTION'] ?>
                                <? if ($arQuestion["REQUIRED"] == "Y") : ?>*<? endif; ?>
                            </div>
                            <input class="input__input" type="<?= $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] ?>" id="<?= $FIELD_SID ?>"
								 name="form_<?= $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] ?>_<?= $arQuestion['STRUCTURE'][0]['ID'] ?>"
								<? if ($arQuestion["REQUIRED"] == "Y") : ?>required<? endif; ?>  value>
                            <? if (is_array($arResult["FORM_ERRORS"]) && array_key_exists($FIELD_SID, $arResult['FORM_ERRORS'])) : ?>
                                <div class="input__notification"><?= $arResult["FORM_ERRORS"][$FIELD_SID] ?></div>
                            <? endif; ?>
					</label></div>
                <? endif; ?>

                <? if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'textarea') : ?>
                    <? $arTextarea[$FIELD_SID] = $arQuestion; ?>
                <? endif; ?>

            <? endforeach; ?>
        </div>
        <div class="contact-form__form-message">

            <? foreach ($arTextarea as $FIELD_SID => $arQuestion) : ?>
                <div class="input"><label class="input__label" for="<?= $FIELD_SID ?>">
                        <div class="input__label-text"><?= $arQuestion['CAPTION'] ?>
							<? if ($arQuestion["REQUIRED"] == "Y") : ?>*<? endif; ?>
						</div>
                        <textarea class="input__input" type="<?= $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] ?>" id="<?= $FIELD_SID ?>"
							 name="form_<?= $arQuestion['STRUCTURE'][0]['FIELD_TYPE'] ?>_<?= $arQuestion['STRUCTURE'][0]['ID'] ?>" value=""></textarea>
                        <? if (is_array($arResult["FORM_ERRORS"]) && array_key_exists($FIELD_SID, $arResult['FORM_ERRORS'])) : ?>
                            <div class="input__notification"><?= $arResult["FORM_ERRORS"][$FIELD_SID] ?></div>
                        <? endif; ?>
                    </label></div>
            <? endforeach; ?>

        </div>
        <div class="contact-form__bottom">
			<?if ($arParams['USER_CONSENT'] == 'Y'):?>
			<div class="contact-form__bottom-policy">
				<?$APPLICATION->IncludeComponent(
			  	"my_components:main.userconsent.request",
			  	"custom_template",
			  	array(
				  "ID" => $arParams["USER_CONSENT_ID"],
				  "IS_CHECKED" => $arParams["USER_CONSENT_IS_CHECKED"],
				  "AUTO_SAVE" => "Y",
				  "IS_LOADED" => $arParams["USER_CONSENT_IS_LOADED"],
				  "COMPONENT_TEMPLATE" => "custom_template",
			  	)
			 );?>
			</div>
			<?endif;?>
            <button class="form-button contact-form__bottom-button" data-success="Отправлено" data-error="Ошибка отправки" name="web_form_submit"
				value="<?= $arResult["arForm"]["BUTTON"]; ?>">

               	<div class="form-button__title"><?= $arResult["arForm"]["BUTTON"]; ?>
                </div>
            </button>
        </div>
    </div>
</div>

<?= $arResult["FORM_FOOTER"] ?>