<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RedirectUrlResolver
{
    public function __construct(private UrlGeneratorInterface $urlGenerator) 
    {}

    public function __invoke(Request $request): ?string
    {
        $path = $request->getPathInfo();

        if (str_starts_with($path, '/admin/user')) {
            return $this->urlGenerator->generate('app_user_crud_index');
        }

        if (str_starts_with($path, '/admin/record')) {
            return $this->urlGenerator->generate('app_record_label_index');
        }

        if (str_starts_with($path, '/admin/artist')) {
            return $this->urlGenerator->generate('app_artist_crud_index');
        }


        if (str_starts_with($path, '/admin')) {
            return $this->urlGenerator->generate('app_home_dashboard');
        }


        return '/';
    }
}