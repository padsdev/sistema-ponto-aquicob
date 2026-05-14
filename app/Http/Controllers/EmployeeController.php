<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Employees', description: 'Cadastro de funcionários')]
class EmployeeController extends Controller
{
    #[OA\Get(
        path: '/api/employees',
        summary: 'Lista todos os funcionários',
        tags: ['Employees'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista retornada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/Employee')
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        $employees = Employee::query()->orderBy('name')->get();

        return EmployeeResource::collection($employees)->response();
    }

    #[OA\Post(
        path: '/api/employees',
        summary: 'Cadastra um funcionário',
        tags: ['Employees'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(ref: '#/components/schemas/StoreEmployeeBody')
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Funcionário criado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Employee'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Erro de validação',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'errors', type: 'object', additionalProperties: true),
                    ]
                )
            ),
        ]
    )]
    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = Employee::create($request->validated());

        return (new EmployeeResource($employee))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: '/api/employees/{employee}',
        summary: 'Obtém um funcionário pelo id',
        tags: ['Employees'],
        parameters: [
            new OA\Parameter(name: 'employee', in: 'path', required: true, description: 'ID do funcionário', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Employee'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Funcionário não encontrado'),
        ]
    )]
    public function show(Employee $employee): EmployeeResource
    {
        return new EmployeeResource($employee);
    }

    #[OA\Put(
        path: '/api/employees/{employee}',
        summary: 'Atualiza um funcionário',
        tags: ['Employees'],
        parameters: [
            new OA\Parameter(name: 'employee', in: 'path', required: true, description: 'ID do funcionário', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(ref: '#/components/schemas/UpdateEmployeeBody')
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Atualizado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Employee'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Funcionário não encontrado'),
            new OA\Response(
                response: 422,
                description: 'Erro de validação',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'errors', type: 'object', additionalProperties: true),
                    ]
                )
            ),
        ]
    )]
    public function update(UpdateEmployeeRequest $request, Employee $employee): EmployeeResource
    {
        $employee->update($request->validated());

        return new EmployeeResource($employee->fresh());
    }

    #[OA\Delete(
        path: '/api/employees/{employee}',
        summary: 'Remove um funcionário',
        tags: ['Employees'],
        parameters: [
            new OA\Parameter(name: 'employee', in: 'path', required: true, description: 'ID do funcionário', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Removido com sucesso'),
            new OA\Response(response: 404, description: 'Funcionário não encontrado'),
        ]
    )]
    public function destroy(Employee $employee): Response
    {
        $employee->delete();

        return response()->noContent();
    }
}
