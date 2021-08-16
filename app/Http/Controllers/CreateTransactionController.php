<?php

namespace App\Http\Controllers;

use Adapters\Repositories\TransactionRepository;
use Adapters\Repositories\UserRepository;
use Domain\Exceptions\InsufficientFundsException;
use Domain\Exceptions\InvalidCnpjException;
use Domain\Exceptions\InvalidCpfException;
use Domain\Exceptions\UserNotAuthorizedException;
use Domain\ValueObjects\Cpf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class CreateTransactionController extends Controller
{
    private TransactionRepository $repository;
    private UserRepository $userRepository;

    public function __construct(TransactionRepository $repository, UserRepository $userRepository)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'payer' => 'required',
                'payee' => 'required',
                'value' => 'required'
            ]);

            $fields = $request->all();
            $payer = $this->userRepository->get($fields['payer']);
            $payee = $this->userRepository->get($fields['payee']);

            if (is_null($payer) || is_null($payee)) {
                return response()->json(['message' => 'User not found!'], Response::HTTP_NOT_FOUND);
            }

            $this->repository->save(
                $payer->getRegisterNumber(),
                $payee->getRegisterNumber(),
                $fields['value']
            );

            return response()->json(['message' => 'Transaction successful!'], Response::HTTP_CREATED);
        } catch (InsufficientFundsException |
            UserNotAuthorizedException |
            ValidationException $e
        ) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal server error!',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
