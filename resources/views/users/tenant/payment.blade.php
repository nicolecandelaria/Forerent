@extends('layouts.app')

@section('header-title', 'PAYMENT DOCUMENTATION')
@section('header-subtitle', 'Access payment history and document new receipts')

@section('content')

    @include('livewire.layouts.dashboard.admingreeting')
    <livewire:layouts.tenants.payment-history />
    <livewire:layouts.financials.payment-receipt-modal />

@endsection
