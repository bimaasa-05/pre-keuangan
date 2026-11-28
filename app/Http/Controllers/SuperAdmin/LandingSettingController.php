<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingSettingController extends Controller
{
    public function edit(): View
    {
        $settings = SiteSetting::getValue('landing', config('landing'));

        return view('super-admin.landing', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'hero.eyebrow' => ['nullable', 'string', 'max:100'],
            'hero.title' => ['required', 'string', 'max:150'],
            'hero.subtitle' => ['required', 'string', 'max:500'],
            'hero.cta_text' => ['nullable', 'string', 'max:80'],
            'features' => ['nullable', 'array', 'max:8'],
            'features.*.icon' => ['nullable', 'string', 'max:50'],
            'features.*.title' => ['required_with:features', 'string', 'max:100'],
            'features.*.description' => ['nullable', 'string', 'max:300'],
            'about.title' => ['nullable', 'string', 'max:150'],
            'about.text' => ['nullable', 'string', 'max:1000'],
            'about.points_text' => ['nullable', 'string', 'max:1000'],
            'announcement' => ['nullable', 'string', 'max:500'],
            'contact.email' => ['nullable', 'email', 'max:150'],
            'contact.phone' => ['nullable', 'string', 'max:50'],
            'contact.address' => ['nullable', 'string', 'max:255'],
            'footer' => ['nullable', 'string', 'max:150'],
        ]);

        $features = collect($data['features'] ?? [])
            ->filter(fn ($feature) => filled($feature['title'] ?? null))
            ->map(fn ($feature) => [
                'icon' => ($feature['icon'] ?? null) ?: 'sparkles',
                'title' => $feature['title'],
                'description' => $feature['description'] ?? '',
            ])
            ->values()
            ->all();

        $about = $data['about'] ?? [];
        $contact = $data['contact'] ?? [];

        $points = collect(preg_split('/\r\n|\r|\n/', $about['points_text'] ?? ''))
            ->map(fn ($point) => trim($point))
            ->filter()
            ->values()
            ->all();

        $settings = [
            'hero' => [
                'eyebrow' => $data['hero']['eyebrow'] ?? '',
                'title' => $data['hero']['title'],
                'subtitle' => $data['hero']['subtitle'],
                'cta_text' => $data['hero']['cta_text'] ?? 'Masuk ke Dashboard',
            ],
            'features' => $features,
            'about' => [
                'title' => $about['title'] ?? '',
                'text' => $about['text'] ?? '',
                'points' => $points,
            ],
            'announcement' => $data['announcement'] ?? '',
            'contact' => [
                'email' => $contact['email'] ?? '',
                'phone' => $contact['phone'] ?? '',
                'address' => $contact['address'] ?? '',
            ],
            'footer' => $data['footer'] ?? '',
        ];

        SiteSetting::setValue('landing', $settings);

        NotificationService::send(
            $request->user(),
            'Landing page diperbarui',
            'Konten landing page berhasil disimpan.',
            'globe',
            'warning',
            route('landing'),
            NotificationService::superAdmins(),
        );

        return redirect()
            ->route('super-admin.landing.edit')
            ->with('success', 'Landing page berhasil diperbarui.');
    }
}
