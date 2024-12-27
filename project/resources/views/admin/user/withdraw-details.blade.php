@extends('layouts.load')
@section('content')

                        <div class="content-area no-padding">
                            <div class="add-product-content">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="product-description">
                                            <div class="body-area" id="modalEdit">

                                            <div class="table-responsive show-table">
                                                <table class="table">
                                                    <tr>
                                                        <th>{{ __("User ID#") }}</th>
                                                        <td>{{$withdraw->user->id}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __("User Name") }}</th>
                                                        <td>
                                                            <a href="{{route('admin-user-show',$withdraw->user->id)}}" target="_blank">{{$withdraw->user->name}}</a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __("Withdraw Amount") }}</th>
                                                        <td>${{ round($withdraw->amount, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __("Withdraw Charge") }}</th>
                                                        <td>${{ round($withdraw->fee, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __("Withdraw Process Date") }}</th>
                                                        <td>{{date('d-M-Y',strtotime($withdraw->created_at))}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __("Withdraw Status") }}</th>
                                                        <td>{{ucfirst($withdraw->status)}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __("User Email") }}</th>
                                                        <td>{{$withdraw->user->email}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __("User Phone") }}</th>
                                                        <td>{{$withdraw->user->phone}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __("Withdraw Method") }}</th>
                                                        <td>{{$withdraw->method}}</td>
                                                    </tr>
                                                    <tr>
                                                    <th>{{ __("Comprobante") }}</th>
                                                        <td>
                                                            @if($withdraw->comporbante && Storage::disk('public')->exists($withdraw->comporbante))
                                                                <div class="comprobante-preview">
                                                                    <img src="{{ asset($withdraw->comporbante) }}" 
                                                                        alt="Comprobante de retiro" 
                                                                        class="img-fluid"
                                                                        style="max-width: 200px; cursor: pointer; transition: transform 0.3s;"
                                                                        onclick="window.open(this.src, '_blank')"
                                                                        onerror="this.onerror=null; this.src='{{ asset('assets/images/noimage.jpg') }}';">
                                                                </div>
                                                                <small class="text-muted d-block mt-1">Click para ampliar</small>
                                                            @else
                                                                <span class="text-muted">{{ __("No disponible") }}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __("Withdraw Account Details") }}</th>
                                                        <td>{{$withdraw->details}}</td>
                                                    </tr>
                                                </table>
                                            </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

@endsection
