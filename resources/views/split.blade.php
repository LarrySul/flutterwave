@extends('layouts.app')

@section('content')
<div class="container" id="loading">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">SPLIT AMOUNT</div>

                    <div class="card-body">
                        <form method="POST" action="">
                            @csrf

                            <div class="form-group row">
                                <label for="fullname" class="col-md-4 col-form-label text-md-right">{{ __('Fullname') }}</label>

                                <div class="col-md-6">
                                    <input id="fullname" type="text" class="form-control @error('fullname') is-invalid @enderror" name="fullname" value="{{ old('fullname') }}" required autocomplete="fullname" autofocus>

                                    @error('fullname')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="amount" class="col-md-4 col-form-label text-md-right">{{ __('Amount') }}</label>

                                <div class="col-md-6">
                                    <input id="amount" type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required autocomplete="amount" autofocus>

                                    @error('amoun')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="currency" class="col-md-4 col-form-label text-md-right">{{ __('Currency') }}</label>

                                <div class="col-md-6">
                                    <select id="currency" name="currency" class="form-control @error('currency') is-invalid @enderror" required>
                                        <option value="NGN">NGN</option>
                                    </select>
                                   
                                    @error('currency')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <input type="hidden" name="txref" value="rave-123456">
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-3 text-center ">
                                    <button type="button" id="submit_pay" class="btn btn-primary">
                                        Pay
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#submit_pay').click(function () {
                var email = $('#email').val();
                var amount = $('#amount').val();
                var currency = $('#currency').val();
                var public_key = '<?php echo getenv('FLUTTER_WAVE_API_KEY') ?>';
                var name = $('#fullname').val();
                var transaction_reference = 'rave-'+Date.now();
                $('#loading').css({
                    opacity : 0.2
                })
                $('a').css('pointer-events','none');
                //initial payment set with ajax
                handleInitialPayment(email, amount, currency, name, transaction_reference, public_key)
            })
        })

        //function to store initial payment
        function handleInitialPayment(email, amount, currency, name, transaction_reference, public_key) {
            $.ajax({
                cache: false,
                type: "POST",
                url: '/payment/process',
                dataType: 'json',
                data: {email : email, amount: amount, currency: currency, transaction_reference: transaction_reference,
                        name: name, "_token": "{{ csrf_token() }}"},
                beforeSend: function(data) {
                    // run toast showing progress
                    toastr_options = {
                        "progressBar": true,
                        // "showDuration": "300",
                        "preventDuplicates": true,
                        "tapToDismiss": false,
                        "hideDuration": "1",
                        "timeOut": "300000000"
                    };
                    msg = "Processing request, please wait"
                    toastr.info(msg, null, toastr_options)
                },
                success: function (data) {
                    toastr.clear();
                    if (data.status === 'success') {
                        payWithRave(email, amount, currency, public_key, transaction_reference);
                    } else {
                        toastr.error(data);
                        $('#loading').css({
                            opacity: 1
                        });
                        toastr.error('An error occured while processing your request')
                        $('a').css('pointer-events','');
                        return;
                    }
                },
                error : function (xhr) {
                    handleErrorException(xhr.status)
                }
            })
        }

        //process payment with flutter wave api
        function payWithRave(email, amount, currency, public_key, transaction_reference) {
            var x = getpaidSetup({
                PBFPubKey: public_key,
                customer_email: email,
                amount: parseInt(amount),
                currency: currency,
                txref: transaction_reference,
                subaccounts: [
                    {
                        id: '<?php echo getenv('SUB_ACCOUNT_ID') ?>',
                        transaction_split_ratio: '<?php echo getenv('RATION') ?>'
                    }
                ],
                meta: [{
                    metaname: "HotelRes",
                    metavalue: "HT1234"
                }],
                onclose: function () {
                },
                callback: function (response) {
                    var txref = response.tx.txRef; // collect flwRef returned and pass to a server page to complete status check.
                    //console.log("This is the response returned after a charge", response);
                    if (
                        response.tx.chargeResponseCode == "00" ||
                        response.tx.chargeResponseCode == "0"
                    ) {
                        // function to update user and handle redirection
                        updateTransactionSuccess(response);
                    } else {
                        // handle failure redirection.
                    }
                    x.close(); // use this to close the modal immediately after payment.
                }
            });
        }

        //update transaction
        function updateTransactionSuccess(response) {
            $.ajax({
                cache: false,
                type: "POST",
                url: '/payment/process/update/'+response.tx.txRef,
                dataType: 'json',
                data: {transaction_response : response, "_token": "{{ csrf_token() }}"},
                success: function (data) {
                    toastr.clear();
                    if (data.status === 'success') {
                        toastr.success('Transaction was successfull');
                        location.href = '/';
                    } else {
                        toastr.error(data.message);
                        $('#loading').css({
                            opacity: 1
                        });
                        $('a').css('pointer-events','');
                        return;
                    }
                },
                error : function (xhr) {
                    handleErrorException(xhr.status)
                }
            })
        }

        //error message on exception
        function handleErrorException(status) {
            toastr.clear();
            if(status === 401){
                toastr.error('Your session has expired, please log in ');
                $('#loading').css({
                    opacity: 1
                });
                $('a').css('pointer-events','');
                return;
            }else if(status === 500){
                console.log('here');
                toastr.error('An unknown error has occurred, please try again');
                $('#loading').css({
                    opacity: 1
                });
                $('a').css('pointer-events','');
                return;
            }else if(status === 503){
                toastr.error('The request took longer than expected, please try again');
                $('#loading').css({
                    opacity: 1
                });
                $('a').css('pointer-events','');
                return;
            }else{
                toastr.error('An unknown error has occurred, please try again');
                $('#loading').css({
                    opacity: 1
                });
                $('a').css('pointer-events','');
                return;
            }
        }
    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
@stop
