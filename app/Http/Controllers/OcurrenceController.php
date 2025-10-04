<?php

namespace App\Http\Controllers;

use App\Http\Requests\InactiveOcurrenceRequest;
use App\Http\Requests\OcurrenceStoreRequets;
use App\Models\Ocurrence;
use App\Services\OcurrenceService;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Request;

class OcurrenceController extends Controller
{
    public function __construct(private OcurrenceService $ocurrenceService) {}

    /**
     * @group Ocorrências
     * Listar todas as ocorrências ativas
     *
     * Este endpoint retorna uma lista de todas as ocorrências ativas registradas.
     *
     * @response 200 {
     *   "ocurrences": [
     *     {
     *       "id": 1,
     *       "type_id": 2,
     *       "user_id": 8,
     *       "description": "Buraco na rua que está dificultando o tráfego",
     *       "location": {
     *         "type": "Point",
     *         "coordinates": [-15.7801, -47.9292]
     *       },
     *       "address_name": "Rua das Palmeiras, 123",
     *       "city": "Belém",
     *       "state": "PA",
     *       "country": "Brasil",
     *       "created_at": "2025-07-10T20:30:00.000000Z",
     *       "updated_at": "2025-07-10T20:30:00.000000Z"
     *     }
     *   ]
     * }
     */
    public function index(): JsonResponse
    {
        $ocurrences = Ocurrence::where(['is_active' => true])->get();

        return response()->json([
            'ocurrences' => $ocurrences,
        ]);
    }

    /**
     * @group Ocorrências
     * Registrar nova ocorrência
     *
     * Este endpoint permite que usuários autenticados registrem uma nova ocorrência no sistema.
     *
     * @authenticated
     *
     * @response 201 {
     *   "ocurrence": {
     *     "id": 1,
     *     "type_id": 2,
     *     "user_id": 8,
     *     "description": "Buraco na rua dificultando o tráfego",
     *     "location": {
     *       "type": "Point",
     *       "coordinates": [-15.7801, -47.9292]
     *     },
     *     "address_name": "Rua das Palmeiras, 123",
     *     "city": "Belém",
     *     "state": "PA",
     *     "country": "Brasil",
     *     "is_active": true,
     *     "created_at": "2025-07-10T20:30:00.000000Z",
     *     "updated_at": "2025-07-10T20:30:00.000000Z"
     *   }
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "type_id": ["O tipo informado não existe."],
     *     "location": ["O campo location deve ser um ponto GeoJSON válido."]
     *   }
     * }
     */
    public function store(OcurrenceStoreRequets $request): JsonResponse
    {
        $ocurrenceStore = $request->getDto();

        $ocurrence = $this->ocurrenceService->create($ocurrenceStore, $request->user()->id);

        return response()->json(['ocurrence' => $ocurrence], 201);
    }

