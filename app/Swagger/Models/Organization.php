<?php

namespace App\Swagger\Models;

/**
 * @OA\Schema(
 *     title="Organization",
 *     description="Organization model",
 *
 *     @OA\Xml(
 *         name="Project"
 *     )
 * )
 */
class Organization
{
    /**
     * @OA\Property(
     *     title="id",
     *     description="id of the organization",
     *     format="uuid",
     *     example="9ba9b105-c56b-4537-86c0-76b0034d1c41"
     * )
     *
     * @var string
     */
    private $id;

    /**
     * @OA\Property(
     *      title="Name",
     *      description="Name of the organization",
     *      example="A nice organization"
     * )
     *
     * @var string
     */
    public $name;

    /**
     * @OA\Property(
     *      title="Comment",
     *      description="Comment of the organization",
     *      example="This is a comment ......"
     * )
     *
     * @var string
     */
    public $comment;

    /**
     * @OA\Property(
     *      title="Status",
     *      description="The status of the organization (ACTIVE / INACTIVE)",
     *      example="ACTIVE"
     * )
     *
     * @var string
     */
    public $status;

    /**
     * @OA\Property(
     *     title="Created at",
     *     description="Created at",
     *     example="2024-03-28 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $created_at;

    /**
     * @OA\Property(
     *     title="Updated at",
     *     description="Updated at",
     *     example="2024-03-28 17:54:34",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @OA\Property(
     *     title="Created by",
     *     description="Created by",
     *     example="Dominique",
     *     type="string"
     * )
     *
     * @var string
     */
    private $created_by;

    /**
     * @OA\Property(
     *     title="Updated by",
     *     description="Updated by",
     *     example="Clément",
     *     type="string"
     * )
     *
     * @var string
     */
    private $updated_by;
}
