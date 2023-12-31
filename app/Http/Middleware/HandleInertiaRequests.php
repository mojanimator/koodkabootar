<?php

namespace App\Http\Middleware;

use App\Http\Helpers\Variable;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user(),
            ],
            'isAdmin' => in_array(optional($request->user())->role, ['ad', 'go']),

            'location' => $request->url(),
//            'user' => optional(auth()->user())->only(['id', 'fullname', 'username',]),
            'locale' => function () {
                return app()->getLocale();
            },
            'langs' => Variable::LANGS,
            'images' => asset('assets/images') . '/',
            'language' => function () {
                if (!file_exists(lang_path('/' . app()->getLocale() . '.json'))) {
                    return [];
                }
                return json_decode(file_get_contents(
                        lang_path(
                            app()->getLocale() . '.json'))
                    , true);
            },
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
            'flash' => [
                'message' => fn() => $request->session()->get('flash_message'),
                'status' => fn() => $request->session()->get('flash_status'),
            ],
            'extra' => fn() => $request->session()->get('extra'),
            'pageItems' => Variable::PAGINATE,
            'socials' => [
                'whatsapp' => "https://wa.me/00989132258738",
                'telegram' => "https://t.me/hasannejhad",
                'phone' => "09132258738",
                'email' => "info@koodkabotar.com",
                'address' => __('address'),
            ],
        ]);
    }
}
