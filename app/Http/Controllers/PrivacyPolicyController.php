<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

final class PrivacyPolicyController extends Controller
{
    /**
     * Show the privacy policy for the application.
     */
    public function show(): Response
    {
        $policyFile = type(localizedMarkdownPath('policy.md'))->asString();

        return Inertia::render('PrivacyPolicy', [
            'policy' => Str::markdown(type(file_get_contents($policyFile))->asString()),
        ]);
    }
}
