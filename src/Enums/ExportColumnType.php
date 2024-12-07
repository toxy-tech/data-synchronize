<?php

namespace ToxyTech\DataSynchronize\Enums;

use ToxyTech\Base\Supports\Enum;

class ExportColumnType extends Enum
{
    public const DROPDOWN = 'dropdown';

    public const TEXT = 'text';

    public const NUMBER = 'number';

    public const DATETIME = 'datetime';

    public const BOOLEAN = 'boolean';
}
