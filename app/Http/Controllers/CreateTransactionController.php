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

class CreateTransactionController extends Controller
{
    private TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $fields = $request->all();
            $transaction = $this->transactionRepository->save(
                new Cpf($fields['payer']),
                new Cpf($fields['payee']),
                $fields['value']
            );

            return response()->json($transaction, Response::HTTP_CREATED);
        } catch (InsufficientFundsException | InvalidCpfException | UserNotAuthorizedException $e) {
            return response()->json([
                'status_code' => 400,
                'message' => $e->getMessage(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status_code' => 500,
                'message' => 'Internal server error!',
                'error' => $e,
            ]);
        }
    }
}
