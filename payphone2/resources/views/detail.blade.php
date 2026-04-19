@if ($payment)
    <div class="alert alert-success mt-3 d-block" role="alert">
        <p class="mb-2">{{ trans('plugins/payment::payment.payment_id') }}: <strong>{{ $payment['transactionId'] ?? $payment['id'] }}</strong></p>
        
        @if (!empty($payment['cardBrand']))
            <p class="mb-0 mt-2">
                {{ trans('plugins/payment::payment.card') }}: {{ $payment['cardBrand'] }} - {{ $payment['lastDigits'] ?? '' }}
            </p>
        @endif

        @if (!empty($payment['authorizationCode']))
            <p class="mb-0 mt-2">
                {{ trans('plugins/payphone2::payphone2.authorization_code') }}: <strong>{{ $payment['authorizationCode'] }}</strong>
            </p>
        @endif

        @if (!empty($payment['email']))
            <p class="mb-0 mt-2">
                {{ trans('plugins/payment::payment.email') }}: {{ $payment['email'] }}
            </p>
        @endif

        @if (!empty($payment['phoneNumber']))
            <p class="mb-0 mt-2">
                {{ trans('plugins/payphone2::payphone2.phone') }}: {{ $payment['phoneNumber'] }}
            </p>
        @endif

        @if (!empty($payment['document']))
            <p class="mb-0 mt-2">
                {{ trans('plugins/payphone2::payphone2.document') }}: {{ $payment['document'] }}
            </p>
        @endif

        @if (!empty($payment['date']))
            <p class="mb-0 mt-2">
                {{ trans('plugins/payment::payment.payment_date') }}: {{ BaseHelper::formatDate($payment['date']) }}
            </p>
        @endif

        @if (!empty($payment['reference']))
            <p class="mb-0 mt-2">
                {{ trans('plugins/payphone2::payphone2.reference') }}: {{ $payment['reference'] }}
            </p>
        @endif

        <p class="mb-0 mt-2">
            {{ trans('plugins/payment::payment.status') }}: 
            <span class="badge bg-success">{{ $payment['transactionStatus'] ?? 'Unknown' }}</span>
        </p>
    </div>
@endif
