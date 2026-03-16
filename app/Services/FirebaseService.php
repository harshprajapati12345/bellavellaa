<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FirebaseService
{
    protected $projectId;
    protected $clientEmail;
    protected $privateKey;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id');
        $this->clientEmail = config('services.firebase.client_email');
        $this->privateKey = str_replace('\\n', "\n", config('services.firebase.private_key'));
    }

    public function pushJobToFirestore($professionalId, array $jobData)
    {
        return $this->pushToFirestore("job_requests/professional_{$professionalId}", $jobData, 'pending');
    }

    /**
     * Push a general notification to Firestore.
     */
    public function pushNotificationToFirestore($professionalId, array $notificationData)
    {
        return $this->pushToFirestore("notifications/professional_{$professionalId}", $notificationData, 'unread');
    }

    /**
     * Push an incoming call event to Firestore.
     */
    public function pushCallToFirestore($professionalId, array $callData)
    {
        return $this->pushToFirestore("calls/professional_{$professionalId}", $callData, 'incoming');
    }

    /**
     * Generic helper for Firestore PATCH (Uber-style doc update)
     */
    protected function pushToFirestore($path, array $data, $defaultStatus)
    {
        if (!$this->projectId || !$this->privateKey) {
            Log::warning('Firebase credentials missing. Skipping Firestore push.');
            return false;
        }

        $token = $this->getAccessToken();
        if (!$token) return false;

        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$path}";

        // Standard fields for all real-time events
        if (!isset($data['status'])) $data['status'] = $defaultStatus;
        $data['updated_at'] = time();

        $fields = $this->formatFirestoreFields($data);

        $response = Http::withToken($token)
            ->patch($url, [
                'fields' => $fields
            ]);

        if ($response->failed()) {
            Log::error('Firestore Push Failed', [
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return false;
        }

        return true;
    }

    /**
     * Send a Push Notification via FCM V1.
     */
    public function sendPushNotification($token, $title, $body, array $data = [])
    {
        if (!$this->projectId || !$this->privateKey) {
            Log::warning('Firebase credentials missing. Skipping FCM push.');
            return false;
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) return false;

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $response = Http::withToken($accessToken)
            ->post($url, [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => array_map('strval', $data), // FCM data values must be strings
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'notification_sound',
                            'channel_id' => 'high_importance_channel',
                        ]
                    ],
                ]
            ]);

        if ($response->failed()) {
            Log::error('FCM Push Failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return false;
        }

        return true;
    }

    /**
     * Generate OAuth2 Access Token using Service Account (JWT).
     */
    protected function getAccessToken()
    {
        $now = time();
        $payload = [
            'iss' => $this->clientEmail,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging https://www.googleapis.com/auth/datastore',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now,
        ];

        try {
            $jwt = $this->encodeJwt($payload);
            
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->failed()) {
                Log::error('Firebase Token Generation Failed', [
                    'body' => $response->body()
                ]);
                return null;
            }

            return $response->json('access_token');
        } catch (\Exception $e) {
            Log::error('Firebase JWT Encoding Failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Simple JWT encoder using OpenSSL.
     */
    protected function encodeJwt($payload)
    {
        $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        
        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode(json_encode($payload));
        
        $signature = '';
        openssl_sign($base64UrlHeader . "." . $base64UrlPayload, $signature, $this->privateKey, OPENSSL_ALGO_SHA256);
        $base64UrlSignature = $this->base64UrlEncode($signature);
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    protected function base64UrlEncode($data)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    /**
     * Format a flat array into Firestore REST API's complex "fields" object.
     */
    protected function formatFirestoreFields(array $data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            if (is_int($value)) {
                $fields[$key] = ['integerValue' => (string)$value];
            } elseif (is_float($value) || is_double($value)) {
                $fields[$key] = ['doubleValue' => $value];
            } elseif (is_bool($value)) {
                $fields[$key] = ['booleanValue' => $value];
            } else {
                $fields[$key] = ['stringValue' => (string)$value];
            }
        }
        
        // Add updated_at if not present
        if (!isset($fields['updated_at'])) {
            $fields['updated_at'] = ['integerValue' => (string)time()];
        }
        
        return $fields;
    }
}
