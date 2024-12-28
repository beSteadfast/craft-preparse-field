<?php

namespace jalendport\preparse\migrations;

use Craft;
use craft\db\Migration;
use jalendport\preparse\fields\PreparseFieldType;
use yii\base\ErrorException;
use yii\base\Exception;

/**
 * m240711_230833_jalendport migration.
 */
class m240711_230833_jalendport extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
		$oldType = 'besteadfast\preparsefield\fields\PreparseFieldType';

        $this->update('{{%fields}}', ['type'=> PreparseFieldType::class], ['type' => $oldType]);

		// Don’t make the same config changes twice
		$projectConfig = Craft::$app->getProjectConfig();
		$schemaVersion = $projectConfig->get('plugins.preparse-field.schemaVersion', true);

		if (version_compare($schemaVersion, '1.1.0', '>='))
		{
			return true;
		}

		$fields = $projectConfig->get('fields') ?? [];

		foreach ($fields as $fieldUid => $field)
		{
			if ($field['type'] === $oldType)
			{
				$field['type'] = PreparseFieldType::class;
				try {
					$projectConfig->set("fields.{$fieldUid}", $field);
				} catch (Exception|ErrorException $e) {
					return false;
				}
			}
		}

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m240711_230833_jalendport cannot be reverted.\n";
        return false;
    }
}
