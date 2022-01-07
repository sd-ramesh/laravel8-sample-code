<?php
declare(strict_types=1);


namespace App\Objects\Enums;

use Funeralzone\ValueObjects\Enums\EnumTrait;
use Funeralzone\ValueObjects\ValueObject;

/**
 * User roles.
 *
 * @method static UserRole ADMINISTRATOR()
 * @method static UserRole CUSTOMER() Customer
 * @method static UserRole VENDOR()
 */
final class UserRole implements ValueObject
{
    use EnumTrait;

    /**
     * User Role: Admin
     *
     * @var int
     */
    public const ADMINISTRATOR = 0;

    /**
     * User Role: Customer
     *
     * @var int
     */
    public const CUSTOMER = 1;

    /**
     * User Role: Vendor
     *
     * @var int
     */
    public const VENDOR = 2;
}
