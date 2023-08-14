<?php

/*
 * This file is part of shawplusroot/shop.
 *
 * Copyright (c) 2023 shawplusroot.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Shawplusroot\\Shop;

use Flarum\Extend;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__ . '/js/dist/forum.js')
        ->css(__DIR__ . '/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js')
        ->css(__DIR__ . '/less/admin.less'),
    new Extend\Locales(__DIR__ . '/locale'),

    (new Extend\Routes('api'))
        ->post('/purchase', 'purchase.create', Controllers\PurchaseController::class)
        ->post('/getcustomcode', 'getcustomcode.create', Controllers\CreateCustomDoorkeyController::class)
        ->get('/users/{id}/purchase', 'purchase.history', Controllers\ListCustomCodeController::class)
];
