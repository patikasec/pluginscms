@extends('plugins/payment::layouts.base')

@section('content')
    <div class="payment-payphone-wrapper">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">{{ $payment->name }}</h3>
            </div>
            <div class="panel-body">
                <p>{{ trans('plugins/payphone::payphone.redirecting_message') }}</p>
                
                @if(isset($data['checkout_url']))
                    <div id="payphone-payment-container" style="text-align: center; margin-top: 20px;">
                        <iframe 
                            id="payphone-iframe"
                            src="{{ $data['checkout_url'] }}" 
                            style="width: 100%; height: 600px; border: none; border-radius: 8px;"
                            allow="camera; microphone">
                        </iframe>
                    </div>
                    
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const iframe = document.getElementById('payphone-iframe');
                            
                            // Escuchar mensajes del iframe de Payphone
                            window.addEventListener('message', function(event) {
                                // Verificar origen del mensaje por seguridad
                                if (event.origin.includes('payphone')) {
                                    console.log('Mensaje recibido de Payphone:', event.data);
                                    
                                    // Si el pago fue completado, redirigir al callback
                                    if (event.data.status === 'approved' || event.data.status === 'success') {
                                        window.location.href = '{{ route('payments.payphone.callback') }}?status=' + event.data.status + '&id=' + (event.data.transaction_id || '') + '&external_order_id={{ $data['order_id'] }}';
                                    }
                                }
                            });
                        });
                    </script>
                @else
                    <div class="alert alert-warning">
                        {{ trans('plugins/payphone::payphone.payment_initiation_failed') }}
                    </div>
                    <a href="{{ route('public.checkout') }}" class="btn btn-primary">
                        {{ trans('plugins/payphone::payphone.return_to_checkout') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection
