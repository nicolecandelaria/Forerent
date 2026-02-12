@extends('layouts.guest')
@section('content')
<div class="flex min-h-screen bg-white relative z-50">

    {{-- Left Side: Login Form --}}
    <div class="w-full md:w-1/2 flex flex-1 flex-col justify-center p-8 sm:p-12 bg-white">
        <livewire:actions.login-form />
    </div>

    

</div>
@endsection
