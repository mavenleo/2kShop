<?php

namespace App\Exceptions;

use Exception;

class WishlistException extends Exception
{
    protected $code = 400;

    public function __construct(string $message = 'Wishlist operation failed', int $code = 400)
    {
        parent::__construct($message, $code);
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $this->getMessage(),
                'code' => $this->getCode(),
            ], $this->getCode());
        }

        return back()->withErrors(['wishlist' => $this->getMessage()]);
    }
}
