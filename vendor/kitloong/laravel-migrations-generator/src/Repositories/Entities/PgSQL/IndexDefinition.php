<?php

namespace KitLoong\MigrationsGenerator\Repositories\Entities\PgSQL;

class IndexDefinition
{
    private $tableName;
    private $indexName;
    private $indexDef;

    public function __construct(string $tableName, string $indexName, string $indexDef)
    {
        $this->tableName = $tableName;
        $this->indexName = $indexName;
        $this->indexDef  = $indexDef;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getIndexName(): string
    {
        return $this->indexName;
    }

    public function getIndexDef(): string
    {
        return $this->indexDef;
    }
}
