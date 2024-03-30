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
     * Get the list of the organizations
     * 
     * The list can be filtered by name, status.
     * You must have the <strong>SUPERADMIN</strong> role to get the list.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Organization::class);
        $organizations = $this->organizationRepository->getOrganizations($request->all());

        return OrganizationResource::collection($organizations);
    }

    /**
     * Store a new organization
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
     * Display an organization
     */
    public function show(Organization $organization): OrganizationResource
    {
        $this->authorize('view', $organization);

        return new OrganizationResource($organization);
    }

    /**
     * Update an organization
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
     * Delete an organization
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
