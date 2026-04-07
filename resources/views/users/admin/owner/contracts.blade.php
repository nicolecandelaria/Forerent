@extends('layouts.app')

@section('header-title', 'CONTRACTS')
@section('header-subtitle', 'View and manage all tenant contracts')

@section('content')

    @include('livewire.layouts.dashboard.admingreeting')

    <livewire:layouts.contracts.contracts-overview />

@endsection
