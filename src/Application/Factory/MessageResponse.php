<?php

namespace App\Application\Factory;

use \Illuminate\Database\Eloquent\Collection;

class MessageResponse
{
    public int $status = 0;
    public string $statusText = "success";
    public mixed $data = null;

    public function __construct(int $status, string $statusText, mixed $data)
    {
        $this->status = $status;
        $this->statusText = $statusText;
        $this->data = $data;
    }

    public static function getInstance(int $status, string $statusText, mixed $data): self
    {
        return new self($status, $statusText, $data);
    }
}