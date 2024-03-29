<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use App\Models\User;
use App\Repositories\OrganizationRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class OrganizationController extends Controller
{
    private OrganizationRepository $organizationRepository;

    use AuthorizesRequests;

    public function __construct(OrganizationRepository $organizationRepository)
    {
        $this->organizationRepository = $organizationRepository;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/organizations",
     *      operationId="getOrganizationsList",
     *      tags={"Organizations"},
     *      summary="Get list of organizationss",
     *      description="Returns a list of organizations. You must be authenticated and you must have the <strong>SUPERADMIN</strong> role.",
     *
     * @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by the status of the organization",
     *         required=false,
     *
     *         @OA\Schema(
     *             type="string",
     *             enum={"ACTIVE","INACTIVE"},
     *         )
     *     ),
     *
     *      @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter by the name of the organization",
     *         required=false,
     *
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/OrganizationResource")
     *       ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Organization::class);
        $organizations = $this->organizationRepository->getOrganizations($request->all());

        return OrganizationResource::collection($organizations);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/organizations",
     *      operationId="storeOrganization",
     *      tags={"Organizations"},
     *      summary="Store new organization",
     *      description="Store a new organization. You must be authenticated and have the <strong>SUPERADMIN</strong> role.",
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/StoreOrganizationRequest")
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Organization")
     *       ),
     *
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function store(StoreOrganizationRequest $request): OrganizationResource|JsonResponse
    {
        // see the authorization in the storeOrganizationRequest
        // $this->authorize('create', Organization::class);

        try {
            /** @var User $user */
            $user = $request->user();

            $organization = $this->organizationRepository->insert($request->all());

            return new OrganizationResource($organization);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 422);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/organizations/{id}",
     *      operationId="getOrganizationById",
     *      tags={"Organizations"},
     *      summary="Get organization information",
     *      description="Returns organization data",
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Organization id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(type="string",format="uuid")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Organization")
     *       ),
     *
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     * )
     */
    public function show(Organization $organization): OrganizationResource
    {
        $this->authorize('view', $organization);

        return new OrganizationResource($organization);
    }

    /**
     * @OA\Put(
     *      path="/api/v1/organizations/{id}",
     *      operationId="updateOrganization",
     *      tags={"Organizations"},
     *      summary="Update existing organization",
     *      description="Returns updated organization data",
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Organization id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(
     *              type="string", format="uuid"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/StoreOrganizationRequest")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/Organization")
     *       ),
     *
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function update(StoreOrganizationRequest $request, Organization $organization): OrganizationResource|JsonResponse
    {
        // see the authorization in the storeOrganizationRequest
        // $this->authorize('update', $organization);

        try {
            $organization = $this->organizationRepository->update($organization, $request->all());

            return new OrganizationResource($organization);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 422);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/organizations/{id}",
     *      operationId="deleteOrganization",
     *      tags={"Organizations"},
     *      summary="Delete existing organization",
     *      description="Deletes a record and returns no content. Be careful, the deletion deletes also all the childs by cascade (users, notes...)",
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Organization id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(
     *              type="string", format="uuid"
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *
     *          @OA\JsonContent()
     *       ),
     *
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function destroy(Organization $organization): Response|JsonResponse
    {
        $this->authorize('delete', $organization);

        try {
            $this->organizationRepository->delete($organization);

            return response()->noContent();
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 422);
        }
    }
}
