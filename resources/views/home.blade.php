@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">BVN VERIFICATION</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('verify') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="bvn" class="col-md-4 col-form-label text-md-right">{{ __('Bank Verification Number') }}</label>

                                <div class="col-md-6">
                                    <input id="bvn" type="number" class="form-control @error('bvn') is-invalid @enderror" name="bvn" value="{{ old('bvn') }}" required autocomplete="bvn" autofocus>

                                    @error('bvn')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-2 text-center">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Verify Bvn') }}
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
