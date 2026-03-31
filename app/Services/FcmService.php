<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    /**
     * Send a push notification using Firebase Cloud Messaging API (V1).
     * This requires a service account JSON file in storage/app/firebase.json.
     */
    public function sendPush($token, $title, $body, $data = [])
    {
        if (empty($token)) {
            return false;
        }

        $projectId = config('services.fcm.project_id');
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            Log::error('FCM v1: Failed to obtain access token.');
            return false;
        }

        try {
            $response = Http::withToken($accessToken)
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                    'message' => [
                        'token' => $token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => array_map('strval', $data), // FCM v1 data must be strings
                        'android' => [
                            'priority' => 'high',
                            'notification' => [
                                'sound' => 'default',
                            ],
                        ],
                        'apns' => [
                            'payload' => [
                                'aps' => [
                                    'sound' => 'default',
                                ],
                            ],
                        ],
                    ],
                ]);

            if ($response->successful()) {
                Log::info("FCM v1 Notification sent to token: {$token}");
                return true;
            }

            Log::error('FCM v1 Notification failed: ' . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error('FCM v1 Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtain an OAuth2 access token for FCM v1.
     */
    private function getAccessToken()
    {
        try {
            $jsonPath = storage_path('app/firebase.json');
            
            if (!file_exists($jsonPath)) {
                Log::error("FCM v1: firebase.json not found at {$jsonPath}");
                return null;
            }

            $client = new \Google_Client();
            $client->setAuthConfig($jsonPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            
            $token = $client->fetchAccessTokenWithAssertion();
            
            return $token['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error("FCM v1 Token Error: " . $e->getMessage());
            return null;
        }
    }
}

