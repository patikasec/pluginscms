<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ trans('plugins/payphone2::payphone2.instructions_title') }}</h5>
    </div>
    <div class="card-body">
        <h6>{{ trans('plugins/payphone2::payphone2.what_is_payphone') }}</h6>
        <p>{{ trans('plugins/payphone2::payphone2.what_is_payphone_description') }}</p>

        <h6 class="mt-4">{{ trans('plugins/payphone2::payphone2.setup_instructions') }}</h6>
        <ol>
            <li>{{ trans('plugins/payphone2::payphone2.step_1') }}</li>
            <li>{{ trans('plugins/payphone2::payphone2.step_2') }}</li>
            <li>{{ trans('plugins/payphone2::payphone2.step_3') }}</li>
            <li>{{ trans('plugins/payphone2::payphone2.step_4') }}</li>
            <li>{{ trans('plugins/payphone2::payphone2.step_5') }}</li>
        </ol>

        <div class="alert alert-info mt-3">
            <strong><i class="fa fa-info-circle"></i> {{ trans('plugins/payphone2::payphone2.important_note') }}:</strong>
            <p class="mb-0">{{ trans('plugins/payphone2::payphone2.important_note_description') }}</p>
        </div>

        <div class="alert alert-warning mt-3">
            <strong><i class="fa fa-exclamation-triangle"></i> {{ trans('plugins/payphone2::payphone2.confirmation_warning') }}:</strong>
            <p class="mb-0">{{ trans('plugins/payphone2::payphone2.confirmation_warning_description') }}</p>
        </div>
    </div>
</div>
