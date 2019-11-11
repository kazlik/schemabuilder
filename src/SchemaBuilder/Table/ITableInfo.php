<?php
/**
 * Author: Marek Dočekal
 * Licence: WTFPL v2
 */

namespace Kazlik\SchemaBuilder\Table;




use Doctrine\DBAL\Schema\Table;

interface ITableInfo
{
    public function getTable(): Table;

    public function getForeignKeys(): array;
}