@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush


@extends('layouts.admin')

@section('content')




<div class="card">
	<div class="d-sm-flex align-items-center justify-content-between py-3">
    <h5 class=" mb-0 text-gray-800 pl-3">{{ strtoupper($data->name) }}</h5>
        <ol class="breadcrumb py-0 m-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="javascript:;">{{ __('User') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.user.index') }}">{{ __('Users') }}</a></li>
        </ol>
	</div>
</div>


<div class="row mt-3">
  <div class="col-lg-12">
    @php
      $currency = defaultCurr();
    @endphp
	@include('includes.admin.form-success')

	<div class="card mb-4">
        <div class="row">
            <div class="col-md-2">
                <div class="user-image">
                    @if($data->is_provider == 1)
                    <img src="{{ $data->photo ? asset($data->photo):asset('assets/images/noimage.png')}}" alt="No Image">
                    @else
                    <img  class="" src="{{ $data->photo ? asset('assets/images/'.$data->photo):asset('assets/images/noimage.png')}}" alt="No Image">
                    @endif
                    <a  class="mybtn1 btn btn-primary"  data-email="{{ $data->email }}" data-toggle="modal" data-target="#vendorform" href="">{{__('Send Message')}}</a>

                </div>
            </div>
            <div class="col-md-5 mt-5">
                <div class="table-responsive show-table">
                    <table class="table">
                    <tr>
                      <th>{{__('ID#')}}</th>
                      <td>{{$data->id}}</td>
                    </tr>
                    <tr>
                      <th>{{__('Username')}}</th>
                      <td>{{$data->name}}</td>
                    </tr>
                    <tr>
                      <th>{{__('Email')}}</th>
                      <td>{{$data->email}}</td>
                    </tr>
                    <tr>
                      <th>{{__('Address')}}</th>
                      <td>{{$data->address}}</td>
                    </tr>

                    <tr>
                      <th>{{__('City')}}</th>
                      <td>{{$data->city}}</td>
                    </tr>

                    <tr>
                      <th>{{__('Zip Code')}}</th>
                      <td>{{$data->zip}}</td>
                    </tr>

                    <tr>
                      <th>{{__('Joined')}}</th>
                      <td>{{$data->created_at->diffForHumans()}}</td>
                    </tr>
                    <tr>
                      <th>{{__('KYC')}}</th>
                      <td>
                          @if($data->kyc_status == 0)
                              <span class="badge badge-warning">Pendiente</span>
                          @elseif($data->kyc_status == 1)
                              <span class="badge badge-success">Aprobado</span>
                          @elseif($data->kyc_status == 2)
                              <span class="badge badge-danger">Rechazado</span>
                          @endif
                      </td>
                  </tr>

                    </table>
                </div>
            </div>
            <div class="col-md-4 mx-auto mt-5">

            <div class="d-flex justify-content-center align-items-center gap-2 py-3">
                <h3 class="card-title mb-0 fw-bold">
                    <span class="text-muted fs-6">@lang('Available Balance')</span>
                    <div class="mt-1 text-primary fs-4">
                        {{ $data->balance.$currency->name }}
                    </div>
                </h3>
            </div>

            <form action="{{ route('admin.user.balance.add.deduct') }}" method="post">
                @csrf
                <div class="form-group">
                  <label for="inp-address">{{ __('Amount') }}</label>
                  <input type="number" class="form-control" id="inp-address" name="amount"  placeholder="{{ __('Enter Amount') }}" value="" min="0" step="0.01" required>
                </div>

                <input type="hidden" name="user_id" value="{{ $data->id }}">

                <div class="form-group">
                  <label for="exampleFormControlSelect1">@lang('Select Method')</label>
                  <select class="form-control" name="type" id="exampleFormControlSelect1" required>
                    <option value="add">@lang('add amount')</option>
                    <option value="subtract">@lang('subtract amount')</option>
                  </select>
                </div>
                <button type="submit" id="submit-btn" class="btn btn-primary w-100">{{ __('Submit') }}</button>
              </form>

              <!-- aqui selector estadoc redito -->
                <div class="form-group credit-status-container">
                    <label for="estado_credito" class="d-block mb-3">
                        <i class="fas fa-chart-line me-2"></i>
                        Estado del Crédito
                        <span class="selected-percentage">{{ $data->estado_credito }}%</span>
                    </label>
                    <div class="progress mb-3" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                            style="width: {{ $data->estado_credito }}%;"
                            aria-valuenow="{{ $data->estado_credito }}" 
                            aria-valuemin="0" 
                            aria-valuemax="100">
                        </div>
                    </div>
                    <select class="form-select form-control custom-select" 
                            name="estado_credito" 
                            id="estado_credito" 
                            required>
                        @for ($i = 0; $i <= 100; $i += 5)
                            <option value="{{ $i }}" {{ $data->estado_credito == $i ? 'selected' : '' }}>
                                {{ $i }}% {{ $i == 100 ? '- Completado' : '' }}
                            </option>
                        @endfor
                    </select>
                </div>
              <!-- fin selctor estado credito -->


            </div>
        </div>

	</div>
  </div>

