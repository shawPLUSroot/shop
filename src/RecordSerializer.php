<?php

namespace Shawplusroot\Shop;

use Flarum\Api\Serializer\AbstractSerializer;

class RecordSerializer extends AbstractSerializer
{
    protected $type = 'record';

    protected function getDefaultAttributes($model): array
    {
        return [];
    }
}
