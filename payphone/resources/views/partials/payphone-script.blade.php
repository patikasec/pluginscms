@if(setting('payphone_enabled') && setting('payphone_token') && setting('payphone_store_id'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script para manejar la redirección o modal de Payphone si es necesario
        console.log('Payphone payment gateway loaded');
    });
</script>
@endif
