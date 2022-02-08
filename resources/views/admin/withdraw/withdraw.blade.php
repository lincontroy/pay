@extends('layouts.backend.app')

@section('title','Withdrawal Lists')

@section('head')
    @include('layouts.backend.partials.headersection',['title'=>'Withdrawal List','button_name'=> 'Add New','button_link'=>
    route('admin.order.create')])
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
                <div class="col-sm-12">
                    <div class="float-right">
                      <form>            
                        <div class="input-group mb-2">
                          <input type="text" id="src" class="form-control" placeholder="Search..." required="" name="src" autocomplete="off" value="{{ $request->src ?? '' }}">
                          <select class="form-control selectric" name="type" id="type">
                            <option value="payment_id">{{ __('Search By Payment Id') }}</option>
                        </select>
                        <div class="input-group-append">                                            
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>       
            <div class="float-left mb-1">
               <a href="{{ url('/admin/order') }}" class="mr-2 btn btn-outline-primary "> {{ __('All') }}
              </a>
               <a href="{{ url('/admin/order?1') }}" class="mr-2 btn btn-outline-success "> {{ __('Accepted') }}
              </a>
               <a href="{{ url('/admin/order?0') }}" class="mr-2 btn btn-outline-danger "> {{ __('Rejected') }}
              </a>
               <a href="{{ url('/admin/order?3') }}" class="mr-2 btn btn-outline-warning "> {{ __('Pending') }}
               </a>
               <a href="{{ url('/admin/order?2') }}" class="mr-2 btn btn-outline-secondary "> {{ __('Expired') }}
               </a>

           </div>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table" id="table-2">
                        <thead>
                        <tr>
                            <th>{{ __('SL.') }}</th>
                            <th>{{ __('User') }}</th>
                            <th>{{ __('reference') }}</th>
                            <th>{{ __('Bank name') }}</th>
                            <th>{{ __('Bank Account') }}</th>
                            <th>{{ __(' Status') }}</th> 
                            <th>{{ __('Action') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($withdrawals as $withdrawal)

                            <tr>
                                <td>{{ $withdrawal->id }}</td>
                                <td><a href="{{ route('admin.merchant.show',$withdrawal->user_id) }}">
                                    
                                  
                                    
                                    <?php
                                    $users=App\Models\User::where('id',$withdrawal->user_id)->get();
                                    
                                    foreach($users as $user){
                                        echo $user->name;
                                    }
                                    ?>
                                    
                                    
                                    </a></td>
                                <td>{{ $withdrawal->reference }}</td>
                                <td>{{ $withdrawal->bankname }}</td>
                                <td>{{ $withdrawal->bankaccount }}</td>
                                <td>
                                    @php
                                    $pay_status = [
                                        0 => ['class' => 'badge-warning', 'text' => 'Pending'],
                                        1 => ['class' => 'badge-primary', 'text' => 'Processing'],
                                        2 => ['class' => 'badge-success', 'text' => 'Success'],
                                        3 => ['class' => 'badge-danger', 'text' => 'Canceled'],
                                    ][$withdrawal->status];
                                    @endphp
                                    <span class="badge {{ $pay_status['class'] }}">{{ $pay_status['text'] }}</span>
                                </td>
                            
                                <td>
                                    <button class="btn btn-primary dropdown-toggle" type="button"
                                            id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item has-icon"
                                            href="{{ route('admin.order.show', $withdrawal->id) }}"><i
                                                class="fa fa-eye"></i>{{ __('View') }}</a>
                                        <a class="dropdown-item has-icon"
                                            href="{{ route('admin.order.edit', $withdrawal->id) }}"><i
                                                class="fa fa-edit"></i>{{ __('Edit') }}</a>
                                        <a class="dropdown-item has-icon delete-confirm" href="javascript:void(0)"
                                            data-id={{ $withdrawal->id }}><i
                                                class="fa fa-trash"></i>{{ __('Delete') }}</a>
                                        <!-- Delete Form -->
                                        <form class="d-none" id="delete_form_{{ $withdrawal->id }}"
                                                action="{{ route('admin.order.destroy', $withdrawal->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
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

