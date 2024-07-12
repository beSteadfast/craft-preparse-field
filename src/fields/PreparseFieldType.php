<?php
/**
 * Preparse Field plugin for Craft CMS 3.x
 */

namespace jalendport\preparse\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use craft\base\SortableFieldInterface;
use craft\db\mysql\Schema;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use craft\gql\types\DateTime as DateTimeType;
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
use craft\i18n\Locale;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;

/**
 *  Preparse field type
 *
 * @property string $contentColumnType
 * @property null|string $settingsHtml
 */
class PreparseFieldType extends Field implements PreviewableFieldInterface, SortableFieldInterface
{
    // Public Properties
    // =========================================================================

    /**
     * Some attribute
     *
     * @var string
     */
    public $fieldTwig = '';
    public $displayType = 'hidden';
    public $showField = false;
    public $columnType = Schema::TYPE_TEXT;
    public $decimals = 0;
    public $textareaRows = 5;
    public $parseBeforeSave = false;
    public $parseOnMove = false;
    public $allowSelect = false;

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('preparse-field', 'Preparse Field');
    }

    // Public Methods
    // =========================================================================

    public function rules()
    {
        $rules = parent::rules();
		return array_merge($rules, [
			['fieldTwig', 'string'],
			['fieldTwig', 'default', 'value' => ''],
			['columnType', 'string'],
			['columnType', 'default', 'value' => ''],
			['decimals', 'number'],
			['decimals', 'default', 'value' => 0],
			['textareaRows', 'number'],
			['textareaRows', 'default', 'value' => 5],
			['parseBeforeSave', 'boolean'],
			['parseBeforeSave', 'default', 'value' => false],
			['parseOnMove', 'boolean'],
			['parseOnMove', 'default', 'value' => false],
			['displayType', 'string'],
			['displayType', 'default', 'value' => 'hidden'],
			['allowSelect', 'boolean'],
			['allowSelect', 'default', 'value' => false],
		]);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getContentColumnType(): string
    {
        if ($this->columnType === Schema::TYPE_DECIMAL) {
            return Db::getNumericalColumnType(null, null, $this->decimals);
        }

        return $this->columnType;
    }

	/**
	 * @return null|string
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
    public function getSettingsHtml()
    {
        $columns = [
            Schema::TYPE_TEXT => Craft::t('preparse-field', 'Text (stores about 64K)'),
            Schema::TYPE_MEDIUMTEXT => Craft::t('preparse-field', 'Mediumtext (stores about 16MB)'),
            Schema::TYPE_INTEGER => Craft::t('preparse-field', 'Number (integer)'),
            Schema::TYPE_DECIMAL => Craft::t('preparse-field', 'Number (decimal)'),
            Schema::TYPE_FLOAT => Craft::t('preparse-field', 'Number (float)'),
            Schema::TYPE_DATETIME => Craft::t('preparse-field', 'Date (datetime)'),
        ];

        $displayTypes = [
            'hidden' => 'Hidden',
            'textinput' => 'Text input',
            'textarea' => 'Textarea',
        ];

        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'preparse-field/_components/fields/_settings',
            [
                'field' => $this,
                'columns' => $columns,
                'displayTypes' => $displayTypes,
                'existing' => $this->id !== null,
            ]
        );
    }

	/**
	 * @param mixed $value
	 * @param ElementInterface|null $element
	 *
	 * @return string
	 * @throws LoaderError
	 * @throws RuntimeError
	 * @throws SyntaxError
	 */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Render the input template
        $displayType = $this->displayType;
        if ($displayType !== 'hidden' && $this->columnType === Schema::TYPE_DATETIME) {
            $displayType = 'date';
        }
        return Craft::$app->getView()->renderTemplate(
            'preparse-field/_components/fields/_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
                'displayType' => $displayType,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getSearchKeywords($value, ElementInterface $element): string
    {
        if ($this->columnType === Schema::TYPE_DATETIME) {
            return '';
        }
        return parent::getSearchKeywords($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function getTableAttributeHtml($value, ElementInterface $element): string
    {
        if (!$value) {
            return '';
        }

        if ($this->columnType === Schema::TYPE_DATETIME) {
            return Craft::$app->getFormatter()->asDatetime($value, Locale::LENGTH_SHORT);
        }

        return parent::getTableAttributeHtml($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if ($this->columnType === Schema::TYPE_DATETIME) {
            if ($value && ($date = DateTimeHelper::toDateTime($value)) !== false) {
                return $date;
            }
            return null;
        }
        return parent::normalizeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function modifyElementsQuery(ElementQueryInterface $query, $value)
    {
        if ($this->columnType === Schema::TYPE_DATETIME) {
            if ($value !== null) {
                /** @var ElementQuery $query */
                $query->subQuery->andWhere(Db::parseDateParam('content.' . Craft::$app->getContent()->fieldColumnPrefix . $this->handle, $value));
            }
            return null;
        }
        return parent::modifyElementsQuery($query, $value);
    }

    /**
     * @inheritdoc
     */
    public function getContentGqlType()
    {
        if ($this->columnType === Schema::TYPE_DATETIME) {
            return DateTimeType::getType();
        }
        return parent::getContentGqlType();
    }
}

class_alias(PreparseFieldType::class, \aelvan\preparsefield\fields\PreparseFieldType::class);
class_alias(PreparseFieldType::class, \besteadfast\preparsefield\fields\PreparseFieldType::class);
