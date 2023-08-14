<?php

namespace Shawplusroot\Shop\Controllers;

use AntoineFr\Money\Event\MoneyUpdated;
use Flarum\Http\RequestUtil;
use Flarum\User\User;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Shawplusroot\Shop\Command\CreateCustomDoorkeyHandler;
use Shawplusroot\Shop\Record;
use FoF\Doorman\Validators\DoorkeyValidator;


class PurchaseController implements RequestHandlerInterface
{
    protected $validation;
    protected $dispatcher;

    public function __construct(Factory $validation, Dispatcher $dispatcher)
    {
        $this->validation = $validation;
        $this->dispatcher = $dispatcher;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertRegistered();
        $amount = intval(Arr::get($request->getParsedBody(), 'amount'));
        $this->validation->make(compact('amount', 'message'), [
            'amount' => 'required|numeric|min:1',
        ])->validate();

        $unitPrice = 25; // 单价

        $query = User::query();
        $record = Record::where('user_id', $actor->id)
            ->where('just_purchased', false)
            ->where('amount', $amount)
            ->first();

        if ($record === null) {

            if (!Arr::get($request->getParsedBody(), 'dryRun')) {
                $record = new Record();
                $record->amount = $amount;
                $record->just_purchased = false;
                $record->user()->associate($actor);

                if ($amount * $unitPrice > $actor->money) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Insufficient funds',
                    ]);
                }

                $totalCost = $amount * $unitPrice; // 总花费

                $query->each(
                    function (User $user) use ($totalCost) {

                        $this->dispatcher->dispatch(new MoneyUpdated($user));
                        $user->money -= $totalCost;
                        $user->save();
                    }
                );
                $record->save();
            }
        }

        return new JsonResponse([
            'success' => true,
            'cost_money' => $amount * $unitPrice,
        ]);
    }
}
