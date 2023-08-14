<?php

namespace Shawplusroot\Shop\Commands;


use Flarum\User\Exception\PermissionDeniedException;
use FoF\Doorman\Doorkey;
use FoF\Doorman\Validators\DoorkeyValidator;
use Illuminate\Support\Arr;
use Shawplusroot\Shop\Commands\CreateCustomDoorkey;
use Shawplusroot\Shop\Record;
use Laminas\Diactoros\Response\JsonResponse;

class CreateCustomDoorkeyHandler
{
    /**
     * @var DoorkeyValidator
     */
    protected $validator;

    /**
     * @param DoorkeyValidator $validator
     */
    public function __construct(DoorkeyValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param CreateDoorkey $command
     *
     * @throws PermissionDeniedException
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return Doorkey
     */
    public function handle(CreateCustomDoorkey $command)
    {
        // Validate User Indentity
        $actor = $command->actor;
        $actor->assertRegistered();

        // Extract invite code value requirements
        $data = $command->data;
        $code = Arr::get($data, 'attributes.key');
        $group_id = Arr::get($data, 'attributes.groupId');
        $max_uses = Arr::get($data, 'attributes.maxUses');

        // Check if the user has finished the purchase operation
        // but not received the code yet
        $record = Record::where('user_id', $actor->id)
            ->where('just_purchased', false)
            ->where('amount', $max_uses)
            ->firstOrFail();

        // If there exists such, assign a code
        $record->just_purchased = true;
        $record->invite_code = $code;
        $record->save();


        $doorkey = Doorkey::build(
            $code,
            $group_id,
            Arr::get($data, 'attributes.maxUses'),
            Arr::get($data, 'attributes.activates')
        );

        $this->validator->assertValid($doorkey->getAttributes());

        $doorkey->save();
        return $doorkey;
    }
}
