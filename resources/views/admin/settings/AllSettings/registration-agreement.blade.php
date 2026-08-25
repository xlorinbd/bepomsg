@extends('layouts/contentLayoutMaster')

@section('title', __('locale.labels.registration_agreement'))

@section('page-style')
    <style>
        .ql-editor img {
            max-width: 100%;
            height: auto;
        }
        #termsContent img, .modal-body img {
            max-width: 100%;
            height: auto;
        }
    </style>
@endsection

@section('content')

    <section class="snow-editor">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">{{ __('locale.labels.registration_agreement') }}</h4>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="card-content collapse show">
                        <div class="card-body">
                            <form class="form form-vertical" action="{{ route('admin.settings.registration-agreement') }}"
                                method="post">
                                @csrf
                                <div class="row">

                                    <div class="col-12">
                                        <div class="mb-1">
                                            <label class="form-label required">English Version</label>
                                            <div id="english-editor-wrapper">
                                                <div id="english-editor-container">
                                                    <div class="quill-toolbar">
                                                        <span class="ql-formats">
                                                          <select class="ql-header">
                                                            <option value="1">Heading</option>
                                                            <option value="2">Subheading</option>
                                                            <option selected>Normal</option>
                                                          </select>
                                                        </span>
                                                        <span class="ql-formats">
                                                          <button class="ql-bold"></button>
                                                          <button class="ql-italic"></button>
                                                          <button class="ql-underline"></button>
                                                        </span>
                                                        <span class="ql-formats">
                                                          <button class="ql-list" value="ordered"></button>
                                                          <button class="ql-list" value="bullet"></button>
                                                        </span>
                                                        <span class="ql-formats">
                                                          <button class="ql-link"></button>
                                                          <button class="ql-image"></button>
                                                        </span>
                                                        <span class="ql-formats">
                                                          <button class="ql-clean"></button>
                                                        </span>
                                                    </div>
                                                    <div class="editor">
                                                        {!! $registrationAgreementData !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <textarea name="registration_agreement" style="display:none" id="registration_agreement">{{ $registrationAgreementData }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-2">
                                        <div class="mb-1">
                                            <label class="form-label required">Bengali Version (বাংলা সংস্করণ)</label>
                                            <div id="bengali-editor-wrapper">
                                                <div id="bengali-editor-container">
                                                    <div class="quill-toolbar">
                                                        <span class="ql-formats">
                                                          <select class="ql-header">
                                                            <option value="1">Heading</option>
                                                            <option value="2">Subheading</option>
                                                            <option selected>Normal</option>
                                                          </select>
                                                        </span>
                                                        <span class="ql-formats">
                                                          <button class="ql-bold"></button>
                                                          <button class="ql-italic"></button>
                                                          <button class="ql-underline"></button>
                                                        </span>
                                                        <span class="ql-formats">
                                                          <button class="ql-list" value="ordered"></button>
                                                          <button class="ql-list" value="bullet"></button>
                                                        </span>
                                                        <span class="ql-formats">
                                                          <button class="ql-link"></button>
                                                          <button class="ql-image"></button>
                                                        </span>
                                                        <span class="ql-formats">
                                                          <button class="ql-clean"></button>
                                                        </span>
                                                    </div>
                                                    <div class="editor">
                                                        {!! $registrationAgreementDataBN !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <textarea name="registration_agreement_bn" style="display:none" id="registration_agreement_bn">{{ $registrationAgreementDataBN }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-2">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1"><i data-feather="save"></i>
                                            {{ __('locale.buttons.save') }}</button>
                                    </div>

                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/katex.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/monokai-sublime.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
@endsection

@section('page-script')
<script>
    $(document).ready(function() {
        var englishEditor = new Quill('#english-editor-container .editor', {
            bounds: '#english-editor-container .editor',
            modules: {
                formula: true,
                syntax: true,
                toolbar: '#english-editor-container .quill-toolbar'
            },
            theme: 'snow'
        });

        var bengaliEditor = new Quill('#bengali-editor-container .editor', {
            bounds: '#bengali-editor-container .editor',
            modules: {
                formula: true,
                syntax: true,
                toolbar: '#bengali-editor-container .quill-toolbar'
            },
            theme: 'snow'
        });

        // Sync on submit
        $('form').on('submit', function() {
            var enContent = englishEditor.root.innerHTML;
            var bnContent = bengaliEditor.root.innerHTML;

            // If editor is empty or just has a newline, send empty string or handle as empty
            if (englishEditor.getText().trim().length === 0) enContent = '';
            if (bengaliEditor.getText().trim().length === 0) bnContent = '';

            $('#registration_agreement').val(enContent);
            $('#registration_agreement_bn').val(bnContent);
        });

        // Optional: Sync on text-change for real-time feel
        englishEditor.on('text-change', function() {
            $('#registration_agreement').val(englishEditor.root.innerHTML);
        });

        bengaliEditor.on('text-change', function() {
            $('#registration_agreement_bn').val(bengaliEditor.root.innerHTML);
        });
    });
</script>
@endsection
