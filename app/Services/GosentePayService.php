<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

class GosentePayService
{
    protected string $baseUrl = 'https://api.gosentepay.com/v1';

    /**
     * Get or cache the Bearer token for 24 hours.
     */
    public function getToken(): ?string
    {
        return Cache::remember('gosente_token', 82800, function () {
            try {
                $response = Http::timeout(15)->post("{$this->baseUrl}/authorization_token", [
                    'api_key' => config('services.gosente.api_key'),
                ]);

                if ($response->successful() && $response->json('status') === 'success') {
                    return $response->json('token');
                }
            } catch (Exception $e) {
                // Log exception if needed
            }

            return null;
        });
    }

    /**
     * Request a mobile money deposit (STK Push).
     */
    public function deposit(array $data): array
    {
        try {
            $token = $this->getToken();

            if (!$token) {
                return [
                    'status' => 'error',
                    'message' => 'Failed to generate authorization token.',
                ];
            }

            $response = Http::withToken($token)
                ->timeout(20)
                ->post("{$this->baseUrl}/deposit", [
                    'secret_key' => config('services.gosente.secret_key'),
                    'phone'      => $data['phone'],
                    'amount'     => $data['amount'],
                    'email'      => $data['email'],
                    'ref'        => $data['ref'],
                    'callback'   => $data['callback'],
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Gateway responded with status code: ' . $response->status(),
                'error' => $response->json(),
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Payment gateway connection failed or timed out.',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check transaction status.
     */
    public function checkStatus(string $reference): array
    {
        try {
            $token = $this->getToken();

            $response = Http::withToken($token)
                ->timeout(15)
                ->post("{$this->baseUrl}/transaction-status", [
                    'ref' => $reference,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Could not retrieve status, code: ' . $response->status(),
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }


    /**
     * Send a withdrawal (payout) to a mobile money wallet.
     */
    public function withdraw(array $data): array
    {
        try {
            $token = $this->getToken();

            if (!$token) {
                return [
                    'status' => 'error',
                    'message' => 'Failed to generate authorization token.',
                ];
            }

            $response = Http::withToken($token)
                ->timeout(20)
                ->post("{$this->baseUrl}/withdraw", [
                    'secret_key' => config('services.gosente.secret_key'),
                    'phone'      => $data['phone'],      // Recipient phone number
                    'amount'     => $data['amount'],     // Amount in UGX (Min 500)
                    'email'      => $data['email'],      // Recipient email
                    'reason'     => $data['reason'],     // Description/Reason for payout
                    'ref'        => $data['ref'],        // Unique payout reference
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Gateway responded with status code: ' . $response->status(),
                'error' => $response->json(),
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Withdrawal connection failed or timed out.',
                'error' => $e->getMessage(),
            ];
        }
    }
}