</div>

<div class="row mb-3">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-uppercase mb-1">{{ __('LOAN') }}</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($loans) }}</div>
              </div>
              <div class="col-auto">
                <i class="fas fa-cash-register fa-2x text-danger"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-uppercase mb-1">{{ __('DPS') }}</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($dps) }}</div>
              </div>
              <div class="col-auto">
                <i class="fas fa-warehouse fa-2x text-success"></i>
              </div>
            </div>
          </div>
        </div>
      </div>


      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-uppercase mb-1">{{ __('FDR') }}</div>
                <div class="h6 mb-0 mt-2 font-weight-bold text-gray-800">{{ count($dps) }}</div>
              </div>
              <div class="col-auto">
                <i class="fas fa-user-shield fa-2x text-success"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-uppercase mb-1">{{ __('WITHDRAW') }}</div>
                <div class="h6 mb-0 mt-2 font-weight-bold text-gray-800">{{ count($withdraws) }}</div>
              </div>
              <div class="col-auto">
                <i class="fas fa-file-signature fa-2x text-danger"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

  </div>
<!--Row-->

{{-- STATUS MODAL --}}

<div class="modal fade confirm-modal" id="statusModal" tabindex="-1" role="dialog"
	aria-labelledby="statusModalTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
	<div class="modal-content">
		<div class="modal-header">
		<h5 class="modal-title">{{ __("Update Status") }}</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
		</div>
		<div class="modal-body">
			<p class="text-center">{{ __("You are about to change the status.") }}</p>
			<p class="text-center">{{ __("Do you want to proceed?") }}</p>
		</div>
		<div class="modal-footer">
		<a href="javascript:;" class="btn btn-secondary" data-dismiss="modal">{{ __("Cancel") }}</a>
		<a href="javascript:;" class="btn btn-success btn-ok">{{ __("Update") }}</a>
		</div>
	</div>
	</div>
</div>

{{-- STATUS MODAL ENDS --}}


