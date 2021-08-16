<?php

namespace App\Http\Controllers;

use Adapters\Repositories\TransactionRepository;
use Adapters\Repositories\UserRepository;
use Domain\Entities\User;
use Domain\Exceptions\InsufficientFundsException;
use Domain\Exceptions\InvalidCpfException;
use Domain\Exceptions\UserNotAuthorizedException;
use Domain\ValueObjects\Cpf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class CreateUserController extends Controller
{

    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {

        $this->repository = $repository;
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required',
                'cpf' => 'required|unique:users',
            ]);
            $fields = $request->all();
            $this->repository->save(
                $fields['name'],
                $fields['email'],
                $fields['password'],
                new Cpf($fields['cpf']),
                $fields['type'] || User::CUSTOMER,
                $fields['funds'] || 0
            );

            return response()->json([
                'message' => 'User registration successful!',
            ], Response::HTTP_CREATED);
        } catch (InvalidCpfException | ValidationException $e) {
            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => $e->validator->getMessageBag()
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal server error!',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
