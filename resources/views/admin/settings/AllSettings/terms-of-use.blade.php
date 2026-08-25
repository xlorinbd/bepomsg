@extends('layouts/contentLayoutMaster')

@section('title', __('locale.labels.terms_of_use'))

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">
@endsection

@section('content')
    <section class="snow-editor">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h4 class="card-title">{{ __('locale.labels.terms_of_use') }}</h4>
                    </div>
                    <div class="card-body mt-2">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form class="form form-vertical" action="{{ route('admin.settings.terms-of-use') }}" method="post">
                            @csrf
                            <div class="row">

                                <div class="col-12">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="english-tab" data-bs-toggle="tab" href="#english"
                                                aria-controls="english" role="tab" aria-selected="true">English</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="bangla-tab" data-bs-toggle="tab" href="#bangla"
                                                aria-controls="bangla" role="tab" aria-selected="false">Bangla</a>
                                        </li>
                                    </ul>

                                    <div class="tab-content mt-2">
                                        <div class="tab-pane active" id="english" aria-labelledby="english-tab"
                                            role="tabpanel">
                                            <div class="mb-1">
                                                <label for="terms_of_use" class="form-label required">Terms of Use
                                                    (English)</label>
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
                                                                <button class="ql-clean"></button>
                                                            </span>
                                                        </div>
                                                        <div class="editor" id="english-editor" style="min-height: 200px;">
                                                            {!! $termsOfUseData !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <textarea name="terms_of_use" style="display:none" id="terms_of_use_hidden">{{ $termsOfUseData }}</textarea>
                                            </div>
                                        </div>

                                        <div class="tab-pane" id="bangla" aria-labelledby="bangla-tab" role="tabpanel">
                                            <div class="mb-1">
                                                <label for="terms_of_use_bn" class="form-label">Terms of Use (Bangla)</label>
                                                <div id="bangla-editor-wrapper">
                                                    <div id="bangla-editor-container">
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
                                                                <button class="ql-clean"></button>
                                                            </span>
                                                        </div>
                                                        <div class="editor" id="bangla-editor" style="min-height: 200px;">
                                                            {!! $termsOfUseDataBN !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <textarea name="terms_of_use_bn" style="display:none" id="terms_of_use_bn_hidden">{{ $termsOfUseDataBN }}</textarea>
                                            </div>
                                        </div>
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
    </section>
@endsection

@section('vendor-script')
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            var englishEditor = new Quill('#english-editor', {
                modules: {
                    toolbar: '#english-editor-container .quill-toolbar'
                },
                theme: 'snow'
            });

            var banglaEditor = new Quill('#bangla-editor', {
                modules: {
                    toolbar: '#bangla-editor-container .quill-toolbar'
                },
                theme: 'snow'
            });

            // Sync content on change and before submit
            const syncContent = () => {
                $('#terms_of_use_hidden').val(englishEditor.root.innerHTML);
                $('#terms_of_use_bn_hidden').val(banglaEditor.root.innerHTML);
            };

            englishEditor.on('text-change', syncContent);
            banglaEditor.on('text-change', syncContent);

            $('form').on('submit', function() {
                syncContent();
            });
        });
    </script>
@endsection