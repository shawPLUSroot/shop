<?php


namespace Shawplusroot\Shop\Controllers;

use Flarum\Api\Controller\AbstractCreateController;
use FoF\Doorman\Api\Serializers\DoorkeySerializer;
use Shawplusroot\Shop\Commands\CreateCustomDoorkey;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class CreateCustomDoorkeyController extends AbstractCreateController
{
    public $serializer = DoorkeySerializer::class;

    /**
     * @var Dispatcher
     */
    protected $bus;

    /**
     * @param Dispatcher $bus
     */
    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Document               $document
     *
     * @return mixed
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        return $this->bus->dispatch(
            new CreateCustomDoorkey($request->getAttribute('actor'), Arr::get($request->getParsedBody(), 'data', []))
        );
    }
}
