@extends('layouts.docs.app')

@section('content')
<div class="dashboard-main-area">
    <div class="dashboard-title">
        <h2>{{ __('Payments through API (Demo with Postman)') }}</h2>
    </div>
    <div class="postman-route-type">
        <h5><span class="route-type">POST</span> generate auto payment with STK feature</h5>
        <p>Our aim is to make this experience as simple as possible</p>
    </div>
    <div class="route-url-link">
        <p>{{ url('/') }}/api/request</p>
    </div>
    <div class="postman-request-data-send-card">
        <div class="request-data-header">
            <span>Headers</span>
        </div>
        <div class="request-data-body">
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <td>Accept</td>
                        <td>application/json</td>
                    </tr>
                    <tr>
                        <td>Content-Type</td>
                        <td>application/json</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="postman-request-data-send-card">
        <div class="request-data-header">
            <span>BODY <span class="small">formdata</span></span>
        </div>
        <div class="request-data-body">
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <td>private_key</td>
                        <td>Merchant_private_key</td>
                    </tr>
                    <tr>
                        <td>currency</td>
                        <td>Merchant_currency</td>
                    </tr>
                    <tr>
                        <td>is_callback (Ensure this is set on the settings page)</td>
                        <td>Accept(1 OR 0) <ul><li>1 = It will return callback URl.</li><li>0 = It won't return callback URl.</li></ul></td>
                    </tr>
                    <tr>
                        <td>callback_url</td>
                        <td>http://yourdomain.com/status <ul><li>is_fallback = 1 then it will require.</li></ul></td>
                    </tr>
                    <tr>
                        <td>is_test</td>
                        <td>Accept(1 OR 0) <ul><li>1 = Sandbox Mode</li><li>0 = Live Mode</li></ul></td>
                    </tr>
                    <tr>
                        <td>Mobile number</td>
                        <td>Phone number that is registered with safaricom. This required for KES currency</td>
                    </tr>
                    
                    <tr>
                        <td>Head office</td>
                        <td>This is a short abbreviation of your company, e.g Lgs for Lifegeegs
                       </td>
                    </tr>
                    <tr>
                        <td>amount</td>
                        <td> All requests with KES as merchant currency need to have a maximum of Kes 50</td>
                    </tr>
                    <tr>
                        <td>purpose</td>
                        <td>testing purpose</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="dashboard-des">
        <p>{{ __('In body add private_key,currency,is_callback,url,is_test,amount as key and their values from the credentials of merchant profile.') }}</p>
    </div>
    <div class="main-container-area">
        <div class="step-area mt-5">
            <div class="step-body">
                <div class="step-img">
                    
                    <img class="img-fluid" src="https://lifegeegs.com/admin/res.png" alt=""><br><br><br>
                    
                    <h3>PHP Implementation for Stk push:</h3>
                    
                    <script src="https://gist.github.com/lincontroy/4a6d807164d081a3f8b6abb9e6278787.js"></script>
                    
                    <hr>
                    <h4>Node js Implementation for stk push</h4>
                    
                    <script src="https://gist.github.com/lincontroy/c17dfd0edc18c3f990b7dc2567076d1f.js"></script>
                    <hr>
                    <h3>Response code for a successfull payment</h3>
                    
                    <script src="https://gist.github.com/lincontroy/c40fac5546c5c9c9a0ceb6bd17e49081.js"></script>
                    
                    
                    <hr>
                    <p>
                        The above response is the most important response since it is sent by our server to your server.
                        
                        The endpoint registered on the merchant dashoard <a href="https://pay.lifegeegs.com/merchant/settings">here</a> should be able to capture this response and perform your business Logic.
                        <br>
                        
                        The Bill <b>"BillRefNumber":"fp#12"</b>, has two sections. This sections are divided with # . The fp is a shortform of your company which is system generated or we can set that for you, while the 12 is the customer identification. For instance customer number 12 or order number 12.
                    </p>
                    <hr>
                    <p>
                        If your callback url is something like 
                        <b>https://example.com/api/callback</b>
                        You should ensure that the url can receive http request and not limited to csrf tokens. This means that the url should be open to receive any form of json data
                        
                        <br><br>
                        
                        Below is how you receive this response in laravel
                        
                        <script src="https://gist.github.com/lincontroy/8b9911c6e77db4b19b518471f6e40e0c.js"></script>
                        <hr>
                        
                        <h4>
                            Below is how you receive this response in Node js
                        </h4>
                        
                        <script src="https://gist.github.com/lincontroy/5c7b72a9a4b95a0c9f68afdc08999483.js"></script>
                        
                        <p>We strongly recommend Logging the response / saving the response on txt for initial testing purposes</p>
                        <br>
                        <p>You should only ensure that the response is from our IP address and not from another ip address or domain for security measures.
                        
                        
                        </p>
                        
                        
                        
                        
                    </p>
                    
                    <h4>Checking account balance via API:</h4><br>
                     <div class="route-url-link">
                        <p>{{ url('/') }}/api/information</p>
                    </div>
                    <p>Below is how to query account balance via the api. Ensure that the private key and action are as the gist below. This is a post request.</p>
                    <script src="https://gist.github.com/lincontroy/4c2902e1b7dbef69fe91a8ebb868336b.js"></script>

                    


                    <h4>Success Response:</h4><br>

                    <script src="https://gist.github.com/lincontroy/bf1ac62e2a13d3086c2f0512e03aa769.js"></script>

                    <h4>Checking account Transaction statement via API:</h4><br>
                     <div class="route-url-link">
                        <p>{{ url('/') }}/api/information</p>
                    </div>
                    <p>Below is how to query account Statement via the api. Ensure that the private key and action are as the gist below. This is a Post request</p>
                    <script src="https://gist.github.com/lincontroy/57fd86662b4abf32c3835906672f370d.js"></script>

                    


                    <h4>Success Response:</h4><br>

                    <script src="https://gist.github.com/lincontroy/e0b7deb688758c678b31f5706ab12cb2.js"></script>

                   
                    
                </div>
            </div>
        </div>
    </div>
    <div class="next-page-link-area mt-100 mb-100">
        <div class="next-page-link f-right">
            <a href="{{ route('docs.thankyou') }}">{{ __('Thank You') }} <span class="iconify" data-icon="eva:arrow-ios-forward-outline" data-inline="false"></span></a>
        </div>
    </div>
</div>
@endsection