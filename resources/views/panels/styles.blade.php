<!-- BEGIN: Vendor CSS-->
@if (isset($configData['direction']) && $configData['direction'] === 'rtl')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/vendors-rtl.min.css')) }}"/>
@else
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/vendors.min.css')) }}"/>
@endif

@yield('vendor-style')
<!-- END: Vendor CSS-->

<!-- BEGIN: Theme CSS-->
<link rel="stylesheet" href="{{ asset(mix('css/core.css')) }}"/>
<link rel="stylesheet" href="{{ asset(mix('css/base/themes/dark-layout.css')) }}"/>
<link rel="stylesheet" href="{{ asset(mix('css/base/themes/bordered-layout.css')) }}"/>
<link rel="stylesheet" href="{{ asset(mix('css/base/themes/semi-dark-layout.css')) }}"/>
<link rel="stylesheet" href="{{ asset(mix('vendors/css/extensions/toastr.min.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('css/base/plugins/extensions/ext-component-toastr.css')) }}">

@php $configData = Helper::applClasses(); @endphp

<!-- BEGIN: Page CSS-->
@if ($configData['mainLayoutType'] === 'horizontal')
    <link rel="stylesheet" href="{{ asset(mix('css/base/core/menu/menu-types/horizontal-menu.css')) }}"/>
@else
    <link rel="stylesheet" href="{{ asset(mix('css/base/core/menu/menu-types/vertical-menu.css')) }}"/>
@endif

{{-- Page Styles --}}
@yield('page-style')

<!-- laravel style -->
<link rel="stylesheet" href="{{ asset(mix('css/overrides.css')) }}"/>

<!-- BEGIN: Custom CSS-->

@if (isset($configData['direction']) && $configData['direction'] === 'rtl')
    <link rel="stylesheet" href="{{ asset(mix('css-rtl/custom-rtl.css')) }}"/>
    <link rel="stylesheet" href="{{ asset(mix('css-rtl/style-rtl.css')) }}"/>

@else
    {{-- user custom styles --}}
    <link rel="stylesheet" href="{{ asset(mix('css/style.css')) }}"/>
@endif

@include('panels/_brand_dynamic_style')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Serif+Bengali:wght@300;400;500;600;700&display=swap');

    html[lang="bn"] body,
    html[lang="bn"] h1,
    html[lang="bn"] h2,
    html[lang="bn"] h3,
    html[lang="bn"] h4,
    html[lang="bn"] h5,
    html[lang="bn"] h6,
    html[lang="bn"] .h1,
    html[lang="bn"] .h2,
    html[lang="bn"] .h3,
    html[lang="bn"] .h4,
    html[lang="bn"] .h5,
    html[lang="bn"] .h6 {
        font-family: 'Noto Serif Bengali', serif !important;
    }
</style>
