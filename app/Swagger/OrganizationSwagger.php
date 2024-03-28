<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *      path="/organizations",
 *      operationId="getProjectsList",
 *      tags={"organizations"},
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
 *  * @OA\Parameter(
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
 *          @OA\JsonContent(
 *             type="array",
 *
 *             @OA\Items(ref="#/components/schemas/Organization")
 *         ),
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
class OrganizationSwagger
{
    //
}
