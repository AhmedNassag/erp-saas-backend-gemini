<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\MessagingException;
use Illuminate\Support\Facades\Log;
use App\Models\Notification as DbNotification;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(config('firebase.projects.app.credentials'));

        $this->messaging = $factory->createMessaging();
    }



    /**
     * Send to single token
     */
    protected function buildPayload(array $data, $user, $senderId = null): array
    {
        return array_merge($data, [
            'sender_id'     => $senderId ?? auth()->id(),
            'receiver_id'   => $user->id,
            'receiver_type' => $user->type,
            'sender_name'   => auth()->user()->name ?? 'System',
        ]);
    }



    public function sendToToken(string $token, array $data = [], array $notification = []): bool
    {
        try {

            Log::info('FCM Sending Started', [
                'token'        => $token,
                'data'         => $data,
                'notification' => $notification
            ]);

            // convert all data values to string
            $data = collect($data)->map(function ($value) {
                return (string) $value;
            })->toArray();

            $message = CloudMessage::withTarget('token', $token);

            if (!empty($notification)) {

                $message = $message->withNotification(
                    Notification::create(
                        $notification['title'] ?? '',
                        $notification['body'] ?? ''
                    )
                );
            }

            if (!empty($data)) {
                $message = $message->withData($data);
            }

            $response = $this->messaging->send($message);

            Log::info('FCM Sent Successfully', [
                'token'    => $token,
                'response' => $response
            ]);

            return true;

        } catch (\Throwable $e) {

            Log::error('FCM Send Failed', [
                'token'         => $token,
                'error_message' => $e->getMessage(),
                'error_trace'   => $e->getTraceAsString(),
                'data'          => $data,
                'notification'  => $notification
            ]);

            return false;
        }
    }



    /**
     * Send to multiple tokens
     */
    public function sendToMultipleTokens(array $tokens, array $data = [], array $notification = []): array
    {
        $results = [
            'success' => [],
            'failed'  => []
        ];

        foreach ($tokens as $token) {
            $status = $this->sendToToken($token, $data, $notification);

            if ($status) {
                $results['success'][] = $token;
            } else {
                $results['failed'][] = $token;
            }
        }

        return $results;
    }



    /**
     * Send to user (multi-device)
     */
    // public function sendToUser($user, array $data = [], array $notification = []): array
    // {
    //     if (is_numeric($user)) {
    //         $user = User::find($user);
    //     }

    //     if (!$user) {
    //         return ['success' => false, 'message' => 'User not found'];
    //     }

    //     $tokens = $user->deviceTokens()->pluck('token')->toArray();

    //     Log::info('User Tokens', [
    //         'user_id' => $user->id,
    //         'tokens' => $tokens
    //     ]);

    //     // Also check if user has a token property directly (backup)
    //     if ($user->fcm_token) {
    //         $tokens[] = $user->fcm_token;
    //     }
    //     if ($user->token_token) {
    //         $tokens[] = $user->token_token;
    //     }

    //     $tokens = array_unique(array_filter($tokens));

    //     if (empty($tokens)) {
    //         return [
    //             'success' => false,
    //             'message' => 'No devices found',
    //             'success_tokens' => [],
    //             'failed_tokens' => []
    //         ];
    //     }

    //     $result = $this->sendToMultipleTokens($tokens, $data, $notification);

    //     // cleanup invalid tokens
    //     if (!empty($result['failed'])) {
    //         $user->deviceTokens()
    //             ->whereIn('token', $result['failed'])
    //             ->delete();
    //     }

    //     return $result;
    // }



    public function sendToUser($user, array $data = [], array $notification = []): array
    {
        Log::info('sendToUser START', [
            'user_id'      => is_object($user) ? $user->id : $user,
            'data'         => $data,
            'notification' => $notification,
        ]);
        if (is_numeric($user)) {
            $user = User::find($user);
        }

        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        $tokens = $user->deviceTokens()->pluck('token')->toArray();

        if ($user->fcm_token) {
            $tokens[] = $user->fcm_token;
        }

        if ($user->token_token) {
            $tokens[] = $user->token_token;
        }

        $tokens = array_unique(array_filter($tokens));

        Log::info('SENDING TO FIREBASE', [
            'token'   => $tokens,
            'message' => [
                'notification' => $notification,
                'data'         => $data,
            ]
        ]);

        if (empty($tokens)) {
            return [
                'success'        => false,
                'message'        => 'No devices found',
                'success_tokens' => [],
                'failed_tokens'  => []
            ];
        }

        $result = $this->sendToMultipleTokens($tokens, $data, $notification);

        if (!empty($result['failed'])) {
            $user->deviceTokens()
                ->whereIn('token', $result['failed'])
                ->delete();
        }

        return $result;
    }



    /**
     * MAIN METHOD: Send notification, save to DB, and send push notifications
     */
    // public function send($users,string $title,string $body,Model $model = null,array $data = [],?int $senderId = null) {
    //     // 1. Resolve users to a collection of User models
    //     if ($users instanceof Collection) {
    //         $recipients = $users;
    //     } elseif ($users instanceof User) {
    //         $recipients = collect([$users]);
    //     } elseif (is_array($users)) {
    //         $recipients = User::whereIn('id', $users)->get();
    //     } else {
    //         $recipients = User::where('id', $users)->get();
    //     }

    //     if ($recipients->isEmpty()) {
    //         return ['success' => false, 'message' => 'No valid users found'];
    //     }

    //     // 2. Save to Database
    //     $dbNotification = DbNotification::create([
    //         'sender_id' => $senderId ?? (auth()->check() ? auth()->id() : null),
    //         'title' => $title,
    //         'body' => $body,
    //         'data' => $data,
    //         'model_type' => $model ? get_class($model) : null,
    //         'model_id' => $model ? $model->id : null,
    //     ]);

    //     // 3. Attach Recipients in pivot table
    //     $dbNotification->recipients()->attach($recipients->pluck('id')->toArray());

    //     // 4. Send Push Notification via Firebase for each user
    //     $firebaseResults = [];
    //     foreach ($recipients as $user) {
    //         $firebaseResults[$user->id] = $this->sendToUser($user, $data, [
    //             'title' => $title,
    //             'body' => $body
    //         ]);
    //     }
    //     Log::info('Firebase Final Result', [
    //         'results' => $firebaseResults
    //     ]);
    //     return [
    //         'success' => true,
    //         'db_notification' => $dbNotification,
    //         'firebase_results' => $firebaseResults
    //     ];

    // }



    public function send($users,string $title,string $body,Model $model = null,array $data = [],?int $senderId = null)
    {
        // Resolve users
        if ($users instanceof Collection) {
            $recipients = $users;
        } elseif ($users instanceof User) {
            $recipients = collect([$users]);
        } elseif (is_array($users)) {
            $recipients = User::whereIn('id', $users)->get();
        } else {
            $recipients = User::where('id', $users)->get();
        }

        if ($recipients->isEmpty()) {
            return ['success' => false, 'message' => 'No valid users found'];
        }

        // Save notification
        $dbNotification = DbNotification::create([
            'sender_id'  => $senderId ?? (auth()->id()),
            'title'      => $title,
            'body'       => $body,
            'data'       => $data,
            'model_type' => $model ? get_class($model) : null,
            'model_id'   => $model?->id,
        ]);

        // Attach recipients
        $dbNotification->recipients()->attach(
            $recipients->pluck('id')->toArray()
        );

        // Send Firebase
        $firebaseResults = [];

        foreach ($recipients as $user) {

            $payload = $this->buildPayload($data, $user, $senderId);

            $firebaseResults[$user->id] = $this->sendToUser(
                $user,
                $payload,
                [
                    'title' => $title,
                    'body'  => $body,
                ]
            );
        }

        Log::info('🔥 FINAL FIREBASE PAYLOAD', [
            'user_id'      => $user->id,
            'notification' => [
                'title' => $title,
                'body'  => $body
            ],
            'data'         => $payload,
        ]);

        return [
            'success'          => true,
            'db_notification'  => $dbNotification,
            'firebase_results' => $firebaseResults
        ];
    }
}
