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
            $this->repository->save(
                $payer->getRegisterNumber(),
                $payee->getRegisterNumber(),
                $fields['value']
            );

            return response()->json(['message' => 'Transaction successful!'], Response::HTTP_CREATED);
        } catch (InsufficientFundsException |
            InvalidCpfException |
            InvalidCnpjException |
            UserNotAuthorizedException |
            ValidationException $e
        ) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            dump($e);
            return response()->json([
                'message' => 'Internal server error!',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
