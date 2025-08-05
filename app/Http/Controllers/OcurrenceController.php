<?php

namespace App\Http\Controllers;

use App\Http\Requests\OcurrenceStoreRequets;
use App\Models\Ocurrence;
use Clickbar\Magellan\Data\Geometries\Point;
use Illuminate\Http\JsonResponse;

class OcurrenceController extends Controller
{
    /**
     * @group Ocorrências
     * Listar todas as ocorrências
     *
     * Este endpoint retorna uma lista de todas as ocorrências registradas.
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
        $ocurrences = Ocurrence::all();

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
    public function store(OcurrenceStoreRequets $request)
    {
        $ocurrenceStore = $request->getDto();

        $locationFormated = Point::makeGeodetic($ocurrenceStore->location->coordinates[0], $ocurrenceStore->location->coordinates[1]);

        $data = $ocurrenceStore->toArray();

        $data['user_id'] = $request->user()->id;
        $data['location'] = $locationFormated;

        $ocurrence = Ocurrence::create($data);

        return response()->json(['ocurrence' => $ocurrence], 201);
    }

    /**
     * @group Ocorrências
     * Deletar ocorrência
     *
     * Este endpoint permite que um usuário autenticado exclua uma ocorrência existente.
     *
     * @authenticated
     *
     * @urlParam ocurrence int required ID da ocorrência a ser deletada. Example: 1
     *
     * @response 204 ""
     */
    public function destroy(Ocurrence $ocurrence): JsonResponse
    {
        if ($ocurrence->user_id === auth()->user()->id) {
            $ocurrence->delete();

            return response()->json(status: 204);

        }
        return response()->json(["message" => "Você não tem permissão para deletar esta ocorrência."], status: 403);

    }
}