    /**
     * @group Ocorrências
     * Inativar ocorrência
     *
     * Este endpoint permite que um usuário autenticado inative uma ocorrência que ele mesmo registrou.
     * A ocorrência não é removida do banco, apenas marcada como inativa.
     *
     * @authenticated
     *
     * @urlParam ocurrence int required O ID da ocorrência que será inativada. Exemplo: 1
     *
     * @bodyParam type_closure string required Tipo de encerramento da ocorrência.
     * Valores aceitos:
     * - resolved → Ocorrência resolvida
     * - mistake → Ocorrência criada por engano
     * - other → Finalizada por outro motivo
     * Exemplo: "resolved"
     * @bodyParam solution_description string Condicional. Obrigatório se o type_closure for "resolved" ou "other".
     * Máximo de 500 caracteres.
     * Exemplo: "Problema resolvido pela companhia responsável."
     *
     * @response 200 {
     *   "message": "Ocorrência inativada com sucesso."
     * }
     * @response 403 {
     *   "message": "Você não tem permissão para inativar esta ocorrência."
     * }
     * @response 422 {
     *   "message": "Ocorrência já inativada."
     * }
     * @response 422 {
     *   "message": "Não foi possível inativar a ocorrência."
     * }
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "type_closure": ["O campo type_closure é obrigatório."],
     *     "solution_description": ["O campo solution_description é obrigatório quando type_closure é resolved."]
     *   }
     * }
     *
     * @example request
     * POST /api/ocurrences/1/inactivate
     * {
     *   "type_closure": "resolved",
     *   "solution_description": "Problema resolvido pela equipe de manutenção."
     * }
     */
    public function inactiveOcurrence(InactiveOcurrenceRequest $request, Ocurrence $ocurrence): JsonResponse
    {

        if ($ocurrence->is_active === false) {
            return response()->json(['message' => 'Ocorrência já inativada.'], 422);
        }

        if ($ocurrence->user_id === $request->user()->id) {
            try {
                $inactiveOcurrence = $request->getDto();
                $this->ocurrenceService->inactivate($ocurrence, $inactiveOcurrence);
            } catch (\Exception $e) {
                Log::error('Erro ao inativar ocorrência', ['exception' => $e]);

                return response()->json('Não foi possível inativar a ocorrência.', 422);
            }

            return response()->json(['message' => 'Ocorrência inativada com sucesso.']);

        }

        return response()->json(['message' => 'Você não tem permissão para inativar esta ocorrência.'], 403);

    }

    /**
     * @group Ocorrências
     * Listar ocorrências do usuário autenticado
     *
     * Este endpoint retorna todas as ocorrências registradas pelo usuário autenticado,
     * em ordem decrescente de criação. Os resultados são paginados.
     *
     * @authenticated
     *
     * @queryParam page int Opcional. Número da página de resultados a ser retornada. Exemplo: 2
     *
     * @response 200 {
     *   "current_page": 1,
     *   "data": [
     *     {
     *       "id": 1,
     *       "type_id": 2,
     *       "user_id": 8,
     *       "description": "Buraco na rua que está dificultando o tráfego",
     *       "location": {
     *         "type": "Point",
     *         "coordinates": [-15.7801, -47.9292]
     *       },
     *       "address_name": "Rua das Palmeiras, 123",
     *       "city": "Belém",
     *       "state": "PA",
     *       "country": "Brasil",
     *       "is_active": true,
     *       "created_at": "2025-07-10T20:30:00.000000Z",
     *       "updated_at": "2025-07-10T20:30:00.000000Z"
     *     }
     *   ],
     *   "first_page_url": "http://localhost/api/ocurrences/my-ocurrences?page=1",
     *   "from": 1,
     *   "last_page": 3,
     *   "last_page_url": "http://localhost/api/ocurrences/my-ocurrences?page=3",
     *   "links": [
     *     {"url": null, "label": "&laquo; Previous", "active": false},
     *     {"url": "http://localhost/api/ocurrences/my-ocurrences?page=1", "label": "1", "active": true},
     *     {"url": "http://localhost/api/ocurrences/my-ocurrences?page=2", "label": "2", "active": false},
     *     {"url": "http://localhost/api/ocurrences/my-ocurrences?page=3", "label": "3", "active": false},
     *     {"url": "http://localhost/api/ocurrences/my-ocurrences?page=2", "label": "Next &raquo;", "active": false}
     *   ],
     *   "next_page_url": "http://localhost/api/ocurrences/my-ocurrences?page=2",
     *   "path": "http://localhost/api/ocurrences/my-ocurrences",
     *   "per_page": 10,
     *   "prev_page_url": null,
     *   "to": 10,
     *   "total": 25
     * }
     */
    public function ocurrencesUserAuth(Request $request): JsonResponse
    {
        $ocurrences = Ocurrence::where(['user_id' => auth()->id()])->orderByDesc('created_at')->paginate(10);

        return response()->json($ocurrences);
    }
}
