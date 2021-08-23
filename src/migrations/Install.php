<?php
/**
 * SsUserImportExport plugin for Craft CMS 3.x
 *
 *
 * @link      http://www.systemseeders.com/
 * @copyright Copyright (c) 2021 ssplugin
 */

namespace ssplugin\ssuserimportexport\migrations;

use ssplugin\ssuserimportexport\SsUserImportExport;
use Craft;
use craft\db\Migration;
use craft\config\DbConfig;
use craft\helpers\MigrationHelper;

/**
 * Install migration.
 */
class Install extends Migration
{

    /**
     * @var string The database driver to use
     */
    public $driver;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (!$this->db->tableExists('{{%ssuserimportdata}}')) {
            $this->createTable('{{%ssuserimportdata}}', [
                'id' => $this->primaryKey(),
                'response_header' => $this->text()->notNull(),
                'response_data' => $this->longText()->notNull(),
                'lastUpFile' => $this->text()->notNull(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);

            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
        }
        return true;
    }

    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    public function safeDown()
    {
        $this->dropTableIfExists('{{%ssuserimportdata}}');
    }
}
