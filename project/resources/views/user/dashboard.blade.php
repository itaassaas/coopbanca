@extends('layouts.user')


@push('css')



<style>
.install-banner {
    background: linear-gradient(45deg, #2196F3, #1976D2);
    color: white;
    border: none;
    margin-bottom: 20px;
    padding: 15px;
}
.fa-share-square, .fa-plus-square, .fa-ellipsis-v {
    color: #FFD700;
}
.install-btn {
    background: rgba(255,255,255,0.2);
    border: 1px solid white;
    color: white;
    margin-left: 10px;
}
.install-btn:hover {
    background: rgba(255,255,255,0.3);
    color: white;
}
</style>
    
@endpush

@section('contents')

<!-- Add SweetAlert2 CDN in head section or before closing body -->
<link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- En el head de tu layout -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


    <div class="container-xl">

            <div class="alert alert-info install-banner alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center justify-content-between w-100" id="pwaPrompt">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-mobile-alt me-2"></i>
                        <span id="installInstructions">
                            <strong>¡Importante!</strong> Para acceder más rápido:
                        </span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>

            <script>
                // Detect iOS
                const isIos = () => {
                    return /iphone|ipad|ipod/.test(window.navigator.userAgent.toLowerCase());
                }

                // Detect if standalone
                const isInStandaloneMode = () => ('standalone' in window.navigator) && (window.navigator.standalone);

                document.addEventListener('DOMContentLoaded', (event) => {
                    const installInstructions = document.getElementById('installInstructions');
                    
                    if (isIos()) {
                        if (!isInStandaloneMode()) {
                            installInstructions.innerHTML = `
                                <strong>¡Importante!</strong> Para instalar en iPhone/iPad: 
                                Toca el ícono <i class="fas fa-share-square mx-1"></i> y luego 
                                "Añadir a Pantalla de Inicio" <i class="fas fa-plus-square mx-1"></i>
                            `;
                        }
                    } else {
                        // For Android
                        if (window.matchMedia('(display-mode: standalone)').matches) {
                            document.getElementById('pwaPrompt').style.display = 'none';
                        } else {
                            installInstructions.innerHTML = `
                                <strong>¡Importante!</strong> Para instalar en Android: 
                                Toca los tres puntos <i class="fas fa-ellipsis-v mx-1"></i> y luego 
                                "Añadir a Pantalla Principal" <i class="fas fa-plus-square mx-1"></i>
                            `;
                        }
                    }
                });
              </script>
      


      </div>
    <div class="page-header d-print-none">

    </div>
  </div>
  <div class="page-body">
    <div class="container-xl">

      @if (auth()->user()->kyc_status != 1)  
        <div class="row mb-3">
          <div class="col-md-12">
              <div class="card">
                  <div class="card-body">
                        <div class="form-group w-100 d-flex flex-wrap align-items-center justify-content-evenly justify-content-sm-between">
                          <h3 class="my-1 text-center text-sm-start">{{ __('You have a information to submit for kyc verification.') }}</h3>
                          <div class="my-1">
                            <a href="{{ route('user.kyc.form') }}" class="btn btn-warning">@lang('Submit')</a>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        </div>
      @endif




      <!-- Add this after the balance display and before the withdraw button -->

        @if($user->withdraws->where('motivo_rechazo', '!=', null)->count() > 0)
            <div class="alert alert-warning mt-3" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>
                        <strong>Novedades:</strong>
                        <div class="mt-2">
                            @foreach($user->withdraws->where('motivo_rechazo', '!=', null) as $withdraw)
                                <div class="border-start border-warning ps-3 mb-2">
                                    <small class="d-block text-muted">{{ $withdraw->created_at->format('d/m/Y H:i') }}</small>
                                    {{ $withdraw->motivo_rechazo }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

      <div class="row row-deck row-cards mb-2">

        <div class="col-sm-6 col-md-6">
          <div class="card mb-2">
            <div class="card-body p-3 p-md-4">
                <div class="balence--item">
                  <div class="icon">
                    <i class="fas fa-wallet"></i>
                  </div>
                  <div class="content">
                    <div class="subheader">{{__('Account Number')}}</div>

                      <div class="d-flex align-items-center">
                          <div class="h1 mb-0 mt-2">{{ $user->account_number }}</div>
                          <button class="btn btn-sm btn-link text-primary p-0 ml-2" 
                                  onclick="copyToClipboard('{{ $user->account_number }}', event)" 
                                  data-toggle="tooltip" 
                                  title="Copiar">
                              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                  <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                                  <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                              </svg>
                          </button>
                      </div>
                    
                    <div class="h1 mb-0 mt-2">
                      @if($user->kyc_status == 1)
                          <span class="badge badge-success">
                              <i class="fas fa-check-circle"></i> Perfil Verificado 
                          </span>
                      @elseif($user->kyc_status == 0)
                          <span class="badge badge-warning">
                              <i class="fas fa-clock"></i> Verificación KYC Pendiente
                          </span>
                      @else
                          <span class="badge badge-danger">
                              <i class="fas fa-times-circle"></i> Verificación KYC Rechazada
                          </span>
                      @endif
                    </div>


                  </div>
                </div>
              </div>
          </div>
        </div>

        <div class="col-sm-6 col-md-6">
          <div class="card mb-2">
              <div class="card-body p-3 p-md-4">
                <div class="balence--item">
                  <div class="icon">
                    <i class="fas fa-money-bill-wave"></i>
                  </div>
                  <div class="content">
                    <div class="subheader">{{__('Available Balance')}}</div>
                    <div class="h1 mb-0 mt-2">{{ showprice($user->balance,$currency) }}</div>
                    <button onclick="handleWithdraw()" class="btn btn-primary btn-lg w-100 mt-3 d-flex align-items-center justify-content-center" style="transition: all 0.3s ease; font-size: 0.9rem;">
                        <i class="fas fa-wallet me-2"></i>
                        Retirar Fondos
                    </button>

                    <style>
                    .btn-primary:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                    }
                    </style>
                  

                    <script>
                    function handleWithdraw() {
                        Swal.fire({
                            title: '¡Importante!',
                            text: 'Recuerda que para retirar tienes que tener el comprobante de pago en una foto o escaneado.\n\n¿Lo tienes?',
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonText: 'Sí',
                            cancelButtonText: 'No',
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'https://sucursalpersonacoopbanc.cloud/user/withdraw';
                            }
                            // If cancelled, do nothing and stay on current page
                        });
                    }
                    </script>
                 
                  </div>
                </div>
              </div>
          </div>
        </div>

      </div>

      <div class="row justify-content-center">
        <div class="col-sm-6 col-md-4 mb-3">
          <div class="card h-100 card--info-item">
            <div class="text-end icon">
              <i class="fas fa-money-check"></i>
            </div>
            <div class="card-body">
              <div class="h1 m-0">{{ count($user->deposits) }}</div>
              <div class="text-muted">@lang('Deposits')</div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-md-4 mb-3">
          <div class="card h-100 card--info-item">
            <div class="text-end icon">
              <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="card-body p-3 p-md-4">
              <div class="h1 m-0">{{ count($user->withdraws) }}</div>
              <div class="text-muted">@lang('Withdraws')</div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-md-4 mb-3">
          <div class="card h-100 card--info-item">
            <div class="text-end icon">
              <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="card-body">
              <div class="h1 m-0">{{ count($user->transactions) }}</div>
              <div class="text-muted">@lang('Transactions')</div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-md-4 mb-3">
          <div class="card h-100 card--info-item">
            <div class="text-end icon">
              <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="card-body">
              <div class="h1 m-0">{{ count($user->loans) }}</div>
              <div class="text-muted">@lang('Loan')</div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-md-4 mb-3">
          <div class="card h-100 card--info-item">
            <div class="text-end icon">
              <i class="fas fa-wallet"></i>
            </div>
            <div class="card-body">
              <div class="h1 m-0">{{ count($user->dps) }}</div>
              <div class="text-muted">@lang('DPS')</div>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-md-4 mb-3">
          <div class="card h-100 card--info-item">
            <div class="text-end icon">
              <i class="far fa-credit-card"></i>
            </div>
            <div class="card-body">
              <div class="h1 m-0">{{ count($user->fdr) }}</div>
              <div class="text-muted">@lang('FDR')</div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <p>{{ __('Your Referral Link') }}</p>
                        <div class="input-group input--group">
                            <input type="text" name="key" value="{{ url('/').'?reff='.$user->affilate_code}}" class="form-control" id="cronjobURL" readonly>
                            <button class="btn btn-sm copytext input-group-text" id="copyBoard" onclick="myFunction()"> <i class="fa fa-copy"></i> </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">@lang('Recent Transaction')</h3>
            </div>

            @if (count($transactions) == 0)
                <p class="text-center p-2">@lang('NO DATA FOUND')</p>
              @else 
              <div class="table-responsive">
                <table class="table card-table table-vcenter table-mobile-md text-nowrap datatable">
                  <thead>
                    <tr>
                      <th class="w-1">@lang('No'). 
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm text-dark icon-thick" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="6 15 12 9 18 15" /></svg>
                      </th>
                      <th>@lang('Type')</th>
                      <th>@lang('Txnid')</th>
                      <th>@lang('Amount')</th>
                      <th>@lang('Date')</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($transactions as $key=>$data)
                      <tr>
                        <td data-label="@lang('No')">
                          <div>
  
                            <span class="text-muted">{{ $loop->iteration }}</span>
                          </div>
                        </td>
  
                        <td data-label="@lang('Type')">
                          <div>
                            {{ $data->type }}
                          </div>
                        </td>
  
                        <td data-label="@lang('Txnid')">
                          <div>
                            {{ $data->txnid }}
                          </div>
                        </td>
  
                        <td data-label="@lang('Amount')">
                          <div>
                            <p class="m-0 text-{{ $data->profit == 'plus' ? 'success' : 'danger'}}">{{ showprice($data->amount,$currency) }}</p>
                          </div>
                        </td>
  
                        <td data-label="@lang('Date')">
                          <div>
                            {{date('d M Y',strtotime($data->created_at))}}
                          </div>
                        </td>
                        
                      </tr>
                    @endforeach
  
                  </tbody>
                </table>
              </div>
            @endif

          </div>
        </div>
      </div>

    </div>
  </div>
@endsection

@push('js')
    <script>
      'use strict';

      function myFunction() {
        var copyText = document.getElementById("cronjobURL");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        alert('copied');
    }
    </script>



<script>

function copyToClipboard(text, event) {
    // Configurar toastr una sola vez
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "2000",
        "preventDuplicates": true
    };
    
    // Limpiar toasts existentes
    toastr.clear();
    
    // Verificar si tenemos el evento y el botón
    const btn = event?.currentTarget || event?.target;
    if (!btn) {
        console.error('No se pudo encontrar el botón');
        return;
    }
    
    navigator.clipboard.writeText(text)
        .then(() => {
            toastr.success("Copiado al portapapeles");
            
            // Guardar el contenido original del botón
            const originalContent = btn.innerHTML;
            
            // Efecto del botón
            btn.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(() => {
                btn.innerHTML = originalContent;
            }, 1000);
        })
        .catch((err) => {
            toastr.error("Error al copiar");
            console.error(err);
        });
}


</script>


@endpush

