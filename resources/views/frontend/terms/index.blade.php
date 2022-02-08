@extends('layouts.frontend.app')

@section('slider')
<div class="slider-breadcrumb-area text-center">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 offset-lg-3">
                <div class="breadcrumb-title">
                    <h4>{{ __('Terms of service') }}</h4>
                </div>
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="/">{{ __('Terms') }}</a></li>
                        <!--<li class="breadcrumb-item active" aria-current="page">{{ __('Service Details') }}</li>-->
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- breadcrumb area start -->
</div>
@endsection

@section('content')

 <!-- blog area start -->
 <div class="blog-main-area mt-100 mb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="service-content text-center">
                    <div class="service-img">
                       
                    </div>
                    <div class="service-name mb-3">
                        <h2>Terms of service</h2>
                    </div>
                    <div class="service-des">
                        <p>Lifegeegs  (Lifegeegs, “Company”, “we”, “us” or “our”) offers an online payment platform that allows customers to make seamless, stress-free payments online for desired goods and services. This Privacy and Cookie Policy (“Privacy Policy”) describes how we collect, use, store, share, and protect personal information received from website visitors, customers, and/or vendors/service Providers (“Data Subjects”) who engage with us. It applies to our website and all related sites, applications, services and tools (collectively, our “Services”). We therefore implement business practices that comply with the Protection of Personal Information Act 4 of 2013 (“POPIA”). This Privacy Policy applies to all processing of personal information.

Where we refer to "Personal Information" in this Privacy Policy, we mean personal information as defined in POPIA, being information that may be used to identify you directly or indirectly. Personal Information includes, for example, name, surname, email address, contact details, and location.

Our Services are primarily intended for and provided to businesses and other organisations (“Merchants”). Thus, we generally process personal information of website visitors, customers, and/or vendors at the direction of and on behalf of Merchants. When we do, we do so as a service provider or an “Operator” or “Data Processor” to those Merchants, but we do not control and are not responsible for the privacy practices of those Merchants. If you are a customer of a Lifegeegs Merchant, you should read that Merchant’s Privacy Policy and direct any privacy inquiries to that Merchant. If you are a Merchant, please see the Merchant Privacy Policy.

This Privacy Policy does not apply to services that are not owned or controlled by Lifegeegs, including third-party websites and the services of Lifegeegs Merchants. This Privacy Policy applies to all forms of systems, operations and processes within the Lifegeegs environment that involve the processing of personal information. 

By using or accessing our Services, you agree to the collection, use, and disclosure of your personal information as described in this Privacy Policy. Your use of our Services is also subject to Lifegeeg’s Terms.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- blog area end -->
@endsection