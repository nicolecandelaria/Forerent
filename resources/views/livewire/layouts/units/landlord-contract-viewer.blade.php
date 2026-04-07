<div>
    @if($showModal && $contractData)
        @php
            $hasAnySignature = $ownerSignature || $managerSignature || $tenantSignature
                || $moveOutOwnerSignature || $moveOutManagerSignature || $moveOutTenantSignature;
        @endphp
        {{-- Backdrop --}}
        <div
            x-data="{ showLeaveConfirm: false }"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
            @click.self="{{ $hasAnySignature ? '$wire.closeModal()' : 'showLeaveConfirm = true' }}"
        >
            {{-- Modal Container --}}
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col mx-4 overflow-hidden">

                {{-- Header --}}
                <div class="flex-shrink-0 px-6 py-4 border-b border-gray-200 flex items-center justify-between" style="background: linear-gradient(135deg, #070589 0%, #2360E8 100%);">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: rgba(255,255,255,0.15);">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-white font-bold text-lg">
                                {{ $contractData['personal_info']['first_name'] }} {{ $contractData['personal_info']['last_name'] }}
                            </h2>
                            <p class="text-blue-200 text-xs">
                                {{ $contractData['personal_info']['property'] }} &middot; Unit {{ $contractData['personal_info']['unit'] }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Tab Switcher --}}
                        <div class="flex rounded-lg overflow-hidden border border-white/20">
                            <button
                                wire:click="switchTab('move-in')"
                                class="px-3 py-1.5 text-xs font-semibold transition-colors {{ $contractType === 'move-in' ? 'bg-white text-blue-700' : 'text-white/80 hover:text-white hover:bg-white/10' }}"
                            >
                                Move-In
                            </button>
                            @if($contractData['move_out_details']['move_out_initiated_at'] ?? null)
                                <button
                                    wire:click="switchTab('move-out')"
                                    class="px-3 py-1.5 text-xs font-semibold transition-colors {{ $contractType === 'move-out' ? 'bg-white text-blue-700' : 'text-white/80 hover:text-white hover:bg-white/10' }}"
                                >
                                    Move-Out
                                </button>
                            @endif
                        </div>

                        {{-- Close Button --}}
                        <button @click="{{ $hasAnySignature ? '$wire.closeModal()' : 'showLeaveConfirm = true' }}" class="text-white/80 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Contract Body (Scrollable) --}}
                <div class="flex-1 overflow-y-auto p-8 space-y-6 contract-body" style="font-size: 13px;">
                    @php
                        $t = $contractData;
                        $rate = (float) ($t['move_in_details']['monthly_rate'] ?? 0);
                        $deposit = (float) ($t['move_in_details']['security_deposit'] ?? 0);
                        $premium = (float) ($t['move_in_details']['short_term_premium'] ?? 0);
                        $dueDay = $t['move_in_details']['monthly_due_date'] ?? 1;
                        $dueSfx = match((int) $dueDay % 10) {
                            1 => (int) $dueDay === 11 ? 'th' : 'st',
                            2 => (int) $dueDay === 12 ? 'th' : 'nd',
                            3 => (int) $dueDay === 13 ? 'th' : 'rd',
                            default => 'th',
                        };
                        $totalMoveIn = $rate + $deposit;
                        $contractSettings = $t['contract_settings'] ?? [];
                    @endphp

                    @if($contractType === 'move-in')
                        @include('partials.move-in-contract-body', [
                            't' => $t,
                            'rate' => $rate,
                            'deposit' => $deposit,
                            'premium' => $premium,
                            'dueDay' => $dueDay,
                            'dueSfx' => $dueSfx,
                            'totalMoveIn' => $totalMoveIn,
                            'inspectionChecklist' => $inspectionChecklist,
                            'itemsReceived' => $itemsReceived,
                            'tenantSignature' => $tenantSignature,
                            'ownerSignature' => $ownerSignature,
                            'managerSignature' => $managerSignature,
                            'tenantSignedAt' => $tenantSignedAt,
                            'ownerSignedAt' => $ownerSignedAt,
                            'managerSignedAt' => $managerSignedAt,
                            'contractAgreed' => $contractAgreed,
                            'signatureMode' => 'owner',
                            'contractSettings' => $contractSettings,
                        ])
                    @elseif($contractType === 'move-out')
                        @include('partials.move-out-contract-body', [
                            't' => $t,
                            'deposit' => $deposit,
                            'moveOutChecklist' => $moveOutChecklist,
                            'itemsReturned' => $itemsReturned,
                            'inspectionChecklist' => $inspectionChecklist,
                            'moveOutTenantSignature' => $moveOutTenantSignature,
                            'moveOutOwnerSignature' => $moveOutOwnerSignature,
                            'moveOutManagerSignature' => $moveOutManagerSignature,
                            'moveOutTenantSignedAt' => $moveOutTenantSignedAt,
                            'moveOutOwnerSignedAt' => $moveOutOwnerSignedAt,
                            'moveOutManagerSignedAt' => $moveOutManagerSignedAt,
                            'moveOutContractAgreed' => $moveOutContractAgreed,
                            'signatureMode' => 'owner',
                            'outstandingBalances' => $contractData['outstanding_balances'] ?? [],
                            'depositRefund' => $contractData['deposit_refund'] ?? [],
                        ])
                    @endif
                </div>

                {{-- Footer --}}
                <div class="flex-shrink-0 px-6 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-between">
                    <p class="text-xs text-gray-400">
                        @if($contractAgreed || $moveOutContractAgreed)
                            Contract fully signed
                        @elseif(!$ownerSignature && $contractType === 'move-in')
                            Sign this contract as property owner
                        @elseif(!$moveOutOwnerSignature && $contractType === 'move-out')
                            Sign this contract as property owner
                        @else
                            Waiting for other parties to sign
                        @endif
                    </p>
                    <button
                        @click="{{ $hasAnySignature ? '$wire.closeModal()' : 'showLeaveConfirm = true' }}"
                        class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Close
                    </button>
                </div>
            </div>

            <x-contract-leave-confirm closeAction="$wire.closeModal()" />
        </div>

        {{-- Owner Signature Pad Modals --}}
        <x-inspection.signature-pad-modal
            :show="$showSignatureModal"
            title="Owner E-Signature"
            subtitle="Move-In Contract — Sign as property owner"
            signerName=""
            signerRole="Property Owner / Lessor"
            legalText="By clicking &quot;Apply Signature&quot;, I confirm that I have read and agree to all terms in this Move-In Contract. This electronic signature is legally binding under RA 8792 (Electronic Commerce Act of 2000)."
            wireCloseMethod="closeSignatureModal"
            wireSaveMethod="saveOwnerSignature"
            canvasRef="sigCanvasOwnerMoveIn"
        />

        <x-inspection.signature-pad-modal
            :show="$showMoveOutSignatureModal"
            title="Owner E-Signature"
            subtitle="Move-Out Contract — Sign as property owner"
            signerName=""
            signerRole="Property Owner / Lessor"
            legalText="By clicking &quot;Apply Signature&quot;, I confirm that I have read and agree to all terms in this Move-Out Clearance &amp; Deposit Settlement Agreement. This electronic signature is legally binding under RA 8792 (Electronic Commerce Act of 2000)."
            wireCloseMethod="closeMoveOutSignatureModal"
            wireSaveMethod="saveMoveOutOwnerSignature"
            canvasRef="sigCanvasOwnerMoveOut"
        />
    @endif
</div>
