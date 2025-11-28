<?php

namespace Pterodactyl\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use Pterodactyl\Models\UserServerOrder;
use Pterodactyl\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ServersOrderController extends Controller
{
    /**
     * Return the current user's server preferences (order and sort option).
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $preferences = $user->serverOrder;

        return response()->json([
            'order' => $preferences->order ?? [],
            'sort_option' => $preferences->sort_option ?? 'default',
        ]);
    }

    /**
     * Update the current user's server preferences.
     */
    public function update(Request $request)
    {
        try {
            $user = $request->user();

            $data = $request->validate([
                'order' => ['sometimes', 'array'],
                'order.*' => ['string'], // server UUIDs
                'sort_option' => ['sometimes', 'string', 'in:default,name_asc,custom'],
            ]);

            // Prepare data for upsert, filtering out null values
            $updateData = [];
            if (isset($data['order'])) {
                $updateData['order'] = $data['order'];
            }
            if (isset($data['sort_option'])) {
                $updateData['sort_option'] = $data['sort_option'];
            }

            // Upsert for this user
            $record = UserServerOrder::updateOrCreate(
                ['user_id' => $user->id],
                $updateData
            );

            return response()->json([
                'order' => $record->order ?? [],
                'sort_option' => $record->sort_option ?? 'default',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating server order preferences', [
                'user_id' => $request->user()->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'errors' => [
                    [
                        'code' => 'ServerOrderUpdateError',
                        'status' => '500',
                        'detail' => $e->getMessage()
                    ]
                ]
            ], 500);
        }
    }
}
