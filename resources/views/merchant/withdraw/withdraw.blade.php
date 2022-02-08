@extends('layouts.backend.app')

@section('title', 'Create Withdrawal')

@section('head')
    @include('layouts.backend.partials.headersection',['title'=>'Create withdrawal'])
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
                    <form method="POST" action="{{ url('merchant/withdraw/post') }}">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row mb-4">
                                <label
                                    class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Amount') }} ({{Auth::user()->currency}})</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="number" class="form-control" name="amount"
                                        value="{{ old('amount') ? old('amount') : $data->company_name ?? null }}" required>
                                </div>
                            </div>
                           
                          <div class="form-group row mb-4">
                                <label
                                    class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Bank Name / Paybill') }}</label>
                                <div class="col-sm-12 col-md-7" name="status">
                                    <input type="text" name="bankname" required class="form-control" id="w3review" 
                                        {{ $data->callback ?? null }} >
                                </div>
                            </div>
                            
                             <div class="form-group row mb-4">
                                <label
                                    class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Account Number') }}</label>
                                <div class="col-sm-12 col-md-7" >
                                    <input type="text" name="bankaccount" class="form-control" required>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-4">
                                <label
                                    class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Description(if any)') }}</label>
                                <div class="col-sm-12 col-md-7" name="status">
                                    <textarea required  class="form-control" name="description" 
                                        {{ $data->callback ?? null }}>
                                    
                                    </textarea>
                                </div>
                            </div>
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
