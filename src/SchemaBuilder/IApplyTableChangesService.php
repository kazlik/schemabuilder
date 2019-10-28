<?php
/**
 * Author: Marek Dočekal
 * Licence: WTFPL v2
 */

namespace Kazlik\SchemaBuilder;


interface IApplyTableChangesService
{

	public function applyAllChanges(): bool;
}