<?php
/**
 * Salesforce Leads plugin for Craft CMS 3.x
 *
 * Generate Salesforce leads from form submissions.
 *
 * @link      https://github.com/lukeyouell
 * @copyright Copyright (c) 2018 Luke Youell
 */

namespace lukeyouell\salesforceleads\migrations;

use lukeyouell\salesforceleads\SalesforceLeads;

use Craft;
use craft\config\DbConfig;
use craft\db\Migration;
use craft\helpers\MigrationHelper;

class Install extends Migration
{
    // Public Properties
    // =========================================================================

    public $driver;

    // Public Methods
    // =========================================================================

    public function safeUp()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        if ($this->createTables()) {
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
        }

        return true;
    }

    public function safeDown()
    {
        $this->driver = Craft::$app->getConfig()->getDb()->driver;
        $this->dropTables();

        return true;
    }

    // Protected Methods
    // =========================================================================

    protected function createTables()
    {
        $tablesCreated = false;

        // support_tickets table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%sf_logs}}');
        if ($tableSchema === null) {
            $tablesCreated = true;

            $this->createTable(
                '{{%sf_logs}}',
                [
                    'id'          => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid'         => $this->uid(),
                    // Custom columns in the table
                    'status'      => $this->string(),
                    'details'     => $this->string(),
                ]
            );
        }

        return $tablesCreated;
    }

    protected function dropTables()
    {
        $this->dropTable('{{%sf_logs}}');
    }
}
