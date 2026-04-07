<?php

namespace App\Livewire\Layouts\Units;

use App\Livewire\Concerns\InspectionConfig;
use App\Livewire\Concerns\WithContractData;
use App\Livewire\Concerns\WithESignature;
use App\Models\ContractAuditLog;
use App\Models\Lease;
use App\Models\MoveOutInspection;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class LandlordContractViewer extends Component
{
    use WithESignature, WithContractData;

    public $showModal = false;
    public $contractType = 'move-in';
    public $contractData = null;
    public $leaseId = null;

    // Move-in signature display
    public $tenantSignature = null;
    public $ownerSignature = null;
    public $managerSignature = null;
    public $tenantSignedAt = null;
    public $ownerSignedAt = null;
    public $managerSignedAt = null;
    public $contractAgreed = false;

    // Move-out signature display
    public $moveOutTenantSignature = null;
    public $moveOutOwnerSignature = null;
    public $moveOutManagerSignature = null;
    public $moveOutTenantSignedAt = null;
    public $moveOutOwnerSignedAt = null;
    public $moveOutManagerSignedAt = null;
    public $moveOutContractAgreed = false;

    // Signature modals
    public $showSignatureModal = false;
    public $showMoveOutSignatureModal = false;

    // Inspection data
    public $inspectionChecklist = [];
    public $itemsReceived = [];
    public $moveOutChecklist = [];
    public $itemsReturned = [];
    public $inspectionSaved = false;
    public $moveOutInspectionSaved = false;

    #[On('open-landlord-contract-viewer')]
    public function openViewer(int $leaseId, string $contractType = 'move-in'): void
    {
        $this->contractType = $contractType;
        $this->leaseId = $leaseId;

        $lease = Lease::with([
            'tenant',
            'bed.unit.property.owner',
            'billings',
            'moveInInspections',
            'moveOutInspections',
        ])->find($leaseId);

        if (!$lease || !$lease->tenant) {
            return;
        }

        $this->contractData = $this->buildContractDataArray($lease->tenant, $lease);
        $this->loadSignatureState($lease);

        // Load move-in inspection
        $this->loadInspection(
            $lease,
            'moveInInspections',
            'inspectionChecklist',
            'itemsReceived',
            'inspectionSaved',
            'item_received',
            InspectionConfig::RECEIVED_ITEMS
        );

        // Load move-out inspection
        $this->loadInspection(
            $lease,
            'moveOutInspections',
            'moveOutChecklist',
            'itemsReturned',
            'moveOutInspectionSaved',
            'item_returned',
            InspectionConfig::RETURNED_ITEMS
        );

        $this->showModal = true;
    }

    public function switchTab(string $type): void
    {
        $this->contractType = $type;
    }

    // ===== OWNER SIGNING (Move-in) =====

    public function openSignatureModal(): void
    {
        $this->showSignatureModal = true;
    }

    public function closeSignatureModal(): void
    {
        $this->showSignatureModal = false;
    }

    public function saveOwnerSignature(string $signatureData): void
    {
        if (!$this->leaseId) return;

        $lease = Lease::find($this->leaseId);
        if (!$lease) return;

        // Verify the authenticated user is the property owner
        $ownerId = $this->findOwnerIdForLease($lease);
        if (Auth::id() !== $ownerId) {
            $this->dispatch('notify', type: 'error', title: 'Unauthorized', description: 'Only the property owner can sign this contract.');
            return;
        }

        $result = $this->saveLeaseSignature($lease, $signatureData, 'owner', 'movein');

        $this->ownerSignature = $result['signature'];
        $this->ownerSignedAt = $result['signedAt'];
        $this->contractAgreed = $result['agreed'];

        // Notify manager that owner signed → manager's turn to sign as witness
        $this->notifyManagerOfOwnerSign($lease, 'move-in');

        // Update signature_info in contractData
        $lease->refresh();
        $this->contractData['signature_info'] = [
            'tenant_signature'     => $lease->tenant_signature,
            'tenant_signed_at'     => $lease->tenant_signed_at?->format('M d, Y h:i A'),
            'owner_signature'      => $lease->owner_signature,
            'owner_signed_at'      => $lease->owner_signed_at?->format('M d, Y h:i A'),
            'manager_signature'    => $lease->manager_signature,
            'manager_signed_at'    => $lease->manager_signed_at?->format('M d, Y h:i A'),
            'contract_agreed'      => (bool) $lease->contract_agreed,
            'signed_contract_path' => $lease->signed_contract_path,
        ];

        $this->closeSignatureModal();
        $this->dispatch('signature-saved');
        $this->dispatch('notify', type: 'success', title: 'Signature Saved', description: 'You have signed the move-in contract as the property owner.');
    }

    // ===== OWNER SIGNING (Move-out) =====

    public function openMoveOutSignatureModal(): void
    {
        // Refresh outstanding balances to ensure real-time accuracy before signing
        $lease = Lease::find($this->leaseId);
        if ($lease) {
            $this->contractData['outstanding_balances'] = $this->buildOutstandingBalances($lease);
            $this->contractData['deposit_refund'] = [
                'amount' => $lease->deposit_refund_amount,
                'deductions' => $lease->deposit_deductions,
                'interest_earned' => $lease->deposit_interest_amount,
            ];
        }

        // Block signing if there are TBD (unconfirmed) repair/replacement costs
        $hasTBD = MoveOutInspection::where('lease_id', $this->leaseId)
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('type', 'checklist')
                       ->whereIn('condition', ['damaged', 'missing'])
                       ->where(function ($q3) {
                           $q3->whereNull('repair_cost')->orWhere('repair_cost', 0);
                       });
                })
                ->orWhere(function ($q2) {
                    $q2->where('type', 'item_returned')
                       ->where('is_returned', false)
                       ->where(function ($q3) {
                           $q3->whereNull('replacement_cost')->orWhere('replacement_cost', 0);
                       });
                });
            })
            ->exists();

        if ($hasTBD) {
            $this->dispatch('notify', type: 'error', title: 'Cannot Sign Yet', description: 'All repair and replacement costs must be confirmed before signing. No TBD values allowed.');
            return;
        }

        $this->showMoveOutSignatureModal = true;
    }

    public function closeMoveOutSignatureModal(): void
    {
        $this->showMoveOutSignatureModal = false;
    }

    public function saveMoveOutOwnerSignature(string $signatureData): void
    {
        if (!$this->leaseId) return;

        $lease = Lease::find($this->leaseId);
        if (!$lease) return;

        $ownerId = $this->findOwnerIdForLease($lease);
        if (Auth::id() !== $ownerId) {
            $this->dispatch('notify', type: 'error', title: 'Unauthorized', description: 'Only the property owner can sign this contract.');
            return;
        }

        $result = $this->saveLeaseSignature($lease, $signatureData, 'owner', 'moveout');

        $this->moveOutOwnerSignature = $result['signature'];
        $this->moveOutOwnerSignedAt = $result['signedAt'];
        $this->moveOutContractAgreed = $result['agreed'];

        // Notify manager that owner signed → manager's turn to sign as witness
        $this->notifyManagerOfOwnerSign($lease, 'move-out');

        $this->closeMoveOutSignatureModal();
        $this->dispatch('moveout-signature-saved');
        $this->dispatch('notify', type: 'success', title: 'Signature Saved', description: 'You have signed the move-out contract as the property owner.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->contractData = null;
    }

    public function render()
    {
        return view('livewire.layouts.units.landlord-contract-viewer');
    }
}
