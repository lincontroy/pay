@extends('layouts.backend.app')

@section('title', 'Confirm Withdrawal')

@section('head')
    @include('layouts.backend.partials.headersection',['title'=>'Confirm withdrawal'])
@endsection

@section('content')
@if (\Session::has('success'))
    <div class="alert alert-success">
        <ul>
            <li>{!! \Session::get('success') !!}</li>
        </ul>
    </div>
@endif

@if (\Session::has('error'))
    <div class="alert alert-warning">
        <ul>
            <li>{!! \Session::get('error') !!}</li>
        </ul>
    </div>
@endif
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card">
                    <form method="POST" action="{{ url('merchant/withdraw/confirm') }}">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label
                                    class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Code') }} </label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="number" class="form-control" name="code"
                                        value="{{ old('amount') ? old('amount') : $data->company_name ?? null }}" required>
                                </div>
                            </div>
                            
                           
                                <input type="hidden" value="{{ request()->ref }}" name="ref">
                            
                          
                          
                         
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                <div class="col-sm-12 col-md-7">
                                    <button class="btn btn-primary basicbtn btn-lg w-100"
                                        type="submit">{{ __('Submit') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
