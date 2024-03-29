<?php

namespace App\Swagger\Requests;

/**
 * @OA\Schema(
 *      title="StoreOrganizationrequest",
 *      description="Store Organization request body data",
 *      type="object",
 *      required={"name", "status"}
 * )
 */
class StoreOrganizationRequest
{
    /**
     * @OA\Property(
     *      title="name",
     *      description="Name of the new organization",
     *      example="A nice organization",
     *      maxLength=50,
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *      title="comment",
     *      description="Comment of the new organization",
     *      example="This is new organization's comment",
     * maxLength=100,
     * )
     *
     * @var string
     */
    public $comment;

    /**
     * @OA\Property(
     *      title="status",
     *      description="Status of the new organization (ACTIVE / INACTIVE)",
     *      example="ACTIVE",
     * enum={"ACTIVE","INACTIVE"},
     * )
     *
     * @var string
     */
    public $status;
}
