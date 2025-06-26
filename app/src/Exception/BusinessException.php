<?php
namespace App\Exception;

use Throwable;

class BusinessException extends \Exception
{
    private array $details;

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null, array $details = [])
    {
        parent::__construct($message, $code, $previous);
        $this->details = $details;
    }


    public function getDetails(): array
    {
        return $this->details;
    }
}