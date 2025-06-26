<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class RedirectUrlResolver
{
    public function __invoke(Request $request): ?string
    {
        $path = $request->getPathInfo();

        if (str_starts_with($path, '/user')) {
            return '/user/crud/list';
        }

        // if (str_starts_with($path, '/product')) {
        //     return '/product/list';
        // }

        return '/';
    }
}