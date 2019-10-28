<?php
/**
 * Author: Marek Dočekal
 * Licence: WTFPL v2
 */

namespace Kazlik\Schemabuilder;


interface IApplyTableChangesService
{

	public function applyAllChanges(): bool;
}