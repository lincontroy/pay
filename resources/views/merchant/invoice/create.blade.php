@extends('layouts.backend.app')

@section('title', 'Create Invoice')

@section('head')
    @include('layouts.backend.partials.headersection',['title'=>'Create Invoice'])
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
                    <form method="POST" action="{{ url('merchant/invoice/post') }}">
                        @csrf
                        <div class="card-body">

                            <div class="form-group row mb-4">
                                <label
                                    class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Invoice type') }}</label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control">
                                        <option value="basic">Basic Invoice</option>
                                    </select>

                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label
                                    class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Customer email') }}</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="email" class="form-control" name="c_email"
                                        value="" required>
                                </div>
                            </div>
                           
                          <div class="form-group row mb-4">
                                <label
                                    class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Currency') }}</label>
                                <div class="col-sm-12 col-md-7" name="status">
                                    <select class="form-control" name="currency">
                                        <option value="usd">USD</option>
                                        <option value="kes">KES</option>
                                        <option value="tzs">TZS</option>
                                        <option value="ugx">UGX</option>

                                        

                                    </select>
                                </div>
                            </div>
                            
                             <div class="form-group row mb-4">
                                <label
                                    class="col-form-label text-md-right col-12 col-md-3 col-lg-3">{{ __('Amount') }}</label>
                                <div class="col-sm-12 col-md-7" >
                                    <input type="text" name="amount" class="form-control" required>
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
