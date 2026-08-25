@extends('layouts.contentLayoutMaster')

@section('title', 'API Documentation Settings')

@section('content')
    <section id="api-features">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Manage API Documentation Visibility</h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Enable or disable specific API sections in the developer documentation page. This affects all customers globally.</p>
                        <form action="{{ route('admin.settings.api_features.update') }}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Feature Name</th>
                                        <th>Slug</th>
                                        <th class="text-center">Status (ON/OFF)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($features as $feature)
                                        <tr>
                                            <td>{{ $feature->name }}</td>
                                            <td><code>{{ $feature->slug }}</code></td>
                                            <td class="text-center">
                                                <div class="form-check form-switch form-check-inline">
                                                    <input type="checkbox" class="form-check-input" 
                                                           name="features[{{ $feature->slug }}]" 
                                                           id="switch-{{ $feature->slug }}" 
                                                           {{ $feature->status ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="switch-{{ $feature->slug }}"></label>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2 text-end">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
