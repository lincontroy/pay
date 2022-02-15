@extends('layouts.backend.app')

@section('title','Send emails to merchants')

@section('head')
    @include('layouts.backend.partials.headersection',['title'=>'Email Compose','button_name'=> 'All Merchants','button_link'=> route('admin.merchant.index')])
@endsection

@push('before_css')
    <link rel="stylesheet" href="{{ asset('backend/admin/assets/css/selectric.css') }}">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>{{ __('Create email to all merchants') }}</h4>
            </div>
            <form method="POST" action="{{ url('admin/sendemail/post') }}" class="basicform_with_reset" >
                @csrf
                <div class="card-body">
                    <div class="form-row">
                        <div class="col-md-12 col-lg-12 col-sm-12">
                            <div class="form-group">
                                <label>{{ __('Subject') }}<sup>*</sup></label>
                                <input type="text" class="form-control" name="subject">
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-12 col-sm-12">
                            <div class="form-group">
                                <label>{{ __('Body') }} <sup>*</sup></label>
                                <textarea name="body" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                      
               
                  
                    
                    <div class="row">
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-primary btn-lg basicbtn">{{ __('Submit') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script src="{{ asset('backend/admin/assets/js/jquery.selectric.min.js') }}"></script>
    <script src="{{ asset('backend/admin/assets/js/selectric.js') }}"></script>
@endpush