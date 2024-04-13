<?php

namespace App\UseCase;

use App\Interface\RepositoryInterface;
use App\Request\GetServersRequest;
use App\Response\ErrorResponse;
use App\Response\GetServersResponse;
use App\Response\Meta;
use App\Servers\ServerCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final readonly class GetServersUseCase
{

    public function __construct(
        protected ValidatorInterface  $validator,
        protected RepositoryInterface $serverRepository
    )
    {
    }

    /**
     * @param GetServersRequest $request
     * @return GetServersResponse|ErrorResponse
     * @throws Exception
     */
    public function handle(GetServersRequest $request): GetServersResponse|ErrorResponse
    {
        $errors = $this->validator->validate($request);
        if($errors->count() > 0) {
            return new ErrorResponse(
                'validation_failed',
                $this->formatValidationErrors($errors)
            );
        }
        extract($request->toArray());

        $start = ($page - 1) * $itemsPerPage;

        /** @var ServerCollection $allServers */
        $allServers = $this->serverRepository->all();

        $allServers = $allServers->applyFilters($filters);

        if (!empty($order)) {
            $order = array_slice($order, 0, 1);
            $allServers = $allServers->order(key($order), $order[key($order)]);
        }

        $servers = new ServerCollection($allServers->slice($start, $itemsPerPage));

        return new GetServersResponse(
            new Meta($page,
                $itemsPerPage,
                $servers->count(),
                $allServers->count()
            ), array_values($servers->toArray()));
    }

    /**
     * @param ConstraintViolationList $errors
     * @return array
     */
    private function formatValidationErrors(ConstraintViolationList $errors): array
    {
        /** @var ConstraintViolation $message */
        foreach ($errors as $message) {
            $formatted[] = [
                'property' => $message->getPropertyPath(),
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage(),
            ];
        }

        return $formatted ?? [];
    }
}