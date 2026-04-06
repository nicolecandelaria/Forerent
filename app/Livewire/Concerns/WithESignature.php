<?php

namespace App\Livewire\Concerns;

use App\Models\ContractAuditLog;
use App\Models\Lease;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait WithESignature
{
    /**
     * Process base64 signature data, store as PNG, and return the storage path.
     * Deletes the old signature file if one exists.
     */
    protected function processAndStoreSignature(
        string $signatureData,
        string $filenamePrefix,
        ?string $oldSignaturePath = null
    ): string {
        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $signatureData);
        $imageData = base64_decode($imageData);

        $filename = "signatures/{$filenamePrefix}_" . time() . '.png';
        Storage::disk('public')->put($filename, $imageData);

        if ($oldSignaturePath) {
            Storage::disk('public')->delete($oldSignaturePath);
        }

        return $filename;
    }

    /**
     * Save a signature to a lease for a given contract type and role.
     *
     * Signing order: owner (1st) → manager/witness (2nd) → tenant (3rd)
     *
     * @param Lease  $lease         The lease to update
     * @param string $signatureData Base64 encoded signature image
     * @param string $role          'owner', 'manager', or 'tenant'
     * @param string $type          'movein' or 'moveout'
     * @return array{signature: string, signedAt: string, agreed: bool}
     */
    protected function saveLeaseSignature(
        Lease $lease,
        string $signatureData,
        string $role,
        string $type = 'movein'
    ): array {
        $dbPrefix = $type === 'moveout' ? 'moveout_' : '';

        // Map role to DB field names
        $sigField = $dbPrefix . match ($role) {
            'tenant' => 'tenant_signature',
            'manager' => 'manager_signature',
            default => 'owner_signature',
        };
        $dateField = $dbPrefix . match ($role) {
            'tenant' => 'tenant_signed_at',
            'manager' => 'manager_signed_at',
            default => 'owner_signed_at',
        };
        $ipField = $dbPrefix . match ($role) {
            'tenant' => 'tenant_signed_ip',
            'manager' => 'manager_signed_ip',
            default => 'owner_signed_ip',
        };

        $agreedField = $dbPrefix . 'contract_agreed';
        $ownerSigField = $dbPrefix . 'owner_signature';
        $managerSigField = $dbPrefix . 'manager_signature';
        $tenantSigField = $dbPrefix . 'tenant_signature';
        $statusField = $type === 'moveout' ? 'moveout_contract_status' : 'contract_status';

        $filename = $this->processAndStoreSignature(
            $signatureData,
            "{$lease->lease_id}_{$type}_{$role}",
            $lease->$sigField
        );

        // Use a transaction with row lock to prevent race condition
        $agreed = DB::transaction(function () use (
            $lease, $filename, $sigField, $dateField, $ipField,
            $agreedField, $ownerSigField, $managerSigField, $tenantSigField,
            $statusField, $role, $type
        ) {
            $locked = Lease::where('lease_id', $lease->lease_id)->lockForUpdate()->first();

            $locked->update([
                $sigField => $filename,
                $dateField => now(),
                $ipField => request()->ip(),
            ]);

            $locked->refresh();

            // Check if all three signatures exist
            $allSigned = $locked->$ownerSigField && $locked->$managerSigField && $locked->$tenantSigField;

            // Auto-transition contract status based on signing order:
            // owner (1st) → manager (2nd) → tenant (3rd)
            if ($allSigned) {
                $locked->update([
                    $agreedField => true,
                    $statusField => 'executed',
                ]);
            } elseif ($locked->$statusField !== 'executed') {
                if ($role === 'owner') {
                    // Owner signed → waiting for manager witness
                    $locked->update([$statusField => 'pending_manager']);
                } elseif ($role === 'manager') {
                    // Manager signed → waiting for tenant
                    $locked->update([$statusField => 'pending_tenant']);
                } elseif ($role === 'tenant') {
                    // Tenant signed but others missing (shouldn't normally happen with enforced order)
                    if (!$locked->$ownerSigField) {
                        $locked->update([$statusField => 'pending_owner']);
                    } elseif (!$locked->$managerSigField) {
                        $locked->update([$statusField => 'pending_manager']);
                    }
                }
            }

            // Audit log
            ContractAuditLog::log($locked->lease_id, "{$type}_signature_{$role}", [
                'field_changed' => $sigField,
                'new_value' => $filename,
                'metadata' => [
                    'contract_type' => $type,
                    'role' => $role,
                    'ip' => request()->ip(),
                    'all_signed' => $allSigned,
                ],
            ]);

            if ($allSigned) {
                ContractAuditLog::log($locked->lease_id, "{$type}_contract_executed", [
                    'metadata' => ['contract_type' => $type],
                ]);
            }

            return $allSigned;
        });

        $lease->refresh();

        return [
            'signature' => $filename,
            'signedAt' => now()->format('M d, Y h:i A'),
            'agreed' => $agreed,
        ];
    }
}
