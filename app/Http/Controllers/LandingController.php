<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Services\ContactService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class LandingController extends Controller
{
    public function __construct(
        private readonly ContactService $service,
    ) {}

    public function index(): View
    {
        return view('landing');
    }

    public function store(ContactFormRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['email'] = $data['correo'];
        unset($data['correo']);

        $this->service->create($data);

        return redirect()
            ->route('landing')
            ->with('success', '¡Mensaje enviado exitosamente! Nos pondremos en contacto pronto.');
    }
}
