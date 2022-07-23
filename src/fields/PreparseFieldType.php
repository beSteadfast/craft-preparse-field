<?php
/**
 * Preparse Field plugin for Craft CMS 3.x
 *
 * @link      https://www.steadfastdesignfirm.com/
 * @copyright Copyright (c) Steadfast Design Firm
 */

namespace besteadfast\preparsefield\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use craft\base\SortableFieldInterface;
use craft\db\mysql\Schema;
use craft\helpers\Db;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;

/**
 *  Preparse field type
 *
 * @author    Steadfast Design Firm
 * @package   PreparseField
 * @since     1.0.0
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

    public function rules(): array
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
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

        return $rules;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getContentColumnType(): array|string
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
    public function getSettingsHtml(): ?string
    {
        $columns = [
            Schema::TYPE_TEXT => Craft::t('preparse-field', 'Text (stores about 64K)'),
            Schema::TYPE_MEDIUMTEXT => Craft::t('preparse-field', 'Mediumtext (stores about 16MB)'),
            Schema::TYPE_INTEGER => Craft::t('preparse-field', 'Number (integer)'),
            Schema::TYPE_DECIMAL => Craft::t('preparse-field', 'Number (decimal)'),
            Schema::TYPE_FLOAT => Craft::t('preparse-field', 'Number (float)'),
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
    public function getInputHtml(mixed $value, ?\craft\base\ElementInterface $element = null): string
    {
        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'preparse-field/_components/fields/_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
            ]
        );
    }
}

class_alias(PreparseFieldType::class, \aelvan\preparsefield\fields\PreparseFieldType::class);