{{-- MESSAGE MODAL --}}
<div class="sub-categori">
    <div class="modal fade confirm-modal" id="vendorform" tabindex="-1" role="dialog"
    aria-labelledby="vendorformLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title" id="vendorformLabel">{{ __("Send Message") }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body">
            <div class="container-fluid p-0">
                <div class="row">
                    <div class="col-md-12">
                        <div class="contact-form">
                            <form id="emailreply1">
                                {{csrf_field()}}

                                <div class="form-group">
                                    <input type="email" class="form-control" id="eml1" name="to"  placeholder="{{ __('Email') }}" value="{{ $data->email }}" required="">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="subj1" name="subject"  placeholder="{{ __('Subject') }}" value="" required="">
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" name="message" id="msg1" cols="20" rows="6" placeholder="{{ __('Your Message') }} "required=""></textarea>
                                </div>



                                <button class="submit-btn btn btn-primary text-center" id="emlsub1" type="submit">{{ __("Send Message") }}</button>
                            </form>
                        </div>
                    </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    {{-- MESSAGE MODAL ENDS --}}

{{-- DELETE MODAL --}}

<div class="modal fade confirm-modal" id="deleteModal" tabindex="-1" role="dialog"
aria-labelledby="deleteModalTitle" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">{{ __("Confirm Delete") }}</h5>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">
	<p class="text-center">{{__("You are about to delete this Blog.")}}</p>
	<p class="text-center">{{ __("Do you want to proceed?") }}</p>
</div>
<div class="modal-footer">
	<a href="javascript:;" class="btn btn-secondary" data-dismiss="modal">{{ __("Cancel") }}</a>
	<a href="javascript:;" class="btn btn-danger btn-ok">{{ __("Delete") }}</a>
</div>
</div>
</div>
</div>


<!-- Reemplaza el script existente por este -->
<script>
document.getElementById('estado_credito').addEventListener('change', function() {
    const newValue = this.value;
    const userId = {{ $data->id }};
    
    // Actualizar UI
    document.querySelector('.selected-percentage').textContent = newValue + '%';
    document.querySelector('.progress-bar').style.width = newValue + '%';
    
    // Enviar actualización a la base de datos
    fetch('{{ route("admin.user.credit.status") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            user_id: userId,
            estado_credito: newValue
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar notificación de éxito
            toastr.success('Estado del crédito actualizado exitosamente');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('Error al actualizar el estado del crédito');
    });
});
</script>

<!-- Asegúrate de que tienes toastr incluido en tu layout -->

<style>
.credit-status-container {
    background: #fff !important;
    padding: 2rem !important;
    border-radius: 15px !important;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08) !important;
    margin-top: 1.5rem !important;
    border: 2px solid #e5e9f2 !important;
    position: relative !important;
    overflow: hidden !important;
}

.credit-status-container::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 4px !important;
    background: linear-gradient(45deg, #2196F3, #1976D2) !important;
}

.credit-status-container label {
    font-size: 1.1rem !important;
    color: #344767 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    margin-bottom: 1.5rem !important;
}

.credit-status-container .fas {
    color: #2196F3 !important;
    font-size: 1.2rem !important;
}

.selected-percentage {
    background: #E3F2FD !important;
    padding: 0.5rem 1rem !important;
    border-radius: 25px !important;
    color: #2196F3 !important;
    font-weight: 600 !important;
    font-size: 0.9rem !important;
    box-shadow: 0 2px 8px rgba(33, 150, 243, 0.15) !important;
}

.progress {
    height: 12px !important;
    background-color: #f0f2f5 !important;
    border-radius: 10px !important;
    margin: 1.5rem 0 !important;
    overflow: hidden !important;
}

.progress-bar {
    background: linear-gradient(45deg, #2196F3, #1976D2) !important;
    transition: all 0.6s ease !important;
    box-shadow: 0 2px 5px rgba(33, 150, 243, 0.2) !important;
}

.custom-select {
    width: 100% !important;
    height: auto !important;
    border: 2px solid #e5e9f2 !important;
    padding: 1rem !important;
    border-radius: 10px !important;
    font-size: 1.1rem !important;
    color: #344767 !important;
    transition: all 0.3s ease !important;
    background-color: #fff !important;
    cursor: pointer !important;
    margin-top: 1rem !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23344767' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 1rem center !important;
    background-size: 1em !important;
    padding-right: 2.5rem !important;
}

.credit-status-container {
    padding: 2.5rem !important;
    margin-bottom: 2rem !important;
    /* ...resto de las propiedades existentes... */
}

option {
    font-size: 1.1rem !important;
    padding: 10px !important;
}

/* Ajustar el espaciado del label y el porcentaje */
.credit-status-container label {
    margin-bottom: 2rem !important;
    font-size: 1.2rem !important;
}

.selected-percentage {
    padding: 0.5rem 1.2rem !important;
    font-size: 1.1rem !important;
    min-width: 80px !important;
    text-align: center !important;
}

.custom-select:hover {
    border-color: #2196F3 !important;
    background-color: #f8fafc !important;
}

.custom-select:focus {
    border-color: #2196F3 !important;
    box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.15) !important;
    outline: none !important;
}
</style>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@endpush

{{-- DELETE MODAL ENDS --}}

@endsection



