<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Move-In Contract — {{ $tenant['personal_info']['first_name'] }} {{ $tenant['personal_info']['last_name'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000;
            background: #fff;
        }

        /* ── Page wrapper ── */
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 18mm 20mm 20mm 20mm;
            background: #fff;
            page-break-after: always;
            position: relative;
        }
        .page:last-child {
            page-break-after: auto;
        }

        /* ── Confidential banner ── */
        .confidential-banner {
            text-align: center;
            font-size: 7.5pt;
            color: #555;
            border-top: 0.5pt solid #999;
            border-bottom: 0.5pt solid #999;
            padding: 2px 0;
            margin-bottom: 10px;
            letter-spacing: 0.3px;
        }

        /* ── Document header (banner style) ── */
        .doc-header-banner {
            background: #1a2744;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 20px;
            margin-bottom: 4px;
            border-bottom: 3px solid #2360E8;
        }
        .doc-header-banner .banner-left .doc-title-main {
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #fff;
            margin: 0;
        }
        .doc-header-banner .banner-left .republic {
            font-size: 8.5pt;
            letter-spacing: 0.5px;
            color: #ccc;
            margin-top: 2px;
        }
        .doc-header-banner .banner-right {
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #fff;
        }
        .doc-header {
            text-align: center;
            margin-bottom: 14px;
        }
        .doc-header .page-label {
            font-size: 8.5pt;
            color: #666;
            margin-top: 2px;
        }

        /* ── Divider ── */
        .divider {
            border: none;
            border-top: 1.5pt solid #070589;
            margin: 8px 0;
        }
        .divider-thin {
            border: none;
            border-top: 0.5pt solid #ccc;
            margin: 6px 0;
        }

        /* ── Intro paragraph ── */
        .intro {
            font-size: 9.5pt;
            text-align: justify;
            line-height: 1.55;
            margin-bottom: 12px;
            color: #222;
        }

        /* ── Section heading ── */
        .section-heading {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #070589;
            background: #EEF2FF;
            padding: 4px 8px;
            margin: 14px 0 8px;
            border-left: 3px solid #2360E8;
        }

        /* ── Sub-heading (LESSOR / LESSEE) ── */
        .sub-heading {
            font-size: 9.5pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 8px 0 5px;
            color: #111;
        }

        /* ── Field rows ── */
        .field-row {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            margin-bottom: 6px;
            font-size: 9.5pt;
        }
        .field-row.two-col {
            flex-wrap: wrap;
            gap: 6px 20px;
        }
        .field-row.two-col > .field-row {
            min-width: 200px;
        }
        .field-label {
            white-space: nowrap;
            color: #333;
            flex-shrink: 0;
        }
        .field-value {
            border-bottom: 0.75pt solid #333;
            flex: 1;
            min-width: 80px;
            padding-bottom: 1px;
            font-weight: bold;
            color: #000;
            font-size: 9.5pt;
        }
        .field-value.fixed {
            flex: none;
            min-width: 160px;
        }
        .field-value.short {
            min-width: 100px;
            flex: none;
        }

        /* ── Payment table ── */
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 9pt;
        }
        .payment-table th {
            background: #070589;
            color: #fff;
            padding: 5px 8px;
            text-align: left;
            font-size: 8.5pt;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        .payment-table th.right,
        .payment-table td.right {
            text-align: right;
        }
        .payment-table td {
            padding: 5px 8px;
            border-bottom: 0.5pt solid #ddd;
            vertical-align: top;
            color: #111;
        }
        .payment-table td.desc {
            font-size: 8.5pt;
            color: #555;
        }
        .payment-table tr:nth-child(even) td {
            background: #f7f8ff;
        }
        .payment-table tr.total-row td {
            font-weight: bold;
            border-top: 1pt solid #070589;
            background: #EEF2FF;
            color: #070589;
        }
        .amount-value {
            font-weight: bold;
        }

        /* ── Bullet lists ── */
        .policy-list {
            margin: 6px 0 6px 18px;
            font-size: 9pt;
            line-height: 1.6;
            color: #222;
        }
        .policy-list li {
            margin-bottom: 3px;
        }

        /* ── Inspection table ── */
        .inspection-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin: 8px 0;
        }
        .inspection-table th {
            background: #070589;
            color: #fff;
            padding: 4px 6px;
            text-align: center;
            font-size: 8pt;
        }
        .inspection-table th:first-child {
            text-align: left;
        }
        .inspection-table td {
            padding: 5px 6px;
            border: 0.5pt solid #ccc;
            vertical-align: middle;
        }
        .inspection-table td.check-cell {
            text-align: center;
        }
        .inspection-table tr:nth-child(even) td {
            background: #f9f9ff;
        }

        /* ── Items received table ── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin: 8px 0;
        }
        .items-table th {
            background: #070589;
            color: #fff;
            padding: 4px 8px;
            font-size: 8pt;
            text-align: left;
        }
        .items-table td {
            padding: 5px 8px;
            border: 0.5pt solid #ccc;
        }
        .items-table tr:nth-child(even) td {
            background: #f9f9ff;
        }

        /* ── Signature block ── */
        .signature-section {
            margin-top: 24px;
        }
        .sig-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 20px;
        }
        .sig-block {
            text-align: center;
        }
        .sig-line {
            border-top: 1pt solid #000;
            margin-top: 42px;
            margin-bottom: 4px;
        }
        .sig-label {
            font-size: 8.5pt;
            color: #333;
            line-height: 1.4;
        }
        .sig-date-row {
            display: flex;
            align-items: flex-end;
            gap: 6px;
            margin-top: 8px;
            font-size: 9pt;
            justify-content: center;
        }
        .sig-date-line {
            border-bottom: 0.75pt solid #333;
            width: 120px;
        }

        /* ── Footer ── */
        .doc-footer {
            text-align: center;
            font-size: 7.5pt;
            color: #666;
            border-top: 0.5pt solid #ccc;
            padding-top: 5px;
            margin-top: 10px;
        }

        /* ── Penalty highlight ── */
        .penalty-note {
            font-size: 8.5pt;
            background: #fff8f0;
            border-left: 2.5pt solid #e67e22;
            padding: 5px 8px;
            margin: 6px 0;
            color: #333;
        }

        /* ── RA note ── */
        .law-note {
            font-size: 8.5pt;
            background: #f0f4ff;
            border-left: 2.5pt solid #2360E8;
            padding: 5px 8px;
            margin: 5px 0;
            color: #333;
        }

        /* ── Print-only styles ── */
        @media print {
            body { background: #fff; }
            .page {
                width: 210mm;
                margin: 0;
                padding: 15mm 18mm 18mm 18mm;
                page-break-after: always;
            }
            .page:last-child { page-break-after: auto; }
        }

        @media screen {
            body { background: #e0e4ef; padding: 20px 0; }
            .page { box-shadow: 0 2px 16px rgba(0,0,0,0.15); margin-bottom: 20px; }
        }
    </style>
</head>
<body>

@php
    $inspectionChecklist = $inspectionChecklist ?? [];
    $itemsReceived = $itemsReceived ?? [];
    $contractSettings = $contractSettings ?? [];

    $defaultInclusions = [
        'Association dues / condo or building fees',
        'Wi-Fi / Internet access',
        'Access to building amenities (pool, gym, function areas, etc.)',
        'Housekeeping / common-area cleaning',
        'Use of shared appliances',
        '24/7 building security',
        'Furnished room (bed, cabinet, air conditioning, etc.)',
        'Water utility',
    ];
    $defaultExclusions = [
        'Electricity (split equally among unit tenants)',
        'Water (if not included above)',
        'Laundry services',
        'Parking fees',
    ];
    $defaultHouseRules = [
        'No overnight visitors or unauthorized guests. Visitors must leave by the designated curfew time.',
        'No smoking inside the unit or building common areas.',
        'No illegal drugs, substances, or activities of any kind.',
        'No pets allowed within the premises unless explicitly permitted in writing.',
        'Observe quiet hours from 10:00 PM to 6:00 AM.',
        'No unauthorized room transfers, subletting, or sharing of assigned bed with another person.',
        'No tampering with air conditioning units, electrical systems, or building infrastructure.',
        'No unauthorized repairs, modifications, or do-it-yourself (DIY) maintenance. All maintenance concerns must be reported to the dormitory administration for proper handling.',
        'Report all maintenance issues to the dormitory administration promptly.',
        'Keep personal area and all shared spaces clean and orderly.',
        'Follow proper garbage disposal and recycling procedures.',
        'Respect fellow tenants\' privacy, belongings, and personal space.',
        'Comply with all building management rules and regulations.',
    ];
    $defaultPenalties = 'First offense — written warning. Second offense — fine of PHP 500.00. Third offense — grounds for lease termination with possible deposit forfeiture. Serious violations (illegal activity, property destruction) may result in immediate termination.';

    $inclusions = data_get($contractSettings, 'inclusions', $defaultInclusions);
    $exclusions = data_get($contractSettings, 'exclusions', $defaultExclusions);
    $houseRules = data_get($contractSettings, 'house_rules', $defaultHouseRules);
    $penaltySchedule = data_get($contractSettings, 'penalty_schedule', $defaultPenalties);

    $monthlyRate = $rate ?? 0;
    $secDeposit = $deposit ?? 0;
    $shortTermPremium = $premium ?? 0;
    $advance = $monthlyRate;
    $totalMoveIn = $monthlyRate + $shortTermPremium + $advance + $secDeposit;
@endphp


{{-- ═══════════════════════════════════════ PAGE 1 ═══════════════════════════════════════ --}}
<div class="page">

    {{-- Header banner --}}
    <div class="doc-header-banner">
        <div class="banner-left">
            <div class="doc-title-main">Dormitory Rental Agreement</div>
            <div class="republic">Republic of the Philippines</div>
        </div>
        <div class="banner-right">Move-In Contract</div>
    </div>
    <div class="confidential-banner">This document is confidential and intended solely for the parties named herein.</div>

    <p class="intro">
        This Move-In Contract ("Agreement") is entered into by and between the <strong>LESSOR</strong> (the dormitory owner or authorized operator) and the <strong>LESSEE</strong> (the tenant), under the terms and conditions set forth below, in compliance with <em>Republic Act No. 9653 (Rent Control Act of 2009)</em> and other applicable laws of the Republic of the Philippines.
    </p>

    {{-- SECTION 1 --}}
    <div class="section-heading">Section 1 — Parties to the Agreement</div>

    <div class="sub-heading">LESSOR (Dormitory Owner / Operator)</div>

    <div class="field-row">
        <span class="field-label">Business / Trade Name:</span>
        <span class="field-value">{{ $lessor['business_name'] ?? '' }}</span>
    </div>
    <div class="field-row">
        <span class="field-label">Registered Company Name:</span>
        <span class="field-value">{{ $lessor['company_name'] ?? '' }}</span>
    </div>
    <div class="field-row">
        <span class="field-label">Business Address:</span>
        <span class="field-value">{{ $lessor['address'] ?? '' }}</span>
    </div>
    <div class="field-row two-col">
        <div class="field-row" style="flex:1;">
            <span class="field-label">Contact Number:</span>
            <span class="field-value">{{ $lessor['contact'] ?? '' }}</span>
        </div>
        <div class="field-row" style="flex:1;">
            <span class="field-label">Email Address:</span>
            <span class="field-value">{{ $lessor['email'] ?? '' }}</span>
        </div>
    </div>
    <div class="field-row">
        <span class="field-label">Authorized Representative:</span>
        <span class="field-value">{{ $lessor['representative'] ?? '' }}</span>
    </div>

    <div class="sub-heading">LESSEE (Tenant)</div>

    <div class="field-row">
        <span class="field-label">Full Legal Name:</span>
        <span class="field-value">{{ $tenant['personal_info']['first_name'] }} {{ $tenant['personal_info']['last_name'] }}</span>
    </div>
    <div class="field-row">
        <span class="field-label">Permanent Home Address:</span>
        <span class="field-value">{{ $tenant['personal_info']['permanent_address'] ?? '' }}</span>
    </div>
    <div class="field-row two-col">
        <div class="field-row" style="flex:1;">
            <span class="field-label">Contact Number:</span>
            <span class="field-value">{{ $tenant['contact_info']['contact_number'] }}</span>
        </div>
        <div class="field-row" style="flex:1;">
            <span class="field-label">Email Address:</span>
            <span class="field-value">{{ $tenant['contact_info']['email'] }}</span>
        </div>
    </div>
    <div class="field-row two-col">
        <div class="field-row" style="flex:1;">
            <span class="field-label">Valid Government ID Type:</span>
            <span class="field-value">{{ $tenant['personal_info']['government_id_type'] ?? '' }}</span>
        </div>
        <div class="field-row" style="flex:1;">
            <span class="field-label">ID Number:</span>
            <span class="field-value">{{ $tenant['personal_info']['government_id_number'] ?? '' }}</span>
        </div>
    </div>
    <div class="field-row two-col">
        <div class="field-row" style="flex:1;">
            <span class="field-label">Company / School:</span>
            <span class="field-value">{{ $tenant['personal_info']['company_school'] ?? '' }}</span>
        </div>
        <div class="field-row" style="flex:1;">
            <span class="field-label">Position / Course:</span>
            <span class="field-value">{{ $tenant['personal_info']['position_course'] ?? '' }}</span>
        </div>
    </div>

    <div class="sub-heading">Emergency Contact Person</div>

    <div class="field-row two-col">
        <div class="field-row" style="flex:1;">
            <span class="field-label">Full Name:</span>
            <span class="field-value">{{ $tenant['personal_info']['emergency_contact_name'] ?? '' }}</span>
        </div>
        <div class="field-row" style="flex:1;">
            <span class="field-label">Relationship to Tenant:</span>
            <span class="field-value">{{ $tenant['personal_info']['emergency_contact_relationship'] ?? '' }}</span>
        </div>
    </div>
    <div class="field-row">
        <span class="field-label">Contact Number:</span>
        <span class="field-value">{{ $tenant['personal_info']['emergency_contact_number'] ?? '' }}</span>
    </div>

    {{-- SECTION 2 --}}
    <div class="section-heading">Section 2 — Property Details</div>

    <div class="field-row">
        <span class="field-label">Building / Property Name:</span>
        <span class="field-value">{{ $tenant['personal_info']['property'] }}</span>
    </div>
    <div class="field-row">
        <span class="field-label">Complete Address:</span>
        <span class="field-value">{{ $tenant['personal_info']['address'] }}</span>
    </div>
    <div class="field-row two-col">
        <div class="field-row" style="flex:1;">
            <span class="field-label">Unit / Room Number:</span>
            <span class="field-value">{{ $tenant['personal_info']['unit'] }}</span>
        </div>
        <div class="field-row" style="flex:1;">
            <span class="field-label">Floor:</span>
            <span class="field-value">{{ $tenant['rent_details']['floor'] ?? '' }}</span>
        </div>
    </div>

    <hr class="divider-thin" style="margin-top:auto;">
    <div class="doc-footer">DORMITORY RENTAL AGREEMENT &nbsp;|&nbsp; Republic of the Philippines &nbsp;|&nbsp; MOVE-IN CONTRACT</div>
</div>


{{-- ═══════════════════════════════════════ PAGE 2 ═══════════════════════════════════════ --}}
<div class="page">

    <div class="doc-header-banner">
        <div class="banner-left">
            <div class="doc-title-main">Dormitory Rental Agreement</div>
            <div class="republic">Republic of the Philippines | Move-In Contract</div>
        </div>
        <div class="banner-right">Page 2</div>
    </div>
    <div class="confidential-banner">This document is confidential and intended solely for the parties named herein.</div>

    <div class="field-row two-col" style="margin-top:10px;">
        <div class="field-row" style="flex:1;">
            <span class="field-label">Bed Assignment (if applicable):</span>
            <span class="field-value">{{ $tenant['rent_details']['bed_number'] }}</span>
        </div>
        <div class="field-row" style="flex:1;">
            <span class="field-label">Room Type:</span>
            <span class="field-value">{{ $tenant['rent_details']['room_type'] ?? '' }}</span>
        </div>
    </div>
    <div class="field-row">
        <span class="field-label">Gender Policy (Male-Only / Female-Only / Co-ed):</span>
        <span class="field-value">{{ $tenant['rent_details']['dorm_type'] }}</span>
    </div>

    {{-- SECTION 3 --}}
    <div class="section-heading">Section 3 — Lease Term</div>

    <div class="field-row two-col">
        <div class="field-row" style="flex:1;">
            <span class="field-label">Lease Start Date:</span>
            <span class="field-value">{{ \Carbon\Carbon::parse($tenant['rent_details']['lease_start_date'])->format('F d, Y') }}</span>
        </div>
        <div class="field-row" style="flex:1;">
            <span class="field-label">Lease End Date:</span>
            <span class="field-value">{{ \Carbon\Carbon::parse($tenant['rent_details']['lease_end_date'])->format('F d, Y') }}</span>
        </div>
    </div>
    <div class="field-row">
        <span class="field-label">Minimum Stay Period:</span>
        <span class="field-value">{{ $tenant['rent_details']['lease_term'] }} month(s)</span>
    </div>

    <p style="font-size:8.5pt; color:#333; line-height:1.55; margin:6px 0 0;">
        This is a fixed-term lease. The lease shall automatically expire on the Lease End Date stated above. No separate notice to vacate is required for normal end-of-lease move-outs. If the Lessee wishes to renew, both parties must execute a new Agreement in writing before the Lease End Date. For early termination (moving out before the Lease End Date), refer to Section 7 of this Agreement.
    </p>

    {{-- SECTION 4 --}}
    <div class="section-heading">Section 4 — Rent and Payment Terms</div>

    <table class="payment-table">
        <thead>
        <tr>
            <th>Item Description</th>
            <th>Details</th>
            <th class="right">Amount (PHP)</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><strong>Monthly Rent</strong></td>
            <td class="desc">Base monthly rate for the assigned bed / room</td>
            <td class="right amount-value">&#8369; {{ number_format($monthlyRate, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Short-Term Premium</strong> <span style="font-size:7.5pt;color:#888;">(if applicable)</span></td>
            <td class="desc">PHP 500/month — automatically applied when lease term is below 6 months</td>
            <td class="right amount-value">{{ $shortTermPremium > 0 ? '&#8369; ' . number_format($shortTermPremium, 2) : '—' }}</td>
        </tr>
        <tr>
            <td><strong>1 Month Advance</strong></td>
            <td class="desc">Covers the first month of occupancy</td>
            <td class="right amount-value">&#8369; {{ number_format($advance, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Security Deposit</strong> <span style="font-size:7.5pt;color:#888;">(max 2 months per RA 9653)</span></td>
            <td class="desc">Refundable upon move-out, subject to inspection and clearance</td>
            <td class="right amount-value">&#8369; {{ number_format($secDeposit, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td colspan="2"><strong>TOTAL MOVE-IN COST</strong></td>
            <td class="right"><strong>&#8369; {{ number_format($totalMoveIn, 2) }}</strong></td>
        </tr>
        </tbody>
    </table>

    <div class="field-row two-col" style="margin-top:8px;">
        <div class="field-row" style="flex:1;">
            <span class="field-label">Monthly Due Date:</span>
            <span class="field-value">{{ $dueDay ? $dueDay . $dueSfx . ' of the month' : '—' }}</span>
        </div>
        <div class="field-row" style="flex:1;">
            <span class="field-label">Accepted Payment Methods:</span>
            <span class="field-value">{{ data_get($contractSettings, 'payment_methods', 'GCash, Maya, Bank Transfer, Cash') }}</span>
        </div>
    </div>

    <div class="penalty-note">
        <strong>Short-Term Premium:</strong> A fixed charge of PHP 500.00 per month is automatically applied when the lease term is below six (6) months. This will be reflected in the monthly billing statement.
    </div>

    <div class="penalty-note">
        <strong>Late Payment Penalty:</strong> <strong>{{ $tenant['move_in_details']['late_payment_penalty'] ?? 1 }}%</strong> of the monthly rent per day of delay shall be automatically computed and applied to any rent payment received after the monthly due date. The total late payment penalty is capped at a maximum of 25% of the monthly rent.
    </div>

    <div class="law-note">
        Under RA 9653, the Lessor cannot demand more than one (1) month advance rent and two (2) months' security deposit. The security deposit shall be placed in a bank account under the Lessor's name. Interest earned shall be returned to the Lessee upon lease expiration. The security deposit shall <strong>NOT</strong> be applied as monthly rent during the lease term — it is refundable only upon move-out after inspection and clearance. Utility charges not included in the base rent shall be billed separately, split equally among tenants, and prorated for mid-month move-ins.
    </div>

    {{-- SECTION 4A --}}
    <div class="section-heading">Section 4A — Payment Before Occupancy</div>

    <p style="font-size:9pt; color:#222; line-height:1.6; text-align:justify;">
        There is no reservation fee. The full move-in payment (1 month advance + security deposit) must be completed before the Lessee is assigned to a bed and allowed to occupy the premises. No move-in shall proceed without confirmed payment. This is in accordance with RA 9653, Section 6, which allows the Lessor to require advance rent and deposit to be paid in advance of occupancy.
    </p>

    <hr class="divider-thin">
    <div class="doc-footer">DORMITORY RENTAL AGREEMENT &nbsp;|&nbsp; Republic of the Philippines &nbsp;|&nbsp; MOVE-IN CONTRACT</div>
</div>


{{-- ═══════════════════════════════════════ PAGE 3 ═══════════════════════════════════════ --}}
<div class="page">

    <div class="doc-header-banner">
        <div class="banner-left">
            <div class="doc-title-main">Dormitory Rental Agreement</div>
            <div class="republic">Republic of the Philippines | Move-In Contract</div>
        </div>
        <div class="banner-right">Page 3</div>
    </div>
    <div class="confidential-banner">This document is confidential and intended solely for the parties named herein.</div>

    {{-- SECTION 5 --}}
    <div class="section-heading" style="margin-top:10px;">Section 5 — Rent Inclusions and Exclusions</div>

    <p style="font-size:9pt; margin-bottom:5px; color:#222;">The following items are <strong>included</strong> in the monthly rent:</p>
    <ul class="policy-list">
        @foreach($inclusions as $item)<li>{{ $item }}</li>@endforeach
    </ul>

    <hr class="divider-thin">

    <p style="font-size:9pt; margin: 6px 0 5px; color:#222;">The following items are <strong>NOT included</strong> and will be billed separately:</p>
    <ul class="policy-list">
        @foreach($exclusions as $item)<li>{{ $item }}</li>@endforeach
    </ul>

    {{-- SECTION 6 --}}
    <div class="section-heading">Section 6 — House Rules and Policies</div>

    <p style="font-size:9pt; margin-bottom:4px; color:#222;">The Lessee agrees to abide by the following rules at all times:</p>
    <ul class="policy-list">
        @foreach($houseRules as $rule)<li>{{ $rule }}</li>@endforeach
    </ul>

    <div class="penalty-note">
        <strong>Violation Penalties:</strong> {{ $penaltySchedule }}
    </div>

    {{-- SECTION 7 --}}
    <div class="section-heading">Section 7 — Early Termination</div>

    <p style="font-size:9pt; margin-bottom:4px; color:#222;">Early termination means the Lessee vacates before the Lease End Date specified in Section 3. If early termination occurs, the following shall apply:</p>
    <ul class="policy-list">
        <li>The Lessee must provide a minimum of thirty (30) calendar days' written notice of intent to vacate early.</li>
        <li>The security deposit shall be automatically forfeited in full as liquidated damages.</li>
        <li>Any outstanding utility balances, unpaid rent, and other charges must be settled in full before vacating.</li>
        <li>No additional early termination fee shall be charged beyond the deposit forfeiture.</li>
    </ul>

    <hr class="divider-thin">
    <div class="doc-footer">DORMITORY RENTAL AGREEMENT &nbsp;|&nbsp; Republic of the Philippines &nbsp;|&nbsp; MOVE-IN CONTRACT</div>
</div>


{{-- ═══════════════════════════════════════ PAGE 4 ═══════════════════════════════════════ --}}
<div class="page">

    <div class="doc-header-banner">
        <div class="banner-left">
            <div class="doc-title-main">Dormitory Rental Agreement</div>
            <div class="republic">Republic of the Philippines | Move-In Contract</div>
        </div>
        <div class="banner-right">Page 4</div>
    </div>
    <div class="confidential-banner">This document is confidential and intended solely for the parties named herein.</div>

    {{-- SECTION 8 --}}
    <div class="section-heading" style="margin-top:10px;">Section 8 — Move-In Room Condition Checklist</div>

    <p style="font-size:8.5pt; color:#333; margin-bottom:6px; line-height:1.5;">
        Both parties shall inspect the room on the move-in date and record the condition of each item below. This checklist serves as the baseline for the move-out inspection.
    </p>

    <table class="inspection-table">
        <thead>
        <tr>
            <th style="width:38%;">Item</th>
            <th style="width:12%;">Good</th>
            <th style="width:12%;">Damaged</th>
            <th style="width:12%;">Missing</th>
            <th>Remarks</th>
        </tr>
        </thead>
        <tbody>
        @forelse($inspectionChecklist as $item)
            <tr>
                <td>{{ $item['item_name'] }}</td>
                <td class="check-cell">{{ ($item['condition'] ?? '') === 'good' ? '✓' : '' }}</td>
                <td class="check-cell">{{ ($item['condition'] ?? '') === 'damaged' ? '✓' : '' }}</td>
                <td class="check-cell">{{ ($item['condition'] ?? '') === 'missing' ? '✓' : '' }}</td>
                <td style="font-size:7.5pt; color:#555;">{{ $item['remarks'] ?? '' }}</td>
            </tr>
        @empty
            <tr><td colspan="5" style="text-align:center; color:#999; padding:10px;">No inspection data recorded.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{-- SECTION 9 --}}
    <div class="section-heading">Section 9 — Items Received by Tenant</div>

    <table class="items-table">
        <thead>
        <tr>
            <th style="width:40%;">Item</th>
            <th style="width:10%;">Qty</th>
            <th style="width:25%;">Condition</th>
            <th>Confirmed</th>
        </tr>
        </thead>
        <tbody>
        @forelse($itemsReceived as $item)
            <tr>
                <td>{{ $item['item_name'] }}</td>
                <td>{{ $item['quantity'] ?: '' }}</td>
                <td>{{ $item['condition'] ?? '' }}</td>
                <td style="text-align:center;">{{ ($item['tenant_confirmed'] ?? false) ? '✓' : '' }}</td>
            </tr>
        @empty
            <tr><td colspan="4" style="text-align:center; color:#999; padding:10px;">No items recorded.</td></tr>
        @endforelse
        </tbody>
    </table>

    <hr class="divider-thin">
    <div class="doc-footer">DORMITORY RENTAL AGREEMENT &nbsp;|&nbsp; Republic of the Philippines &nbsp;|&nbsp; MOVE-IN CONTRACT</div>
</div>


{{-- ═══════════════════════════════════════ PAGE 5 ═══════════════════════════════════════ --}}
<div class="page">

    <div class="doc-header-banner">
        <div class="banner-left">
            <div class="doc-title-main">Dormitory Rental Agreement</div>
            <div class="republic">Republic of the Philippines | Move-In Contract</div>
        </div>
        <div class="banner-right">Page 5</div>
    </div>
    <div class="confidential-banner">This document is confidential and intended solely for the parties named herein.</div>

    {{-- SECTION 10 --}}
    <div class="section-heading" style="margin-top:10px;">Section 10 — Billing and Payments During Active Tenancy</div>

    <p style="font-size:9pt; margin-bottom:4px; color:#222;">During the active lease period, the following billing rules shall apply:</p>
    <ul class="policy-list">
        <li>A monthly billing statement shall be generated and issued to the Lessee on or before the 1st of each month, showing all charges due for the current period.</li>
        <li>The billing statement shall include the base monthly rent, electricity share, water share, short-term premium (if applicable at PHP 500/month for leases under 6 months), and any conditional charges.</li>
        <li>Electricity and water utility charges shall be computed by dividing the total unit bill equally among all active tenants in the room. Mid-month move-ins shall be prorated by the number of days occupied.</li>
        <li>Late Payment Penalty: {{ $tenant['move_in_details']['late_payment_penalty'] ?? 1 }}% of the monthly rent per day shall be automatically computed and added to the next billing statement for any payment received after the monthly due date. The total late payment penalty is capped at a maximum of 25% of the monthly rent.</li>
        <li>A payment confirmation with a reference number shall be generated upon confirmed payment.</li>
        <li>Accepted payment methods, payment history, and downloadable receipts shall be made available to the Lessee.</li>
    </ul>

    {{-- SECTION 11 --}}
    <div class="section-heading">Section 11 — Lease Renewal</div>

    <p style="font-size:9pt; color:#222; line-height:1.6; text-align:justify;">
        The Lessor shall send a renewal notice to the Lessee at least thirty (30) days before the Lease End Date. The notice shall include any rate adjustments, which shall not exceed the maximum increase allowed under RA 9653. If the Lessee confirms renewal, a new contract shall be signed with updated terms. If the Lessee does not wish to renew, the lease simply expires on the Lease End Date and the move-out process (Section 12) applies.
    </p>

    {{-- SECTION 12 --}}
    <div class="section-heading">Section 12 — Move-Out Inspection and Clearance</div>

    <p style="font-size:9pt; margin-bottom:4px; color:#222;">This section applies when the lease ends on the Lease End Date (normal expiration) or after the 30-day notice period for early termination (Section 7).</p>

    <p style="font-size:9pt; font-weight:bold; color:#222; margin: 6px 0 3px;">Move-Out Inspection:</p>
    <ul class="policy-list">
        <li>The Lessor and Lessee shall conduct a joint room inspection, comparing current conditions against the move-in checklist and photos (Section 8).</li>
        <li>Any damages beyond normal wear and tear shall be documented and the repair cost deducted from the security deposit.</li>
        <li>The Lessee must return all keys, access cards, and borrowed items.</li>
        <li>The Lessor shall sign off on a clearance form upon satisfactory completion.</li>
    </ul>

    <p style="font-size:9pt; font-weight:bold; color:#222; margin: 6px 0 3px;">Deposit Refund (Normal End of Lease Only):</p>
    <ul class="policy-list">
        <li>The deposit refund shall be calculated as: Original Deposit – Unpaid Utility Balance – Damage Repair Costs – Lost Key/Card Replacement + Interest Earned = Net Deposit Refund.</li>
        <li>If the Lessee terminated early (Section 7), the deposit is forfeited in full and no refund applies.</li>
        <li>Refund shall be processed within thirty (30) days after move-out and clearance.</li>
        <li>Refund shall be issued via the same payment method used or via bank transfer.</li>
    </ul>

    {{-- SECTION 13 --}}
    <div class="section-heading">Section 13 — Governing Law and Dispute Resolution</div>

    <p style="font-size:9pt; color:#222; line-height:1.6; text-align:justify;">
        This Agreement shall be governed by and construed in accordance with the laws of the Republic of the Philippines, including but not limited to <em>Republic Act No. 9653 (Rent Control Act of 2009)</em> and its implementing rules. Any dispute arising from this Agreement shall first be settled through amicable negotiation. Should negotiation fail, the parties agree to seek mediation through the Barangay where the property is located, and thereafter through the proper courts of competent jurisdiction.
    </p>

    <hr class="divider-thin">
    <div class="doc-footer">DORMITORY RENTAL AGREEMENT &nbsp;|&nbsp; Republic of the Philippines &nbsp;|&nbsp; MOVE-IN CONTRACT</div>
</div>


{{-- ═══════════════════════════════════════ PAGE 6 ═══════════════════════════════════════ --}}
<div class="page">

    <div class="doc-header-banner">
        <div class="banner-left">
            <div class="doc-title-main">Dormitory Rental Agreement</div>
            <div class="republic">Republic of the Philippines | Move-In Contract</div>
        </div>
        <div class="banner-right">Page 6</div>
    </div>
    <div class="confidential-banner">This document is confidential and intended solely for the parties named herein.</div>

    {{-- SECTION 14 --}}
    <div class="section-heading" style="margin-top:10px;">Section 14 — Agreement and Signatures</div>

    <p style="font-size:9pt; color:#222; line-height:1.55; margin-bottom:8px; text-align:justify;">
        By signing below, all parties acknowledge that they have read, understood, and voluntarily agree to all terms and conditions stated in this Move-In Contract. All parties confirm that all information provided herein is true, correct, and complete.
    </p>

    <div class="signature-section">
        @if(!empty($tenantSignatureBase64) && !empty($ownerSignatureBase64) && !empty($managerSignatureBase64))
            {{-- E-Signature verification banner --}}
            <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:8px 12px; margin-bottom:14px; text-align:center;">
                <span style="font-size:8pt; color:#166534; font-weight:bold;">ELECTRONICALLY SIGNED — RA 8792 COMPLIANT</span>
            </div>
        @endif

        {{-- Owner and Tenant signatures --}}
        <div class="sig-grid">
            <div class="sig-block">
                @if(!empty($ownerSignatureBase64))
                    <div style="height:60px; display:flex; align-items:center; justify-content:center; margin-bottom:4px;">
                        <img src="{{ $ownerSignatureBase64 }}" style="max-height:55px; max-width:100%;" alt="Owner Signature">
                    </div>
                @else
                    <div class="sig-line"></div>
                @endif
                <div class="sig-label">
                    <strong>{{ $lessor['representative'] ?? '' }}</strong><br>
                    Lessor / Property Owner
                </div>
                <div class="sig-date-row">
                    <span>Date:</span>
                    @if(!empty($ownerSignedAt))
                        <span style="font-size:8.5pt; margin-left:4px;">{{ $ownerSignedAt }}</span>
                    @else
                        <div class="sig-date-line"></div>
                    @endif
                </div>
            </div>
            <div class="sig-block">
                @if(!empty($tenantSignatureBase64))
                    <div style="height:60px; display:flex; align-items:center; justify-content:center; margin-bottom:4px;">
                        <img src="{{ $tenantSignatureBase64 }}" style="max-height:55px; max-width:100%;" alt="Tenant Signature">
                    </div>
                @else
                    <div class="sig-line"></div>
                @endif
                <div class="sig-label">
                    <strong>{{ $tenant['personal_info']['first_name'] }} {{ $tenant['personal_info']['last_name'] }}</strong><br>
                    Tenant / Lessee
                </div>
                <div class="sig-date-row">
                    <span>Date:</span>
                    @if(!empty($tenantSignedAt))
                        <span style="font-size:8.5pt; margin-left:4px;">{{ $tenantSignedAt }}</span>
                    @else
                        <div class="sig-date-line"></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Manager Witness signature --}}
        <p style="font-size:9pt; color:#333; margin: 16px 0 8px; font-weight:bold;">Witnessed by:</p>
        <div class="sig-grid">
            <div class="sig-block">
                @if(!empty($managerSignatureBase64))
                    <div style="height:60px; display:flex; align-items:center; justify-content:center; margin-bottom:4px;">
                        <img src="{{ $managerSignatureBase64 }}" style="max-height:55px; max-width:100%;" alt="Manager Witness Signature">
                    </div>
                @else
                    <div class="sig-line"></div>
                @endif
                <div class="sig-label">
                    <strong>{{ $managerName ?? 'Unit Manager' }}</strong><br>
                    Unit Manager / Witness
                </div>
                <div class="sig-date-row">
                    <span>Date:</span>
                    @if(!empty($managerSignedAt))
                        <span style="font-size:8.5pt; margin-left:4px;">{{ $managerSignedAt }}</span>
                    @else
                        <div class="sig-date-line"></div>
                    @endif
                </div>
            </div>
        </div>

        <p style="font-size:8.5pt; color:#555; text-align:center; margin-top:18px; font-style:italic;">
            This Agreement is executed in two (2) original copies — one for the Lessor and one for the Lessee.
        </p>
    </div>

    {{-- APPENDIX --}}
    <div style="margin-top:24px; border-top:1.5pt solid #070589; padding-top:12px;">
        <div class="section-heading">Appendix — Tenant Valid ID</div>
        <p style="font-size:8.5pt; color:#555; margin-bottom:8px;">Attached copy of the tenant's government-issued identification.</p>

        @if($govIdBase64 ?? null)
            <div style="text-align:center; margin-bottom:8px;">
                <img src="{{ $govIdBase64 }}" style="max-width:80%; max-height:200px; border:1px solid #ccc; border-radius:4px;" alt="Tenant Valid ID">
            </div>
            <div style="text-align:center; font-size:8.5pt; color:#333;">
                <p><strong>ID Type:</strong> {{ $tenant['personal_info']['government_id_type'] ?? '—' }}</p>
                <p><strong>ID Number:</strong> {{ $tenant['personal_info']['government_id_number'] ?? '—' }}</p>
                <p><strong>Name:</strong> {{ $tenant['personal_info']['first_name'] }} {{ $tenant['personal_info']['last_name'] }}</p>
            </div>
        @else
            <p style="text-align:center; font-size:9pt; color:#999; padding:20px 0;">No ID image uploaded.</p>
        @endif
    </div>

    <hr class="divider-thin" style="margin-top:16px;">
    <div class="doc-footer">DORMITORY RENTAL AGREEMENT &nbsp;|&nbsp; Republic of the Philippines &nbsp;|&nbsp; MOVE-IN CONTRACT</div>
</div>

</body>
</html>
