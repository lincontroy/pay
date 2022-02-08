@extends('layouts.backend.app')

@section('title', 'Withdrawals')

@section('head')
    @include('layouts.backend.partials.headersection',['title'=>'Withdrawals','button_name'=> 'Add New','button_link'=>
    url('merchant/withdraw')])
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if (Session::has('success'))
                        <div class="alert alert-success">
                            {{ Session::get('success') }}
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table" id="table-2">
                            <thead>
                                <tr>
                                   
                                   
                                    <th>{{ __('Reference') }}</th>
                                  
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Fee') }}</th>
                                   
                                    <th>{{ __('Bank name') }}</th>
                                    
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Created at') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($withdrawls as $withdrawl)
                                    
                                    <tr>
                                       
                                        <td>{{ $withdrawl->reference }}</td>
                                        <td>{{ $withdrawl->amount }}</td>
                                        <td>{{ $withdrawl->fee }}</td>
                                       
                                        <td>{{ $withdrawl->bankname }}</td>
                                      
                                        <td>
                                            @if ($withdrawl->status == 0)
                                                <span class="badge badge-warning">{{ __('Pending') }}</span>
                                                
                                            @endif
                                            
                                             @if ($withdrawl->status == 1)
                                                <span class="badge badge-warning">{{ __('Processing') }}</span>
                                                
                                            @endif
                                             @if ($withdrawl->status == 2)
                                                <span class="badge badge-success">{{ __('Completed') }}</span>
                                                
                                            @endif
                                            
                                             @if ($withdrawl->status == 3)
                                                <span class="badge badge-danger">{{ __('Cancelled') }}</span>
                                                
                                            @endif
                                           
                                          
                                        </td>
                                         <td>{{ $withdrawl->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        "use strict";

        function copy(id) {
            var copyText = document.getElementById(id);
            copyText.select();
            copyText.setSelectionRange(0, 99999)
            document.execCommand("copy");

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            Toast.fire({
                icon: 'success',
                title: 'Successfully Copied!'
            })
        }

    </script>
@endpush
