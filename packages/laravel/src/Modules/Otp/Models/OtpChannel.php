<?php

namespace Ataurbdx\Authenticator\Modules\Otp\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OtpChannel extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'credentials' => 'array',
        'settings'    => 'array',
        'status'      => 'boolean',
    ];

    /**
     * Get the owning gateway model (Polymorphic: SmtpSetting, SmsGateway, etc.).
     */
    public function gateway(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope query to active channels.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get delivery mode ('code', 'link', 'both') defined in this channel's settings.
     */
    public function getDeliveryMode(): ?string
    {
        return $this->settings['mode'] ?? null;
    }

    /**
     * Get custom message template from settings if defined.
     */
    public function getMessageTemplate(): ?string
    {
        return $this->settings['template'] ?? null;
    }

    /**
     * Smart Configuration Resolver:
     * 1. Priority 1: Direct JSON credentials on this table
     * 2. Priority 2: Morphic Gateway Model (by Name -> by is_default -> first active)
     * 3. Priority 3: Fallback to .env (handled by OtpService)
     */
    public function getResolvedCredentials(?string $name = null): array
    {
        // 1. Priority 1: Direct JSON credentials on this record
        if (!empty($this->credentials) && is_array($this->credentials)) {
            return $this->credentials;
        }

        // 2. Priority 2: Direct linked Polymorphic Model instance
        if ($this->gateway) {
            return $this->formatModelCredentials($this->gateway);
        }

        // If gateway_type is configured as a Class but gateway_id is empty, query the gateway model dynamically
        if (!empty($this->gateway_type) && class_exists($this->gateway_type)) {
            $modelClass = $this->gateway_type;
            $gatewayRecord = null;
            $table = (new $modelClass)->getTable();

            // A. Search by Name if column exists and name is given
            $targetName = $name ?: $this->name;
            if ($targetName && \Illuminate\Support\Facades\Schema::hasColumn($table, 'name')) {
                $gatewayRecord = $modelClass::where('name', $targetName)->first();
            }

            // B. If not found by name, search by is_default if column exists
            if (!$gatewayRecord && \Illuminate\Support\Facades\Schema::hasColumn($table, 'is_default')) {
                $gatewayRecord = $modelClass::where('is_default', true)->first();
            }

            // C. Fallback: First active or first available record
            if (!$gatewayRecord) {
                if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'status')) {
                    $gatewayRecord = $modelClass::where('status', true)->first();
                } else {
                    $gatewayRecord = $modelClass::first();
                }
            }

            if ($gatewayRecord) {
                return $this->formatModelCredentials($gatewayRecord);
            }
        }

        return [];
    }

    /**
     * Decrypt sensitive credentials if necessary.
     */
    protected function formatModelCredentials(Model $model): array
    {
        $data = $model->toArray();

        if (!empty($data['password'])) {
            try {
                $data['password'] = decrypt($data['password']);
            } catch (\Throwable) {
                // Already in plain text
            }
        }

        if (!empty($data['api_secret'])) {
            try {
                $data['api_secret'] = decrypt($data['api_secret']);
            } catch (\Throwable) {
                // Already in plain text
            }
        }

        return $data;
    }
}
