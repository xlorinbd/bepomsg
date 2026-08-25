@extends('layouts/fullLayoutMaster')

@section('title', __('locale.labels.terms_of_use'))

@section('content')
    <div class="row auth-inner m-0">
        <div class="col-12 col-md-10 offset-md-1 col-lg-8 offset-lg-2">
            <div class="card mt-2">
                <div class="card-header border-bottom flex-column align-items-center">
                    <h2 class="card-title mb-1 text-primary fw-bolder">{{ __('locale.labels.terms_of_use') }}</h2>
                    <div class="btn-group btn-group-sm mt-1" role="group" aria-label="Language Switcher">
                        <button type="button" class="btn btn-outline-primary active" id="btn-en" onclick="switchLang('en')">Read in English</button>
                        <button type="button" class="btn btn-outline-primary" id="btn-bn" onclick="switchLang('bn')">বাংলায় পড়ুন</button>
                    </div>
                </div>
                <div class="card-body mt-2">
                    <div id="terms-en" class="terms-content animate__animated animate__fadeIn" style="line-height: 1.6;">
                        {!! $termsOfUseData !!}
                    </div>

                    <div id="terms-bn" class="terms-content animate__animated animate__fadeIn" style="display: none; line-height: 1.6; font-family: 'Noto Serif Bengali', serif !important;">
                        {!! $termsOfUseDataBN !!}
                    </div>

                    <div class="mt-3 text-center">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i data-feather="arrow-left"></i> {{ __('locale.buttons.back') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('page-style')
        <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Bengali:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            #terms-bn {
                font-family: 'Noto Serif Bengali', serif !important;
                line-height: 1.8 !important;
                text-rendering: optimizeLegibility;
                -webkit-font-smoothing: antialiased;
                font-variant-ligatures: common-ligatures;
            }
            #terms-bn p, #terms-bn span, #terms-bn li {
                font-family: 'Noto Serif Bengali', serif !important;
                line-height: 1.8 !important;
            }
        </style>
    @endpush

    <script>
        function switchLang(lang) {
            const enContent = document.getElementById('terms-en');
            const bnContent = document.getElementById('terms-bn');
            const btnEn = document.getElementById('btn-en');
            const btnBn = document.getElementById('btn-bn');

            if (lang === 'en') {
                enContent.style.display = 'block';
                bnContent.style.display = 'none';
                btnEn.classList.add('active');
                btnBn.classList.remove('active');
            } else {
                enContent.style.display = 'none';
                bnContent.style.display = 'block';
                btnEn.classList.remove('active');
                btnBn.classList.add('active');
            }
        }
    </script>
@endsection
