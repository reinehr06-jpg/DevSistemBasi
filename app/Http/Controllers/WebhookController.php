<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handleBitbucket(Request $request)
    {
        $data = $request->all();

        $repo = $data['repository']['name'] ?? 'Unknown';
        $branch = $data['push']['changes'][0]['new']['name'] ?? 'Unknown';
        $author = $data['actor']['display_name'] ?? 'Unknown';

        $message = "Novo push em {$repo} ({$branch}) por {$author}";

        Notification::create([
            'type' => 'git',
            'title' => 'Novo Push Recebido',
            'message' => $message,
            'payload' => $data,
        ]);

        return response()->json(['success' => true, 'message' => 'Webhook processed']);
    }

    public function handle(Request $request)
    {
        $type = $request->type ?? 'sistema';

        Notification::create([
            'type' => $type,
            'title' => $request->title ?? 'Notificação',
            'message' => $request->message ?? '',
            'payload' => $request->all(),
        ]);

        return response()->json(['success' => true]);
    }
}