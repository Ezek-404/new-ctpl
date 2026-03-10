@extends('adminlte::auth.login')

@section('adminlte_css')
<style>
    /* Hides "I forgot my password" and "Register" links */
    p.mb-1, p.mb-0 {
        display: none !important;
    }
    
    /* Hides the default AdminLTE logo and brand text at the top */
    .login-logo, .register-logo {
        display: none !important;
    }

    /* Prevents the standard login box from adding unwanted top margin */
    .login-box {
        margin-top: 0 !important;
    }
</style>
@stop