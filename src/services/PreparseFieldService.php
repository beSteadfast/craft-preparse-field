<?php
/**
 * Preparse Field plugin for Craft CMS 4.x
 */

namespace jalendport\preparse\services;

use jalendport\preparse\fields\PreparseFieldType;
use Craft;
use craft\base\Component;
use craft\base\Element;
use craft\helpers\DateTimeHelper;
use craft\web\View;
use craft\db\mysql\Schema;
use DateTime;
use yii\base\Exception;

/**
 * PreparseFieldService Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 */
class PreparseFieldService extends Component
{
	/**
	 * Loops over fields in element to determine if they have preparse fields.
	 *
	 * @param Element $element
	 * @param string $eventHandle
	 *
	 * @return array
	 * @throws Exception
	 */
    public function getPreparseFieldsContent(Element $element, string $eventHandle): array
	{
        $content = [];
        $fieldLayout = $element->getFieldLayout();

        if ($fieldLayout) {
            foreach ($fieldLayout->getCustomFields() as $field) {
                if ($field instanceof PreparseFieldType) {
                    /** @var PreparseFieldType $field */

                    // only get field content for the right event listener
                    $isBeforeSave = ($eventHandle === 'onBeforeSave');
                    $parseBeforeSave = (bool)$field->parseBeforeSave;

                    if ($isBeforeSave === $parseBeforeSave) {
                        $fieldValue = $this->parseField($field, $element);

                        if ($fieldValue !== null) {
                            $content[$field->handle] = $fieldValue;
                        }
                    }
                }
            }
        }

        return $content;
    }

	/**
	 * Parses field for a given element.
	 *
	 * @param PreparseFieldType $field
	 * @param Element $element
	 *
	 * @return null|string|DateTime
	 * @throws Exception
	 */
    public function parseField(PreparseFieldType $field, Element $element): DateTime|string|null
	{
        $fieldTwig = $field->fieldTwig;
        $columnType = $field->columnType;
        $decimals = $field->decimals;
        $fieldValue = null;

        $elementTemplateName = 'element';

        if (method_exists($element, 'refHandle')) {
            $elementTemplateName = strtolower($element->refHandle());
        }

        // Enable generateTransformsBeforePageLoad always
        $generateTransformsBeforePageLoad = Craft::$app->config->general->generateTransformsBeforePageLoad;
        Craft::$app->config->general->generateTransformsBeforePageLoad = true;

        // save cp template path and set to site templates
        $oldMode = Craft::$app->view->getTemplateMode();
        Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_SITE);

        // render value from the field template
        try {
            $vars = array_merge(['element' => $element], [$elementTemplateName => $element]);
            $fieldValue = Craft::$app->view->renderString($fieldTwig, $vars);
        } catch (\Exception $e) {
            Craft::error('Couldn’t render value for element with id “'.$element->id.'” and preparse field “'.
                $field->handle.'” ('.$e->getMessage().').', __METHOD__);
        }

        // restore cp template paths
        Craft::$app->view->setTemplateMode($oldMode);

        // set generateTransformsBeforePageLoad back to whatever it was
        Craft::$app->config->general->generateTransformsBeforePageLoad = $generateTransformsBeforePageLoad;

        if (null === $fieldValue) {
            return null;
        }

        if ($columnType === Schema::TYPE_FLOAT || $columnType === Schema::TYPE_INTEGER) {
            if ($decimals > 0) {
                return number_format(trim($fieldValue), $decimals, '.', '');
            }

            return number_format(trim($fieldValue), 0, '.', '');
        } else if ($columnType === Schema::TYPE_DATETIME) {
            $fieldValue = \trim($fieldValue);
            if (!$fieldValue || !$date = DateTimeHelper::toDateTime($fieldValue, true)) {
                // Return an empty string rather than null to clear out existing DateTime value (null would mean "no change")
                return '';
            }
            return $date;
        }

        return $fieldValue;
    }

	/**
	 * Checks to see if an element has a preparse field that should be saved on move
	 *
	 * @param Element $element
	 *
	 * @return bool
	 */
    public function shouldParseElementOnMove(Element $element): bool
    {
        $fieldLayout = $element->getFieldLayout();

        if ($fieldLayout) {
            foreach ($fieldLayout->getCustomFields() as $field) {
                if ($field instanceof PreparseFieldType) {
					$parseOnMove = $field->parseOnMove;

                    if ($parseOnMove) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}

class_alias(PreparseFieldService::class, \aelvan\preparsefield\services\PreparseFieldService::class);
class_alias(PreparseFieldService::class, \besteadfast\preparsefield\services\PreparseFieldService::class);
