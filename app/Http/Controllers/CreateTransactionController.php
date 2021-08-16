<?php

namespace App\Http\Controllers;

use Adapters\Repositories\TransactionRepository;
use Domain\Exceptions\InsufficientFundsException;
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

    public function __construct(TransactionRepository $repository)
    {
        $this->repository = $repository;
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
            $this->repository->save(
                new Cpf($fields['payer']),
                new Cpf($fields['payee']),
                $fields['value']
            );

            return response()->json(['message' => 'Transaction successful!'], Response::HTTP_CREATED);
        } catch (InsufficientFundsException |
            InvalidCpfException |
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
