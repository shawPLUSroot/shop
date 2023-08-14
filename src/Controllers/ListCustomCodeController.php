<?php

namespace Shawplusroot\Shop\Controllers;

use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Shawplusroot\Shop\RecordSerializer;
use Shawplusroot\Shop\Record;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListCustomCodeController extends AbstractListController
{

    public $serializer = RecordSerializer::class;


    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);

        $actor->assertRegistered();

        $record = Record::where('user_id', $actor->id)
            ->where('just_purchased', true)
            ->whereNotNull('invite_code')
            ->get();

        return $record;
    }
}
